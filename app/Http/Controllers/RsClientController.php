<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

class RsClientController extends Controller
{
    public static function tokenrs()
    {
        $setting = Setting::where('nama', 'rsonline')->first();
        // dd($setting);
        session()->put('base_url', $setting->base_url);

        $client = new \GuzzleHttp\Client(['base_uri' => $setting->base_url]);
        $response = $client->request('POST', 'rslogin', [
            'json' => [
                'kode_rs' => $setting->satker,
                'password' => $setting->key,
            ]
        ]);

        $data = json_decode($response->getBody());

        // dd($data->data->access_token);

        if ($data->message == "access token created") {
            session()->put('tokenrs', $data->data->access_token);
        }
        // dd(session('tokenrs'));
    }

    public function geografi()
    {
        session()->put('ibu', 'RS Online');
        session()->put('anak', 'Data Referensi');
        session()->put('cucu', 'Geografi');

        $datakewarganegaraan = RsClientController::kewarganegaraan();
        $dataprovinsi = RsClientController::provinsi();
        $datakabkota = RsClientController::kabkota();
        $datakecamatan = RsClientController::kecamatan();
        $datakelurahan = RsClientController::kelurahan();

        // dd($datakecamatan);

        return view('data_rsonline.kewarganegaraan', compact('datakewarganegaraan', 'dataprovinsi', 'datakabkota', 'datakecamatan', 'datakelurahan'));
    }

    public function vaksin()
    {
        session()->put('ibu', 'RS Online');
        session()->put('anak', 'Data Referensi');
        session()->put('cucu', 'Status Vaksin');

        $datadosisvaksin = RsClientController::dosisvaksin();
        $datajenisvaksin = RsClientController::jenisvaksin();

        return view('data_rsonline.statusvaksin', compact('datadosisvaksin', 'datajenisvaksin'));
    }

    public function status()
    {
        session()->put('ibu', 'RS Online');
        session()->put('anak', 'Data Referensi');
        session()->put('cucu', 'Status Pasien');

        $dataasal = RsClientController::asalpasien();
        $datajenis = RsClientController::jenispasien();
        $datakelompok = RsClientController::kelompokgejala();
        $datapekerjaan = RsClientController::pekerjaan();
        $datastatus = RsClientController::statuspasien();
        $datapasiensaatmeninggal = RsClientController::statuspasiensaatmeninggal();
        $datakomorbid = RsClientController::komorbid();
        $datakomorbidcoinsiden = RsClientController::komorbidcoinsiden();
        $datastatusrawat = RsClientController::statusrawat();
        $dataterapi = RsClientController::terapi();
        $datakeluar = RsClientController::statuskeluar();
        $datapenyebabkematian = RsClientController::penyebabkematian();
        $datapenyebabkematianlangsung = RsClientController::penyebabkematianlangsung();
        $dataalatoksigen = RsClientController::alatoksigen();
        $datapemeriksaanlab = RsClientController::jenispemeriksaanlab();
        $datavarian = RsClientController::variancovid();

        // dd($datapemeriksaanlab, $datavarian);

        return view('data_rsonline.statuspasien', compact(
            'dataasal',
            'datajenis',
            'datakelompok',
            'datapekerjaan',
            'datastatus',
            'datapasiensaatmeninggal',
            'datakomorbid',
            'datakomorbidcoinsiden',
            'datastatusrawat',
            'dataterapi',
            'datakeluar',
            'datapenyebabkematian',
            'datapenyebabkematianlangsung',
            'dataalatoksigen',
            'datapemeriksaanlab',
            'datavarian'
        ));
    }

    public static function kewarganegaraan()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'kewarganegaraan', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function provinsi()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'provinsi', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function kabkota()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'kabkota', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function kecamatan()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'kecamatan', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function kelurahan()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'kelurahan', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function dosisvaksin()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'dosisvaksin', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function jenisvaksin()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'jenisvaksin', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function asalpasien()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'asalpasien', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function jenispasien()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'jenispasien', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function kelompokgejala()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'kelompokgejala', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function pekerjaan()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'pekerjaan', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function statuspasien()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'statuspasien', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function statuspasiensaatmeninggal()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'statuspasiensaatmeninggal', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function komorbid()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'komorbid', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function komorbidcoinsiden()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'komorbidcoinsiden', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function statusrawat()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'statusrawat', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function terapi()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'terapi', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function statuskeluar()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'statuskeluar', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function penyebabkematian()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'penyebabkematian', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function penyebabkematianlangsung()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'penyebabkematianlangsung', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function alatoksigen()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'alatoksigen', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function jenispemeriksaanlab()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'jenispemeriksaanlab', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }

    public static function variancovid()
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'variancovid', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);

        return $data;
    }
}
