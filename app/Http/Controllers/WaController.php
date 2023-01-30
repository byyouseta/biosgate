<?php

namespace App\Http\Controllers;

use App\Setting;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'Pesan');
        session()->put('anak', 'Setting');
        session()->forget('cucu');

        $status = WaController::cekStatus();
        // dd($status);

        if ($status[2] == 'online') {
            $sessionApp = WaController::cekSession();
        } else {
            $sessionApp = null;
        }
        // dd($connection, $status, $getSession);

        return view('pesan.setting_pesan', compact(
            'status',
            'sessionApp'
        ));
    }

    public function createSession()
    {
        $setting = Setting::where('nama', 'pesan')->first();

        $sessionApp = WaController::cekSession();
        $status = WaController::cekStatus();

        // dd($sessionApp, $status);

        if ($sessionApp == true) {
            // WaController::deleteSession();

            return redirect('/pesan');
        }
        $client = new \GuzzleHttp\Client((['base_uri' => $setting->base_url]));
        try {
            $response = $client->request('POST', '/sessions/add', [
                'form_params' => [
                    'id' => $setting->satker,
                    'isLegacy' => 'false',
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                // $test = json_decode($response->getBody());
                // dd($test);

                // $message = "Medication 1 error $test";

            }
        }

        $dataSession = json_decode($response->getBody());
        $qrCode = $dataSession->data;

        Session::flash('sukses', $dataSession->message);

        // dd($status);

        // dd($dataSession, $qrCode);

        return view('pesan.setting_pesan', compact(
            'qrCode',
            'status',
            'sessionApp'
        ));
    }

    public function cekSession()
    {
        $setting = Setting::where('nama', 'pesan')->first();

        $client = new \GuzzleHttp\Client((['base_uri' => $setting->base_url]));
        try {
            $response = $client->request('GET', "/sessions/status/$setting->satker");
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());
                // dd($test);

                // $message = "Medication 1 error $test";

                Session::flash('error', $test->message);
            }
        }

        $data = json_decode($response->getBody());

        // dd($data, $setting);
        if ($data->message == 'Session not found.') {
            return false;
        } else {
            return true;
        }
    }

    public function cekStatus()
    {
        $data = Setting::where('nama', 'pesan')->first();
        //inisiasi data awal
        $ipServer = null;
        $portServer = null;
        $status = 'offline';

        if (!empty($data)) {
            $pecah = explode(':', $data->base_url);

            $ipServer = $pecah[0];
            $portServer = $pecah[1];

            // dd($ipServer, $portServer);


            // foreach ($ports as $port) {
            $connection = @fsockopen($ipServer, $portServer);

            if (is_resource($connection)) {
                // echo '<h2>' . $host . ':' . $port . ' ' . '(' . getservbyport($port, 'tcp') . ') is open.</h2>' . "\n";

                fclose($connection);

                $status = 'online';
            }
            // else {
            //     echo '<h2>' . $host . ':' . $port . ' is not responding.</h2>' . "\n";
            // }
            // }
        }

        return array($ipServer, $portServer, $status);
    }

    public function deleteSession()
    {
        $setting = Setting::where('nama', 'pesan')->first();

        $client = new \GuzzleHttp\Client((['base_uri' => $setting->base_url]));
        try {
            $response = $client->request('DELETE', "/sessions/delete/$setting->satker");
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());
                // dd($test);

                // $message = "Medication 1 error $test";

                Session::flash('error', $test->message);
            }
        }

        $data = json_decode($response->getBody());

        // dd($data);
        return redirect()->back();
    }

    public function kirimPesan()
    {
        session()->put('ibu', 'Pesan');
        session()->put('anak', 'Kirim Pesan');
        session()->forget('cucu');

        return view('pesan.kirim');
    }

    public function kirim(Request $request)
    {
        $request->validate([
            'penerima' => 'required|starts_with:62'
        ], [
            'penerima.starts_with' => 'Format nomor yang digunakan salah'
        ]);

        $status = WaController::cekStatus();
        // dd($status);

        if ($status[2] == 'online') {
            $sessionApp = WaController::cekSession();
        } else {
            $message = "Server tidak bisa dijangkau!";

            Session::flash('error', $message);

            return redirect()->back();
        }

        // $sessionApp = WaController::cekSession();
        // dd($sessionApp);
        if ($sessionApp == true) {
            $setting = Setting::where('nama', 'pesan')->first();

            $client = new \GuzzleHttp\Client((['base_uri' => $setting->base_url]));
            try {
                $response = $client->request('POST', "/chats/send?id=$setting->satker", [
                    'json' => [
                        "receiver" => "$request->penerima",
                        "message" => [
                            "text" => "$request->pesan"
                        ]
                    ]
                ]);
            } catch (BadResponseException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $test = json_decode($response->getBody());
                    // dd($test);

                    // $message = "Medication 1 error $test";

                    Session::flash('error', $test->message);
                }
            }

            $data = json_decode($response->getBody());

            // dd($data);

            Session::flash('sukses', $data->message);

            return redirect()->back();
        } else {
            $message = "Session id belum tersetting";

            Session::flash('error', $message);

            return redirect()->back();
        }
    }
}
