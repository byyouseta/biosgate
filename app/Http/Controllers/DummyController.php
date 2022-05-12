<?php

namespace App\Http\Controllers;

use App\InstalasiKanker;
use App\KabKota;
use App\Kecamatan;
use App\Kelurahan;
use App\Provinsi;
use App\SubinstalasiKanker;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DummyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'Master Data');
        session()->put('anak', 'Dummy Geografi');
        session()->forget('cucu');

        return view('masters.dummy_geo');
    }

    public function provinsi()
    {
        $dataprovinsi = RsClientController::provinsi();
        // $datakabkota = RsClientController::kabkota();
        // $datakecamatan = RsClientController::kecamatan();
        // $datakelurahan = RsClientController::kelurahan();

        foreach ($dataprovinsi as $data) {
            $cek = Provinsi::where('id', $data->id)->get();
            if ($cek->count() == 0) {
                $baru = new Provinsi();
                $baru->id = $data->id;
                $baru->nama = $data->nama;
                $baru->save();

                Session::flash('sukses', 'Data berhasil ditambahkan!');
            } elseif ($cek->count() > 0) {
                $update = Provinsi::find($data->id);
                $update->id = $data->id;
                $update->nama = $data->nama;
                $update->save();

                Session::flash('sukses', 'Data berhasil diupdate!');
            }
        }

        // $new = Provinsi::all();

        return redirect('/master/dummygeo');
    }

    public function kabkota()
    {
        // $dataprovinsi = RsClientController::provinsi();
        $datakabkota = RsClientController::kabkota();
        // $datakecamatan = RsClientController::kecamatan();
        // $datakelurahan = RsClientController::kelurahan();
        // dd($datakabkota);

        foreach ($datakabkota as $data) {
            $cek = KabKota::where('id', $data->id)->get();
            if ($cek->count() == 0) {
                $baru = new KabKota();
                $baru->id = $data->id;
                $baru->nama = $data->nama;
                $baru->provinsi_id = $data->provinsi_id;
                $baru->save();

                Session::flash('sukses', 'Data berhasil ditambahkan!');
            } elseif ($cek->count() > 0) {
                $update = KabKota::find($data->id);
                $update->id = $data->id;
                $update->nama = $data->nama;
                $update->provinsi_id = $data->provinsi_id;
                $update->save();

                Session::flash('sukses', 'Data berhasil diupdate!');
            }
        }

        // $new = KabKota::all();

        return redirect('/master/dummygeo');
    }

    public function kecamatan(Request $request)
    {
        // $dataprovinsi = RsClientController::provinsi();
        // $datakabkota = RsClientController::kabkota();
        // $datakecamatan = RsClientController::kecamatan();
        // $datakelurahan = RsClientController::kelurahan();
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', "kecamatan?page=$request->page&limit=1000", [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);
        if (count($data) > 0) {
            foreach ($data as $data) {
                $cek = Kecamatan::find($data->id);

                // dd(count($cek));
                if ($cek == null) {
                    $baru = new Kecamatan();
                    $baru->id = $data->id;
                    $baru->nama = $data->nama;
                    $baru->kab_kota_id = $data->kab_kota_id;
                    $baru->save();

                    Session::flash('sukses', 'Data berhasil ditambahkan!');
                } elseif ($cek->count() > 0) {
                    $update = Kecamatan::find($data->id);
                    $update->id = $data->id;
                    $update->nama = $data->nama;
                    $update->kab_kota_id = $data->kab_kota_id;
                    $update->save();

                    Session::flash('sukses', 'Data berhasil diupdate!');
                }
            }
        } else {
            Session::flash('error', 'Data kosong!');
        }

        return redirect('/master/dummygeo');
    }

    public function kelurahan(Request $request)
    {
        // $dataprovinsi = RsClientController::provinsi();
        // $datakabkota = RsClientController::kabkota();
        // $datakecamatan = RsClientController::kecamatan();
        // $datakelurahan = RsClientController::kelurahan();
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', "kelurahan?page=$request->page&limit=1000", [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        // dd($data);
        if (count($data) > 0) {
            foreach ($data as $data) {
                $cek = Kelurahan::find($data->id);

                // dd(count($cek));
                if ($cek == null) {
                    $baru = new Kelurahan();
                    $baru->id = $data->id;
                    $baru->nama = $data->nama;
                    $baru->kecamatan_id = $data->kecamatan_id;
                    $baru->save();

                    Session::flash('sukses', 'Data berhasil ditambahkan!');
                } elseif ($cek->count() > 0) {
                    $update = Kelurahan::find($data->id);
                    $update->id = $data->id;
                    $update->nama = $data->nama;
                    $update->kecamatan_id = $data->kecamatan_id;
                    $update->save();

                    Session::flash('sukses', 'Data berhasil diupdate!');
                }
            }
        } else {
            Session::flash('error', 'Data kosong!');
        }

        return redirect('/master/dummygeo');
    }

    public function instalasi(Request $request)
    {
        KankerController::tokenkanker();

        $access_token = session('tokenkanker');
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('GET', 'm_register_penyakit_instalasi_unit/all', [
                'headers' => [
                    'X-Api-Key' => "sirskemkes",
                    'X-Token' => $access_token
                ]
            ]);
        } catch (ClientException $e) {

            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }

            // $id = Crypt::encrypt($id);
            Session::flash('error', $test->message);

            return redirect()->back();
        }

        $data = json_decode($response->getBody());
        $data = $data->data->m_register_penyakit_instalasi_unit;

        // dd($data);
        if (count($data) > 0) {
            foreach ($data as $data) {
                $cek = InstalasiKanker::where('kode_instalasi_unit', $data->kode_instalasi_unit)->first();

                // dd(count($cek));
                if ($cek == null) {
                    $baru = new InstalasiKanker();
                    $baru->kode_instalasi_unit = $data->kode_instalasi_unit;
                    $baru->instalasi_unit = $data->instalasi_unit;
                    $baru->save();

                    Session::flash('sukses', 'Data berhasil ditambahkan!');
                } elseif ($cek->count() > 0) {
                    $update = InstalasiKanker::where('kode_instalasi_unit', $data->kode_instalasi_unit)->first();
                    $update->kode_instalasi_unit = $data->kode_instalasi_unit;
                    $update->instalasi_unit = $data->instalasi_unit;
                    $update->save();

                    Session::flash('sukses', 'Data berhasil diupdate!');
                }
            }
        } else {
            Session::flash('error', 'Data kosong!');
        }

        return redirect('/master/dummygeo');
    }

    public function subinstalasi(Request $request)
    {
        KankerController::tokenkanker();

        $access_token = session('tokenkanker');
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('GET', 'm_register_penyakit_sub_instalasi_unit/all', [
                'headers' => [
                    'X-Api-Key' => "sirskemkes",
                    'X-Token' => $access_token
                ]
            ]);
        } catch (ClientException $e) {

            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }

            // $id = Crypt::encrypt($id);
            Session::flash('error', $test->message);

            return redirect()->back();
        }

        $data = json_decode($response->getBody());
        $data = $data->data->m_register_penyakit_sub_instalasi_unit;

        // dd($data);
        if (count($data) > 0) {
            foreach ($data as $data) {
                $cek = SubinstalasiKanker::where('kode_gabung_sub_instalasi_unit', $data->kode_gabung_sub_instalasi_unit)->first();

                // dd(count($cek));
                if ($cek == null) {
                    $baru = new SubinstalasiKanker();
                    $baru->kode_instalasi_unit = $data->kode_instalasi_unit;
                    $baru->kode_gabung_sub_instalasi_unit = $data->kode_gabung_sub_instalasi_unit;
                    $baru->sub_instalasi_unit = $data->sub_instalasi_unit;
                    $baru->save();

                    Session::flash('sukses', 'Data berhasil ditambahkan!');
                } elseif ($cek->count() > 0) {
                    $update = SubinstalasiKanker::where('kode_gabung_sub_instalasi_unit', $data->kode_gabung_sub_instalasi_unit)->first();
                    $update->kode_instalasi_unit = $data->kode_instalasi_unit;
                    $update->kode_gabung_sub_instalasi_unit = $data->kode_gabung_sub_instalasi_unit;
                    $update->sub_instalasi_unit = $data->sub_instalasi_unit;
                    $update->save();

                    Session::flash('sukses', 'Data berhasil diupdate!');
                }
            }
        } else {
            Session::flash('error', 'Data kosong!');
        }

        return redirect('/master/dummygeo');
    }
}
