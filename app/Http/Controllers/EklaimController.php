<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EklaimController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //Encryption Function
    public static function inacbg_encrypt($data, $key)
    {

        /// make binary representasion of $key
        $key = hex2bin($key);
        // dd($key);
        /// check key length, must be 256 bit or 32 bytes
        if (mb_strlen($key, "8bit") !== 32) {
            throw new \Exception("Needs a 256-bit key!");
        }
        /// create initialization vector
        $iv_size = openssl_cipher_iv_length("aes-256-cbc");
        $iv = openssl_random_pseudo_bytes($iv_size); // dengan catatan dibawah
        /// encrypt
        $encrypted = openssl_encrypt(
            $data,
            "aes-256-cbc",
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
        /// create signature, against padding oracle attacks
        $signature = mb_substr(hash_hmac(
            "sha256",
            $encrypted,
            $key,
            true
        ), 0, 10, "8bit");
        /// combine all, encode, and format
        $encoded = chunk_split(base64_encode($signature . $iv . $encrypted));
        // dd($encoded);
        return $encoded;
    }

    // Decryption Function
    public static function inacbg_decrypt($str, $strkey)
    {
        /// make binary representation of $key
        $key = hex2bin($strkey);
        /// check key length, must be 256 bit or 32 bytes
        if (mb_strlen($key, "8bit") !== 32) {
            throw new \Exception("Needs a 256-bit key!");
        }
        /// calculate iv size
        $iv_size = openssl_cipher_iv_length("aes-256-cbc");
        /// breakdown parts
        $decoded = base64_decode($str);
        $signature = mb_substr($decoded, 0, 10, "8bit");
        $iv = mb_substr($decoded, 10, $iv_size, "8bit");
        $encrypted = mb_substr($decoded, $iv_size + 10, NULL, "8bit");
        /// check signature, against padding oracle attack
        $calc_signature = mb_substr(hash_hmac(
            "sha256",
            $encrypted,
            $key,
            true
        ), 0, 10, "8bit");
        if (!EklaimController::inacbg_compare($signature, $calc_signature)) {
            return "SIGNATURE_NOT_MATCH"; /// signature doesn't match
        }
        $decrypted = openssl_decrypt(
            $encrypted,
            "aes-256-cbc",
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
        return $decrypted;
    }

    // Compare Function
    public static function inacbg_compare($a, $b)
    {
        /// compare individually to prevent timing attacks

        /// compare length
        if (strlen($a) !== strlen($b)) return false;

        /// compare individual
        $result = 0;
        for ($i = 0; $i < strlen($a); $i++) {
            $result |= ord($a[$i]) ^ ord($b[$i]);
        }

        return $result == 0;
    }

    public function getStatus($sep)
    {
        // contoh encryption key, bukan aktual
        $key = env('KEY_EKLAIM');
        // dd($key);
        // $sep = '0171R0230124V000487';
        // json query
        $json_request =
            [
                "metadata" => [
                    "method" => "claim_print"
                ],
                "data" => [
                    "nomor_sep" => "$sep"
                ]
            ];
        // $json_request =
        //     [
        //         "metadata" => [
        //             "method" => "pull_claim"
        //         ],
        //         "data" => [
        //             "start_dt" => "2023-01-07",
        //             "stop_dt" => "2023-01-07",
        //             "jenis_rawat" => "1"
        //         ]
        //     ];

        // $json_klaim =
        //     [
        //         "metadata" => [
        //             "method" => "get_claim_data"
        //         ],
        //         "data" => [
        //             "nomor_sep" => "0171R0230124V000487"
        //         ]
        //     ];


        // membuat json juga dapat menggunakan json_encode:
        // $ws_query["metadata"]["method"] = "claim_print";
        // $ws_query["data"]["nomor_sep"] = "16120507422";
        $json_request = json_encode($json_request);
        // data yang akan dikirimkan dengan method POST adalah encrypted:
        $payload = EklaimController::inacbg_encrypt($json_request, $key);
        // tentukan Content-Type pada http header
        $header = array("Content-Type: application/x-www-form-urlencoded");
        // url server aplikasi E-Klaim,
        // silakan disesuaikan instalasi masing-masing
        $url = env('URL_EKALIM');
        // setup curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        // request dengan curl
        $response = curl_exec($ch);
        // terlebih dahulu hilangkan "----BEGIN ENCRYPTED DATA----\r\n"
        // dan hilangkan "----END ENCRYPTED DATA----\r\n" dari response
        $first = strpos($response, "\n") + 1;
        $last = strrpos($response, "\n") - 1;
        $response = substr(
            $response,
            $first,
            strlen($response) - $first - $last
        );
        // decrypt dengan fungsi inacbg_decrypt
        $response = EklaimController::inacbg_decrypt($response, $key);
        // hasil decrypt adalah format json, ditranslate kedalam array
        $msg = json_decode($response, true);
        // variable data adalah base64 dari file pdf

        // dd($msg['response']['data']);
        // dd($msg['metadata']['message']);
        if (!empty($msg["data"])) {
            $pdf = base64_decode($msg["data"]);
            // hasilnya adalah berupa binary string $pdf, untuk disimpan:
            file_put_contents("klaim.pdf", $pdf);
            // dd($pdf);

            // atau untuk ditampilkan dengan perintah:
            header("Content-type:application/pdf");
            header("Content-Disposition:inline;filename=sep_$sep.pdf");

            // Tested and works fine. If you want the file to download instead, replace
            //     Content-Disposition: inline
            // with
            //     Content-Disposition: attachment

            echo $pdf;
        } else {
            Session::flash('error', $msg['metadata']['message']);
            // return redirect()->back();
            echo "<script>window.close();</script>";
        }
    }

    public static function getDetail($sep)
    {

        // contoh encryption key, bukan aktual
        $key = env('KEY_EKLAIM');
        // dd($key, 'kene');
        // $sep = '0171R0230324V000017';
        // json query
        $json_klaim =
            [
                "metadata" => [
                    "method" => "get_claim_data"
                    // "method" => "get_claim_status"
                ],
                "data" => [
                    "nomor_sep" => "$sep"
                ]
            ];

        $json_request = json_encode($json_klaim);
        // data yang akan dikirimkan dengan method POST adalah encrypted:
        $payload = EklaimController::inacbg_encrypt($json_request, $key);
        // tentukan Content-Type pada http header
        $header = array("Content-Type: application/x-www-form-urlencoded");
        // url server aplikasi E-Klaim,
        // silakan disesuaikan instalasi masing-masing
        $url = env('URL_EKALIM');
        // setup curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        // request dengan curl
        $response = curl_exec($ch);
        // terlebih dahulu hilangkan "----BEGIN ENCRYPTED DATA----\r\n"
        // dan hilangkan "----END ENCRYPTED DATA----\r\n" dari response
        $first = strpos($response, "\n") + 1;
        $last = strrpos($response, "\n") - 1;
        $response = substr(
            $response,
            $first,
            strlen($response) - $first - $last
        );
        // decrypt dengan fungsi inacbg_decrypt
        $response = EklaimController::inacbg_decrypt($response, $key);
        // hasil decrypt adalah format json, ditranslate kedalam array
        $msg = json_decode($response, true);
        // variable data adalah base64 dari file pdf

        // dd($msg);
        // dd($msg['response']['data']);
        // dd($msg['metadata']['code']);
        if (!empty($msg['response']['data'])) {
            return $msg['response']['data'];
        } else {
            return null;
        }
    }
}
