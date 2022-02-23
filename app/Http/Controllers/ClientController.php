<?php

namespace App\Http\Controllers;

use App\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;

// use GuzzleHttp\Psr7\Request;

class ClientController extends Controller
{
    public function token()
    {
        $setting = Setting::find(1);

        session()->put('base_url', $setting->base_url);

        $client = new \GuzzleHttp\Client(['base_uri' => $setting->base_url]);
        $response = $client->request('POST', 'token', [
            'form_params' => [
                'satker' => $setting->satker,
                'key' => $setting->key,
            ]
        ]);

        $data = json_decode($response->getBody());

        // dd($data);

        if ($data->status == "MSG20004") {
            session()->put('token', $data->token);
        }
        // dd(session('token'));
    }

    public function saldoawal()
    {
        session()->put('ibu', 'Client Saldo Awal');
        session()->forget('anak');

        ClientController::token();

        $tanggal = '2022-2-23';

        $saldo = SaldoAwalController::saldo($tanggal);

        // dd($tanggal);
        // dd($saldo[0]['tgl_transaksi']);

        // dd(session('token'));
        foreach ($saldo as $data) {
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('POST', 'ws/kesehatan/prod', [
                'headers' => [
                    'token' => session('token'),
                ],
                'form_params' => [
                    'kelas' => $data->kelas,
                    'jml_pasien' => $data->jml_pasien,
                    'jml_hari' => $data->jml_hari,
                    'tgl_transaksi' => $data->tgl_transaksi,
                ]
            ]);

            $data = json_decode($response->getBody());

            if ($data->status != 'MSG20003') {
                Session::flash('error', $data->message);

                return view('client_saldo');
            } else {
                $tanggal_update = Carbon::now()->locale('id')->format('Y-m-d H:i:s');
            }
        }

        Session::flash('sukses', "$data->status, $data->message");

        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('POST', 'get/data/kesehatan', [
            'headers' => [
                'token' => session('token'),
            ],
            'form_params' => [
                'tgl_transaksi' => Carbon::parse($tanggal)->format('Y/m/d'),
            ]
        ]);

        $dataterkirim = json_decode($response->getBody());

        // dd($dataterkirim);

        return view('client_saldo', compact('data', 'tanggal_update', 'dataterkirim'));
    }

    public function kesehatan()
    {
        session()->put('ibu', 'Client Saldo Awal');
        session()->forget('anak');

        ClientController::token();

        // $tanggal = Carbon::now()->locale('id')->format('Y-m-d');

        $tanggal = '2022-01-03';
        //data Inap Tanggal sesuai tanggal
        $inap = KesehatanController::ranap($tanggal);

        // dd($inap);

        foreach ($inap as $datainap) {
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('POST', 'pk1/layanan/pasien-rawat-inap', [
                'headers' => [
                    'token' => session('token'),
                ],
                'form_params' => [
                    'kode_kelas' => $datainap->kode_kelas,
                    'jumlah' => $datainap->jumlah,
                    'tgl_transaksi' => $datainap->tgl_transaksi,
                ]
            ]);

            $data = json_decode($response->getBody());

            dd($data);

            if ($data->status != 'MSG20003') {
                Session::flash('error', $data->message);

                return view('client_saldo');
            } else {
                $tanggal_update = Carbon::now()->locale('id')->format('Y-m-d H:i:s');
            }
        }

        dd($data);
    }
}
