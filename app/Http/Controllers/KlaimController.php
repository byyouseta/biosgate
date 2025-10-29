<?php

namespace App\Http\Controllers;

use App\DataPengajuanKlaim;
use App\DataPengajuanKronis;
use App\DataPengajuanUlang;
use App\PeriodeKlaim;
use App\PeriodePengajuanUlang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
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

    public function periodePending()
    {
        session()->put('ibu', 'Master Data');
        session()->put('anak', 'Periode Klaim Pending');
        session()->forget('cucu');

        $data = PeriodePengajuanUlang::orderBy('periode', 'DESC')
            ->get();

        return view('masters.periode_pending', compact('data'));
    }

    public function daftarRajal(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pengajuan Rajal');
        session()->forget('cucu');
        set_time_limit(0);

        if (empty($request->periode)) {
            $dataPengajuan = null;
            $diagnosaGrouped = null;
            $prosedurGrouped = null;
        } else {
            $dataPengajuan = DataPengajuanKlaim::where('periode_klaim_id', $request->periode)
                ->where('jenis_rawat', 'Rawat Jalan')
                ->get();

            // dd($dataPengajuan->first());
            $noRawatList = $dataPengajuan->pluck('no_rawat')->unique();

            $diagnosa = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
                ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
                ->select(
                    'diagnosa_pasien.no_rawat',
                    'diagnosa_pasien.kd_penyakit',
                    'diagnosa_pasien.prioritas',
                    'diagnosa_pasien.status',
                    'penyakit.nm_penyakit'
                )
                ->whereIn('diagnosa_pasien.no_rawat', $noRawatList)
                ->where('diagnosa_pasien.status', '=', 'Ralan')
                ->orderBy('diagnosa_pasien.no_rawat', 'ASC')
                ->orderBy('diagnosa_pasien.prioritas', 'ASC')
                ->get();

            $prosedur = DB::connection('mysqlkhanza')->table('prosedur_pasien')
                ->join('icd9', 'icd9.kode', '=', 'prosedur_pasien.kode')
                ->select(
                    'prosedur_pasien.no_rawat',
                    'prosedur_pasien.kode',
                    'prosedur_pasien.status',
                    'icd9.deskripsi_panjang'
                )
                ->whereIn('prosedur_pasien.no_rawat', $noRawatList)
                ->where('prosedur_pasien.status', '=', 'Ralan')
                ->orderBy('prosedur_pasien.no_rawat', 'ASC')
                ->orderBy('prosedur_pasien.prioritas', 'ASC')
                ->get();

            $diagnosaGrouped = $diagnosa->groupBy('no_rawat');
            $prosedurGrouped = $prosedur->groupBy('no_rawat');

            // dd($prosedurGrouped->first(10));
        }



        $dataPeriode = PeriodeKlaim::orderBy('periode', 'DESC')
            ->get();

        return view('vedika.pengajuan_rajal', compact('dataPengajuan', 'dataPeriode', 'diagnosaGrouped', 'prosedurGrouped'));
    }

    public function daftarRanap(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pengajuan Ranap');
        session()->forget('cucu');
        set_time_limit(0);

        if (empty($request->periode)) {
            $dataPengajuan = null;

            $diagnosaGrouped = null;
            $prosedurGrouped = null;
        } else {
            $dataPengajuan = DataPengajuanKlaim::where('periode_klaim_id', $request->periode)
                ->where('jenis_rawat', 'Rawat Inap')
                ->get();

            $noRawatList = $dataPengajuan->pluck('no_rawat')->unique();

            $diagnosa = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
                ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
                ->select(
                    'diagnosa_pasien.no_rawat',
                    'diagnosa_pasien.kd_penyakit',
                    'diagnosa_pasien.prioritas',
                    'diagnosa_pasien.status',
                    'penyakit.nm_penyakit'
                )
                ->whereIn('diagnosa_pasien.no_rawat', $noRawatList)
                ->where('diagnosa_pasien.status', '=', 'Ranap')
                ->orderBy('diagnosa_pasien.no_rawat', 'ASC')
                ->orderBy('diagnosa_pasien.prioritas', 'ASC')
                ->get();

            $prosedur = DB::connection('mysqlkhanza')->table('prosedur_pasien')
                ->join('icd9', 'icd9.kode', '=', 'prosedur_pasien.kode')
                ->select(
                    'prosedur_pasien.no_rawat',
                    'prosedur_pasien.kode',
                    'prosedur_pasien.status',
                    'icd9.deskripsi_panjang'
                )
                ->whereIn('prosedur_pasien.no_rawat', $noRawatList)
                ->where('prosedur_pasien.status', '=', 'Ranap')
                ->orderBy('prosedur_pasien.no_rawat', 'ASC')
                ->orderBy('prosedur_pasien.prioritas', 'ASC')
                ->get();

            $diagnosaGrouped = $diagnosa->groupBy('no_rawat');
            $prosedurGrouped = $prosedur->groupBy('no_rawat');
        }

        // dd($diagnosaGrouped);

        $dataPeriode = PeriodeKlaim::orderBy('periode', 'DESC')
            ->get();

        return view('vedika.pengajuan_ranap', compact('dataPengajuan', 'dataPeriode', 'diagnosaGrouped', 'prosedurGrouped'));
    }

    public function daftarUlang(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pengajuan Ulang');
        session()->forget('cucu');
        set_time_limit(0);

        if (empty($request->periode)) {
            $dataPengajuan = null;
        } else {
            $dataPengajuan = DataPengajuanUlang::where('periode_pengajuan_ulang_id', $request->periode)
                ->where('jenis_rawat', $request->jenis)
                ->get();
        }

        // dd($dataPengajuan);

        $dataPeriode = PeriodePengajuanUlang::orderBy('periode', 'DESC')
            ->get();

        return view('vedika.pengajuan_ulang', compact('dataPengajuan', 'dataPeriode'));
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

    public function pengajuanUlang(Request $request)
    {

        // dd($request);

        $data = new DataPengajuanUlang();
        $data->no_rawat = $request->no_rawat;
        $data->no_sep = $request->no_sep;
        $data->no_kartu = $request->no_bpjs;
        $data->nama_pasien = $request->nama_pasien;
        $data->jk = $request->jk;
        $data->tgl_registrasi = $request->tgl_registrasi;
        $data->tgl_lahir = $request->tgl_lahir;
        $data->nama_poli = $request->nm_poli;
        $data->jenis_rawat = $request->jenis_rawat;
        $data->periode_pengajuan_ulang_id = $request->periode;
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

    public function deletePengajuanUlang($id)
    {
        $id = Crypt::decrypt($id);

        $delete = DataPengajuanUlang::find($id);
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

    public function storePending(Request $request)
    {
        $this->validate($request, [
            'periode' => 'required|unique:periode_pengajuan_ulangs,periode',
        ], [
            'periode.required' => 'Periode hasus diisi!',
            'periode.unique' => 'Periode sudah pernah diinput!',
        ]);

        $periode = $request->periode . '-01';

        $data = new PeriodePengajuanUlang();
        $data->periode = $periode;
        $data->keterangan = $request->keterangan;
        $data->status = $request->status;
        $data->save();

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        return redirect('/master/vedika/klaimpending');
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);

        $data = PeriodeKlaim::find($id);

        return view('masters.periode_edit', compact('data'));
    }

    public function editPending($id)
    {
        $id = Crypt::decrypt($id);

        $data = PeriodePengajuanUlang::find($id);

        return view('masters.periode_edit_pending', compact('data'));
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

    public function updatePending($id, Request $request)
    {
        $this->validate($request, [
            'periode' => 'required|unique:periode_pengajuan_ulangs,periode,' . $id,
        ], [
            'periode.required' => 'Periode hasus diisi!',
            'periode.unique' => 'Periode sudah pernah diinput!',
        ]);

        $data = PeriodePengajuanUlang::find($id);
        if ($data) {
            $data->periode = $request->periode;
            $data->keterangan = $request->keterangan;
            $data->status = $request->status;
            $data->save();

            Session::flash('sukses', 'Data Berhasil diupdate!');
        } else {
            Session::flash('sukses', 'Data tidak ditemukan!');

            return redirect()->back();
        }


        return redirect('/master/vedika/klaimpending');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = PeriodeKlaim::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }

    public function deletePending($id)
    {
        $id = Crypt::decrypt($id);
        $delete = PeriodePengajuanUlang::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }
}
