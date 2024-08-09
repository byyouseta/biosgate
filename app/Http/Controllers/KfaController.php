<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class KfaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function cari(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'API KFA');
        session()->forget('cucu');
        set_time_limit(0);


        $cari = $request->get('cari');
        // dd($cari);

        if (!empty($cari)) {
            $this->validate($request, [
                'cari' => 'required|string|min:3',
            ]);

            $idRS = env('IDRS');
            //Send data
            SatuSehatController::getTokenSehat();
            $access_token = Session::get('tokenSatuSehat');
            // dd('kososng');
            $client = new \GuzzleHttp\Client(['base_uri' => env('URL_APIKFA')]);
            try {
                $response = $client->request('GET', "/kfa-v2/products/all?page=1&size=100&product_type=farmasi&keyword=$cari", [
                    'headers' => [
                        'Authorization' => "Bearer {$access_token}"
                    ]
                ]);
            } catch (ClientException $e) {
                // echo $e->getRequest();
                // echo $e->getResponse();
                if ($e->hasResponse()) {
                    $response = $e->getResponse();

                    // dd($response);
                    $test = json_decode($response->getBody());
                    dd($test);
                }

                $message = "Gagal melakukan pencarian " . $cari;

                Session::flash('error', $message);

                // goto KirimPasienLain;
            }

            $dataResponse = json_decode($response->getBody());

            if ($dataResponse->total > 0) {
                // dd((object)$dataResponse->items->data);
                $data = (object)$dataResponse->items->data;

                Session::flash('sukses', "Data ditemukan.");
            } else {
                $data = null;

                Session::flash('sukses', "Data tidak ditemukan.");
            }
        } else {
            $data = null;
            // dd($cari);
            // Session::flash('error', 'Data pencarian tidak boleh kosong');
        }

        return view('satu_sehat.kfa_browser', compact('data'));
    }
}
