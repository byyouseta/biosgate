<?php

namespace App\Http\Controllers;

use App\Bank;
use App\SaldoKeuangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class SaldoKeuanganController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'Layanan Keuangan');
        session()->put('anak', 'Data Saldo');

        $tanggal = Carbon::now()->format('Y-m-d');

        $data = SaldoKeuangan::where('tgl_transaksi', $tanggal)->get();
        $bank = Bank::all();
        $rekening = SaldoKeuanganController::KdRek();

        // dd($rekening);
        return view('keuangan.saldo', compact('data', 'bank', 'rekening'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'bank' => 'required',
            'saldo' => 'required',
            'kd_rek' => 'required',
        ]);

        $tanggal = Carbon::now()->format('Y-m-d');

        $data = new SaldoKeuangan();
        $data->bank_id = $request->bank;
        $data->saldo = $request->saldo;
        $data->kd_rek = $request->kd_rek;
        $data->tgl_transaksi = $tanggal;
        $data->status = false;
        // $user->akses = $request->akses;
        $data->save();

        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect('/saldokeuangan');
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);

        $data = SaldoKeuangan::find($id);
        $bank = Bank::all();
        $rekening = SaldoKeuanganController::KdRek();

        return view('keuangan.saldo_edit', compact('data', 'bank', 'rekening'));
    }

    public function update($id, Request $request)
    {
        $data = SaldoKeuangan::find($id);
        $data->bank_id = $request->bank;
        $data->saldo = $request->saldo;
        $data->kd_rek = $request->kd_rek;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect('/saldokeuangan');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = SaldoKeuangan::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }

    public function cari(Request $request)
    {
        $tanggal = $request->get('tanggal');

        $data = SaldoKeuangan::where('tgl_transaksi', $tanggal)->get();

        $bank = Bank::all();
        $rekening = SaldoKeuanganController::KdRek();

        return view('keuangan.saldo', compact('data', 'bank', 'rekening'));
    }

    public static function KdRek()
    {
        ClientController::token();

        //Ambil Data Ref Akun
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'ws/ref/rekening');
        $rekening = json_decode($response->getBody());
        $rekening = $rekening->data;

        // dd($rekening);
        return $rekening;
    }
}
