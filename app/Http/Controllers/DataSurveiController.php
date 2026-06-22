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

    public function dashboard(Request $request)
    {
        session()->put('ibu', 'Survei');
        session()->put('anak', 'Dashboard');
        session()->forget('cucu');

        if (empty($request->get('tanggal'))) {
            $tanggal = Carbon::now();
            $namaBulan = $tanggal->monthName;
            $bulanTahun = $tanggal->format('Y-m');
            $tahun = $tanggal->format('Y');
        } else {
            $tanggal = $request->get('tanggal');
            $namaBulan = Carbon::parse($tanggal)->monthName;
            $bulanTahun = Carbon::parse($tanggal)->format('Y-m');
            $tahun = Carbon::parse($tanggal)->format('Y');
        }

        $skorPenilaian = Kepuasan::whereMonth('created_at', Carbon::parse($bulanTahun)->month)
            ->whereYear('created_at', Carbon::parse($bulanTahun)->year)
            ->get();

        $dataPengaduanBulanan = [];
        $dataKepuasanBulanan = [];
        $labelsBulan = [];
        $labelsNamaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $dataSkor = [];

        for ($i = 1; $i <= 12; $i++) {
            $labelsBulan[] = $i;
            $cekPengaduanBulanan = Pengaduan::whereMonth('created_at', $i)
                ->whereYear('created_at', Carbon::parse($bulanTahun)->year)
                ->get();
            array_push($dataPengaduanBulanan, $cekPengaduanBulanan->count());

            $cekKepuasanBulanan = Kepuasan::whereMonth('created_at', $i)
                ->whereYear('created_at', Carbon::parse($bulanTahun)->year)
                ->get();
            array_push($dataKepuasanBulanan, $cekKepuasanBulanan->count());
        }
        $avgPertanyaan1JKN = $skorPenilaian->where('penjamin', 1)->avg('pertanyaan1');
        $avgPertanyaan2JKN = $skorPenilaian->where('penjamin', 1)->avg('pertanyaan2');
        $avgPertanyaan3JKN = $skorPenilaian->where('penjamin', 1)->avg('pertanyaan3');
        $avgPertanyaan4JKN = 0;
        $avgPertanyaan5JKN = $skorPenilaian->where('penjamin', 1)->avg('pertanyaan5');
        $avgPertanyaan6JKN = $skorPenilaian->where('penjamin', 1)->avg('pertanyaan6');
        $avgPertanyaan7JKN = $skorPenilaian->where('penjamin', 1)->avg('pertanyaan7');
        $avgPertanyaan8JKN = $skorPenilaian->where('penjamin', 1)->avg('pertanyaan8');
        $avgPertanyaan9JKN = $skorPenilaian->where('penjamin', 1)->avg('pertanyaan9');
        $avgPertanyaan10JKN = $skorPenilaian->where('penjamin', 1)->avg('pertanyaan10');
        $avgPertanyaan910JKN = ($avgPertanyaan9JKN + $avgPertanyaan10JKN) / 2;

        $avgPertanyaan1nonJKN = $skorPenilaian->where('penjamin', '!=', 1)->avg('pertanyaan1');
        $avgPertanyaan2nonJKN = $skorPenilaian->where('penjamin', '!=', 1)->avg('pertanyaan2');
        $avgPertanyaan3nonJKN = $skorPenilaian->where('penjamin', '!=', 1)->avg('pertanyaan3');
        $avgPertanyaan4nonJKN = $skorPenilaian->where('penjamin', '!=', 1)->avg('pertanyaan4');
        $avgPertanyaan5nonJKN = $skorPenilaian->where('penjamin', '!=', 1)->avg('pertanyaan5');
        $avgPertanyaan6nonJKN = $skorPenilaian->where('penjamin', '!=', 1)->avg('pertanyaan6');
        $avgPertanyaan7nonJKN = $skorPenilaian->where('penjamin', '!=', 1)->avg('pertanyaan7');
        $avgPertanyaan8nonJKN = $skorPenilaian->where('penjamin', '!=', 1)->avg('pertanyaan8');
        $avgPertanyaan9nonJKN = $skorPenilaian->where('penjamin', '!=', 1)->avg('pertanyaan9');
        $avgPertanyaan10nonJKN = $skorPenilaian->where('penjamin', '!=', 1)->avg('pertanyaan10');
        $avgPertanyaan910nonJKN = ($avgPertanyaan9nonJKN + $avgPertanyaan10nonJKN) / 2;

        $avgTotalPertanyaan1 = ($avgPertanyaan1JKN + $avgPertanyaan1nonJKN) / 2;
        $avgTotalPertanyaan2 = ($avgPertanyaan2JKN + $avgPertanyaan2nonJKN) / 2;
        $avgTotalPertanyaan3 = ($avgPertanyaan3JKN + $avgPertanyaan3nonJKN) / 2;
        $avgTotalPertanyaan4 = ($avgPertanyaan4JKN + $avgPertanyaan4nonJKN);
        $avgTotalPertanyaan5 = ($avgPertanyaan5JKN + $avgPertanyaan5nonJKN) / 2;
        $avgTotalPertanyaan6 = ($avgPertanyaan6JKN + $avgPertanyaan6nonJKN) / 2;
        $avgTotalPertanyaan7 = ($avgPertanyaan7JKN + $avgPertanyaan7nonJKN) / 2;
        $avgTotalPertanyaan8 = ($avgPertanyaan8JKN + $avgPertanyaan8nonJKN) / 2;

        $avgTotalPertanyaan910 = ($avgPertanyaan910JKN + $avgPertanyaan910nonJKN) / 2;
        $avgTotalAll = ($avgTotalPertanyaan1 + $avgTotalPertanyaan2 + $avgTotalPertanyaan3 + $avgTotalPertanyaan4 + $avgTotalPertanyaan5 + $avgTotalPertanyaan6 + $avgTotalPertanyaan7 + $avgTotalPertanyaan8 + $avgTotalPertanyaan910) / 9;

        // dd($avgTotalPertanyaan1, $avgTotalPertanyaan2, $avgTotalPertanyaan3, $avgTotalPertanyaan4, $avgTotalPertanyaan5, $avgTotalPertanyaan6, $avgTotalPertanyaan7, $avgTotalPertanyaan8, $avgTotalPertanyaan910, $avgTotalAll * 25);

        // $pengaduan = Pengaduan::count();
        // $kepuasan = Kepuasan::count();

        return view('survei.dashboard', compact(
            'labelsNamaBulan',
            'dataPengaduanBulanan',
            'dataKepuasanBulanan',
            'avgTotalPertanyaan1',
            'avgTotalPertanyaan2',
            'avgTotalPertanyaan3',
            'avgTotalPertanyaan4',
            'avgTotalPertanyaan5',
            'avgTotalPertanyaan6',
            'avgTotalPertanyaan7',
            'avgTotalPertanyaan8',
            'avgTotalPertanyaan910',
            'avgTotalAll'
        ));
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
