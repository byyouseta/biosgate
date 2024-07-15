<?php

namespace App\Http\Controllers;

use App\DataPengajuanKlaim;
use App\DataPengajuanKronis;
use App\PeriodeKlaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class KlaimController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'Master Data');
        session()->put('anak', 'Periode Klaim');
        session()->forget('cucu');

        $data = PeriodeKlaim::orderBy('periode', 'DESC')
            ->get();

        return view('masters.periode', compact('data'));
    }

    public function daftarRajal(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pengajuan Rajal');
        session()->forget('cucu');
        set_time_limit(0);

        if (empty($request->periode)) {
            $dataPengajuan = null;
        } else {
            $dataPengajuan = DataPengajuanKlaim::where('periode_klaim_id', $request->periode)
                ->where('jenis_rawat', 'Rawat Jalan')
                ->get();
        }

        // dd($dataPengajuan);

        $dataPeriode = PeriodeKlaim::orderBy('periode', 'DESC')
            ->get();

        return view('vedika.pengajuan_rajal', compact('dataPengajuan', 'dataPeriode'));
    }

    public function daftarRanap(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pengajuan Ranap');
        session()->forget('cucu');
        set_time_limit(0);

        if (empty($request->periode)) {
            $dataPengajuan = null;
        } else {
            $dataPengajuan = DataPengajuanKlaim::where('periode_klaim_id', $request->periode)
                ->where('jenis_rawat', 'Rawat Inap')
                ->get();
        }

        // dd($dataPengajuan);

        $dataPeriode = PeriodeKlaim::orderBy('periode', 'DESC')
            ->get();

        return view('vedika.pengajuan_ranap', compact('dataPengajuan', 'dataPeriode'));
    }

    public function daftarRajalKronis(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pengajuan Obat Kronis');
        session()->forget('cucu');
        set_time_limit(0);

        if (empty($request->periode)) {
            $dataPengajuan = null;
        } else {
            $dataPengajuan = DataPengajuanKronis::where('periode_klaim_id', $request->periode)
                // ->where('jenis_rawat', 'Rawat Jalan')
                ->get();
        }

        // dd($dataPengajuan);

        $dataPeriode = PeriodeKlaim::orderBy('periode', 'DESC')
            ->get();

        return view('vedika.obat_kronis', compact('dataPengajuan', 'dataPeriode'));
    }

    public function pengajuan(Request $request)
    {

        // dd($request);

        $data = new DataPengajuanKlaim();
        $data->no_rawat = $request->no_rawat;
        $data->no_sep = $request->no_sep;
        $data->no_kartu = $request->no_bpjs;
        $data->nama_pasien = $request->nama_pasien;
        $data->jk = $request->jk;
        $data->tgl_registrasi = $request->tgl_registrasi;
        $data->tgl_lahir = $request->tgl_lahir;
        $data->nama_poli = $request->nm_poli;
        $data->jenis_rawat = $request->jenis_rawat;
        $data->periode_klaim_id = $request->periode;
        $data->save();

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        return redirect()->back();
    }

    public function pengajuanKronis(Request $request)
    {

        // dd($request);

        $data = new DataPengajuanKronis();
        $data->no_rawat = $request->no_rawat;
        $data->no_resep = $request->no_resep;
        $data->no_sep = $request->no_sep;
        $data->no_kartu = $request->no_bpjs;
        $data->nama_pasien = $request->nama_pasien;
        $data->jk = $request->jk;
        $data->tgl_registrasi = $request->tgl_registrasi;
        $data->tgl_lahir = $request->tgl_lahir;
        $data->nama_poli = $request->nm_poli;
        $data->jenis_rawat = $request->jenis_rawat;
        $data->periode_klaim_id = $request->periode;
        $data->save();

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        return redirect()->back();
    }

    public function updatePengajuan($id, Request $request)
    {
        $id = Crypt::decrypt($id);

        $update = DataPengajuanKlaim::find($id);
        // $update->no_resep = $request->no_resep;
        $update->periode_klaim_id = $request->periode;
        $update->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect()->back();
    }

    public function updatePengajuanKronis($id, Request $request)
    {
        $id = Crypt::decrypt($id);

        $update = DataPengajuanKronis::find($id);
        $update->no_resep = $request->no_resep;
        $update->periode_klaim_id = $request->periode;
        $update->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect()->back();
    }

    public function deletePengajuan($id)
    {
        $id = Crypt::decrypt($id);

        $delete = DataPengajuanKlaim::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }

    public function deletePengajuanKronis($id)
    {
        $id = Crypt::decrypt($id);

        $delete = DataPengajuanKronis::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'periode' => 'required|unique:periode_klaims,periode',
        ], [
            'periode.required' => 'Periode hasus diisi!',
            'periode.unique' => 'Periode sudah pernah diinput!',
        ]);

        $periode = $request->periode . '-01';

        $data = new PeriodeKlaim();
        $data->periode = $periode;
        $data->keterangan = $request->keterangan;
        $data->status = $request->status;
        $data->save();

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        return redirect('/master/vedika/klaim');
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);

        $data = PeriodeKlaim::find($id);

        return view('masters.periode_edit', compact('data'));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'periode' => 'required|unique:periode_klaims,periode,' . $id,
        ], [
            'periode.required' => 'Periode hasus diisi!',
            'periode.unique' => 'Periode sudah pernah diinput!',
        ]);

        $data = PeriodeKlaim::find($id);
        $data->periode = $request->periode;
        $data->keterangan = $request->keterangan;
        $data->status = $request->status;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diupdate!');

        return redirect('/master/vedika/klaim');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = PeriodeKlaim::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }
}
