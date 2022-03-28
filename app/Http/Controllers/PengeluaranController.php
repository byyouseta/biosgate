<?php

namespace App\Http\Controllers;

use App\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class PengeluaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'Layanan Keuangan');
        session()->put('anak', 'Data Pengeluaran');

        $tanggal = Carbon::now()->format('Y-m-d');

        $data = Pengeluaran::where('tgl_transaksi', $tanggal)->get();

        // ClientController::token();

        $akun = PemasukanController::RefAkun();
        // dd($akun->data);
        return view('keuangan.pengeluaran', compact('data', 'akun'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'kd_akun' => 'required',
            'jumlah' => 'required',
        ]);

        $tanggal = Carbon::now()->format('Y-m-d');

        $data = new Pengeluaran();
        $data->kd_akun = $request->kd_akun;
        $data->jumlah = $request->jumlah;
        $data->tgl_transaksi = $tanggal;
        $data->status = false;
        // $user->akses = $request->akses;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect('/pengeluaran');
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);

        $data = Pengeluaran::find($id);
        $akun = PemasukanController::RefAkun();

        return view('keuangan.pengeluaran_edit', compact('data', 'akun'));
    }

    public function update($id, Request $request)
    {
        $data = Pengeluaran::find($id);
        $data->kd_akun = $request->kd_akun;
        $data->jumlah = $request->jumlah;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect('/pengeluaran');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = Pengeluaran::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }

    public function cari(Request $request)
    {
        $tanggal = $request->get('tanggal');

        $data = Pengeluaran::where('tgl_transaksi', $tanggal)->get();

        // ClientController::token();

        $akun = PemasukanController::RefAkun();

        return view('keuangan.pemasukan', compact('data', 'akun'));
    }
}
