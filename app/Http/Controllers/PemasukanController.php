<?php

namespace App\Http\Controllers;

use App\Pemasukan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class PemasukanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'Layanan Keuangan');
        session()->put('anak', 'Data Penerimaan');

        $tanggal = Carbon::now()->format('Y-m-d');

        $data = Pemasukan::where('tgl_transaksi', $tanggal)->get();

        // ClientController::token();

        $akun = PemasukanController::RefAkun();
        // dd($akun->data);
        return view('keuangan.pemasukan', compact('data', 'akun'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'kd_akun' => 'required',
            'jumlah' => 'required',
        ]);

        $tanggal = Carbon::now()->format('Y-m-d');

        $data = new Pemasukan();
        $data->kd_akun = $request->kd_akun;
        $data->jumlah = $request->jumlah;
        $data->tgl_transaksi = $tanggal;
        $data->status = false;
        // $user->akses = $request->akses;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect('/penerimaan');
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);

        $data = Pemasukan::find($id);
        $akun = PemasukanController::RefAkun();

        return view('keuangan.pemasukan_edit', compact('data', 'akun'));
    }

    public function update($id, Request $request)
    {
        $data = Pemasukan::find($id);
        $data->kd_akun = $request->kd_akun;
        $data->jumlah = $request->jumlah;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect('/penerimaan');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = Pemasukan::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }

    public function cari(Request $request)
    {
        $tanggal = $request->get('tanggal');

        $data = Pemasukan::where('tgl_transaksi', $tanggal)->get();

        // ClientController::token();

        $akun = PemasukanController::RefAkun();

        return view('keuangan.pemasukan', compact('data', 'akun'));
    }

    public static function RefAkun()
    {
        ClientController::token();

        //Ambil Data Ref Akun
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'ws/ref/akun');
        $akun = json_decode($response->getBody());
        $akun = $akun->data;

        return $akun;
    }
}
