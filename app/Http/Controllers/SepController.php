<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;

class SepController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function getSep($id)
    {
        $noSep = $id;
        $consid = env('CONS_ID');
        $secretKey = env('SECRETKEY');
        $userKey = env('USERKEY');
        // Computes the timestamp
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        // Computes the signature by hashing the salt with the secret key as the key
        $signature = hash_hmac('sha256', $consid . "&" . $tStamp, $secretKey, true);

        // base64 encode�
        $encodedSignature = base64_encode($signature);

        // urlencode�
        // $encodedSignature = urlencode($encodedSignature);

        // dd($consid, $tStamp, $encodedSignature, $signature);

        $client = new \GuzzleHttp\Client(['base_uri' => 'https://apijkn.bpjs-kesehatan.go.id/vclaim-rest/']);

        try {
            $response = $client->request("GET", "SEP/$noSep", [
                'headers' => [
                    'X-cons-id' => $consid,
                    'X-timestamp' => $tStamp,
                    'X-signature' => $encodedSignature,
                    'user_key' => $userKey,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                dd($test);
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        $kunci = $consid . $secretKey . $tStamp;
        $nilairespon = $dataResponse->response;
        $hasilakhir = SepController::decompress(SepController::stringDecrypt($kunci, $nilairespon));
        $objek = json_decode($hasilakhir);

        // dd($hasilakhir, $objek);

        return $objek;
    }

    public static function peserta($nokartu, $tgl)
    {
        // $noSep = $id;
        $consid = env('CONS_ID');
        $secretKey = env('SECRETKEY');
        $userKey = env('USERKEY');
        // Computes the timestamp
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        // Computes the signature by hashing the salt with the secret key as the key
        $signature = hash_hmac('sha256', $consid . "&" . $tStamp, $secretKey, true);

        // base64 encode�
        $encodedSignature = base64_encode($signature);

        // urlencode�
        // $encodedSignature = urlencode($encodedSignature);

        // dd($consid, $tStamp, $encodedSignature, $signature);

        $client = new \GuzzleHttp\Client(['base_uri' => 'https://apijkn.bpjs-kesehatan.go.id/vclaim-rest/']);

        try {
            $response = $client->request("GET", "Peserta/nokartu/$nokartu/tglSEP/$tgl", [
                'headers' => [
                    'X-cons-id' => $consid,
                    'X-timestamp' => $tStamp,
                    'X-signature' => $encodedSignature,
                    'user_key' => $userKey,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                dd($test);
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        $kunci = $consid . $secretKey . $tStamp;
        $nilairespon = $dataResponse->response;
        $hasilakhir = SepController::decompress(SepController::stringDecrypt($kunci, $nilairespon));
        $objek = json_decode($hasilakhir);

        // dd($objek->peserta->mr);

        return $objek->peserta;
    }

    public static function getSep2($noSep)
    {
        // $noSep = $id;
        $consid = env('CONS_ID');
        $secretKey = env('SECRETKEY');
        $userKey = env('USERKEY');
        // Computes the timestamp
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        // Computes the signature by hashing the salt with the secret key as the key
        $signature = hash_hmac('sha256', $consid . "&" . $tStamp, $secretKey, true);

        // base64 encode�
        $encodedSignature = base64_encode($signature);

        // urlencode�
        // $encodedSignature = urlencode($encodedSignature);

        // dd($consid, $tStamp, $encodedSignature, $signature);

        $client = new \GuzzleHttp\Client(['base_uri' => 'https://apijkn.bpjs-kesehatan.go.id/vclaim-rest/']);

        try {
            $response = $client->request("GET", "RencanaKontrol/nosep/$noSep", [
                'headers' => [
                    'X-cons-id' => $consid,
                    'X-timestamp' => $tStamp,
                    'X-signature' => $encodedSignature,
                    'user_key' => $userKey,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                dd($test);
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        $kunci = $consid . $secretKey . $tStamp;
        $nilairespon = $dataResponse->response;
        $hasilakhir = SepController::decompress(SepController::stringDecrypt($kunci, $nilairespon));
        $objek = json_decode($hasilakhir);

        // dd($objek);

        return $objek;
    }

    public static function getJmlSep()
    {
        // $noSep = $id;
        // dd($noRujukan);
        $consid = env('CONS_ID');
        $secretKey = env('SECRETKEY');
        $userKey = env('USERKEY');
        // Computes the timestamp
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        // Computes the signature by hashing the salt with the secret key as the key
        $signature = hash_hmac('sha256', $consid . "&" . $tStamp, $secretKey, true);

        // base64 encode�
        $encodedSignature = base64_encode($signature);

        // urlencode�
        // $encodedSignature = urlencode($encodedSignature);

        // dd($consid, $tStamp, $encodedSignature, $signature);

        $client = new \GuzzleHttp\Client(['base_uri' => 'https://apijkn.bpjs-kesehatan.go.id/vclaim-rest/']);

        try {
            $response = $client->request("GET", "Rujukan/JumlahSEP/1/112401010823Y004798", [
                'headers' => [
                    'X-cons-id' => $consid,
                    'X-timestamp' => $tStamp,
                    'X-signature' => $encodedSignature,
                    'user_key' => $userKey,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                dd($test);
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        $kunci = $consid . $secretKey . $tStamp;
        $nilairespon = $dataResponse->response;
        $hasilakhir = SepController::decompress(SepController::stringDecrypt($kunci, $nilairespon));
        $objek = json_decode($hasilakhir);

        dd($objek);

        return $objek;
    }

    public static function coba()
    {
        // $noSep = ;
        // dd($noRujukan);
        $consid = env('CONS_ID');
        $secretKey = env('SECRETKEY');
        $userKey = env('USERKEY');
        // Computes the timestamp
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        // Computes the signature by hashing the salt with the secret key as the key
        $signature = hash_hmac('sha256', $consid . "&" . $tStamp, $secretKey, true);

        // base64 encode�
        $encodedSignature = base64_encode($signature);

        // urlencode�
        // $encodedSignature = urlencode($encodedSignature);

        // dd($consid, $tStamp, $encodedSignature, $signature);

        $client = new \GuzzleHttp\Client(['base_uri' => 'https://apijkn-dev.bpjs-kesehatan.go.id/antreanrs/']);

        try {
            $response = $client->request("GET", "dashboard/waktutunggu/bulan/05/tahun/2023/waktu/rs", [
                'headers' => [
                    'X-cons-id' => $consid,
                    'X-timestamp' => $tStamp,
                    'X-signature' => $encodedSignature,
                    'user_key' => $userKey,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                dd($test);
            }
        }

        $dataResponse = json_decode($response->getBody());

        dd($dataResponse);

        $kunci = $consid . $secretKey . $tStamp;
        $nilairespon = $dataResponse->response;
        $hasilakhir = SepController::decompress(SepController::stringDecrypt($kunci, $nilairespon));
        $objek = json_decode($hasilakhir);

        dd($objek);

        return $objek;
    }

    public static function stringDecrypt($key, $string)
    {
        $encrypt_method = 'AES-256-CBC';

        // hash
        $key_hash = hex2bin(hash('sha256', $key));

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);

        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);

        return $output;
    }

    public static function decompress($string)
    {
        return \LZCompressor\LZString::decompressFromEncodedURIComponent($string);
    }
}
