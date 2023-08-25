<?php

namespace App\Http\Controllers;

use App\Kepuasan;
use App\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class DataSurveiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function pengaduan()
    {
        session()->put('ibu', 'Survei');
        session()->put('anak', 'Data Pengaduan');
        session()->forget('cucu');

        $data = Pengaduan::all();

        return view('survei.data_pengaduan', compact('data'));
    }

    public function deletePengaduan($id)
    {
        $id = Crypt::decrypt($id);

        $hapus = Pengaduan::find($id);
        $hapus->delete();

        Session::flash('sukses', 'Data berhasil dihapus');

        return redirect()->back();
    }

    public function detailPengaduan($id)
    {
        $id = Crypt::decrypt($id);

        $data = Pengaduan::find($id);

        return view('survei.detail_pengaduan', compact('data'));
    }

    public function statusPengaduan($id, Request $request)
    {
        $id = Crypt::decrypt($id);

        $update = Pengaduan::find($id);
        $update->status_keluhan_id = $request->status_pelaporan;
        $update->save();

        Session::flash('sukses', 'Data berhasil diupdate');

        return redirect('/survei/datapengaduan');
    }

    public function kepuasan()
    {
        session()->put('ibu', 'Survei');
        session()->put('anak', 'Data Kepuasan');
        session()->forget('cucu');

        $data = Kepuasan::all();

        return view('survei.data_kepuasan', compact('data'));
    }

    public function detailKepuasan($id)
    {
        $id = Crypt::decrypt($id);

        $data = Kepuasan::find($id);

        return view('survei.detail_kepuasan', compact('data'));
    }
}
