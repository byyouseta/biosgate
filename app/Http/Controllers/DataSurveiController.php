<?php

namespace App\Http\Controllers;

use App\Exports\KepuasanExport;
use App\Exports\PengaduanExport;
use App\Kepuasan;
use App\Pengaduan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
// use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel;

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

    public function kepuasan(Request $request)
    {
        session()->put('ibu', 'Survei');
        session()->put('anak', 'Data Kepuasan');
        session()->forget('cucu');

        if (empty($request->get('tanggal'))) {
            $bulan = Carbon::now()->format('m');
            $tahun = Carbon::now()->format('Y');
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
            $bulan = Carbon::parse($tanggal)->format('m');
            $tahun = Carbon::parse($tanggal)->format('Y');
        }

        $data = Kepuasan::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        return view('survei.data_kepuasan', compact('data'));
    }

    public function detailKepuasan($id)
    {
        $id = Crypt::decrypt($id);

        $data = Kepuasan::find($id);

        return view('survei.detail_kepuasan', compact('data'));
    }

    public function exportPengaduan()
    {
        $tanggal = Carbon::now();
        $formatTanggal = Carbon::parse($tanggal)->format('dmYHis');
        return Excel::download(new PengaduanExport, "Data Pengaduan $formatTanggal.xlsx");
    }

    public function exportKepuasan($id)
    {
        $periode = Crypt::decrypt($id);

        $tanggal = new Carbon($periode);
        // dd($periode, $tanggal);
        $bulan = Carbon::parse($tanggal)->format('m');
        $tahun = Carbon::parse($tanggal)->format('Y');

        $formatTanggal = Carbon::parse($tanggal)->locale('id')->format('F Y');
        // return Excel::download(new KepuasanExport, "Data Kepuasan $formatTanggal.xlsx");

        $data = Kepuasan::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        // dd($data);

        return Excel::download(new KepuasanExport($data), "Data Kepuasan periode $formatTanggal.xlsx");
    }
}
