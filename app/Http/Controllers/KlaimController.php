<?php

namespace App\Http\Controllers;

use App\DataPengajuanKlaim;
use App\DataPengajuanKronis;
use App\DataPengajuanUlang;
use App\Exports\IndikasiRanapExport;
use App\PeriodeKlaim;
use App\PeriodePengajuanUlang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

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

    public function getPasien(Request $request)
    {
        $pasien = DataPengajuanKlaim::where('no_rawat', $request->no_rawat)
            ->orWhere('no_sep', $request->no_sep)
            ->first();

        if (!$pasien) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'no_rawat' => $pasien->no_rawat,
                'no_sep' => $pasien->no_sep,
                'no_bpjs' => $pasien->no_kartu,
                'nama_pasien' => $pasien->nama_pasien,
                'tgl_lahir' => $pasien->tgl_lahir,
                'jk' => $pasien->jk,
                'tgl_registrasi' => $pasien->tgl_registrasi,
                'nm_poli' => $pasien->nama_poli,
                'jenis_rawat' => $pasien->jenis_rawat
            ]
        ]);
    }

    public function pengajuan(Request $request)
    {

        $cek = DataPengajuanKlaim::where('no_rawat', $request->no_rawat)
            ->where('jenis_rawat', $request->jenis_rawat)
            ->first();

        if ($cek) {
            Session::flash('gagal', 'Data dengan No. Rawat ' . $request->no_rawat . ' sudah pernah diajukan!');

            return redirect()->back();
        } else {
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
    }

    public function pengajuanUlang(Request $request)
    {

        $cek = DataPengajuanUlang::where('no_rawat', $request->no_rawat)
            ->where('jenis_rawat', $request->jenis_rawat)
            ->first();

        if ($cek) {
            Session::flash('gagal', 'Data dengan No. Rawat ' . $request->no_rawat . ' sudah pernah diajukan!');

            return redirect()->back();
        } else {
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

    public function indikasiRanap($id)
    {
        $id = Crypt::decrypt($id);
        $periode = PeriodeKlaim::find($id);

        $formatPeriode = date('F Y', strtotime($periode->periode));

        $data = DataPengajuanKlaim::where('periode_klaim_id', $id)
            ->where('jenis_rawat', 'Rawat Inap')->get();
        //ambil data no_rawat dari data pengajuan klaim berdasarkan periode
        $noRawatList = $data->pluck('no_rawat')->unique();

        $sub = DB::connection('mysqlkhanza')
            ->table('kamar_inap')
            ->select(
                'no_rawat',
                DB::raw('MAX(CONCAT(tgl_keluar, " ", jam_keluar)) as terakhir')
            )
            ->groupBy('no_rawat');

        $dataMasuk = DB::connection('mysqlkhanza')
            ->table('kamar_inap')
            ->joinSub($sub, 'ki_terakhir', function ($join) {
                $join->on('kamar_inap.no_rawat', '=', 'ki_terakhir.no_rawat')
                    ->whereRaw('CONCAT(kamar_inap.tgl_keluar, " ", kamar_inap.jam_keluar) = ki_terakhir.terakhir');
            })
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'kamar_inap.no_rawat',
                'kamar_inap.tgl_masuk',
                'kamar_inap.tgl_keluar',
                'kamar_inap.jam_keluar'
            )
            ->whereIn('kamar_inap.no_rawat', $noRawatList)
            ->get();

        $dataSpri = DB::connection('mysqlkhanza')->table('permintaan_ranap')
            ->join('permintaan_ranap_detail', 'permintaan_ranap_detail.no_rawat', '=', 'permintaan_ranap.no_rawat')
            ->join('dokter', 'dokter.kd_dokter', '=', 'permintaan_ranap_detail.kd_dokter')
            ->join('kamar', 'kamar.kd_kamar', '=', 'permintaan_ranap.kd_kamar')
            ->leftJoin('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->select(
                'permintaan_ranap.no_rawat',
                'permintaan_ranap.tanggal',
                'permintaan_ranap.kd_kamar',
                'permintaan_ranap.diagnosa',
                'permintaan_ranap.catatan',
                'permintaan_ranap_detail.tindakan',
                'permintaan_ranap_detail.perkiraan_hasil',
                'permintaan_ranap_detail.perkiraan_biaya',
                'permintaan_ranap_detail.jam',
                'permintaan_ranap_detail.kd_dokter',
                'dokter.nm_dokter',
                'bangsal.nm_bangsal'
            )
            ->whereIn('permintaan_ranap.no_rawat', $noRawatList)
            ->get();

        $dataIgd = DB::connection('mysqlkhanza')->table('penilaian_medis_igd')
            ->whereIn('penilaian_medis_igd.no_rawat', $noRawatList)
            ->get();


        foreach ($data as $item) {
            $masuk = $dataMasuk->where('no_rawat', $item->no_rawat)->first();
            $spri = $dataSpri->where('no_rawat', $item->no_rawat)->first();
            $igd = $dataIgd->where('no_rawat', $item->no_rawat)->first();
            if ($masuk) {
                $item->no_rm = $masuk->no_rkm_medis;
                $item->tgl_masuk = $masuk->tgl_registrasi;
                $item->tgl_keluar = $masuk->tgl_keluar;
                $item->jam_keluar = $masuk->jam_keluar;
            } else {
                $item->no_rm = null;
                $item->tgl_masuk = null;
                $item->tgl_keluar = null;
                $item->jam_keluar = null;
            }
            if ($spri) {
                $item->dpjp = $spri->nm_dokter;
                $item->indikasi = $spri->catatan;
            } else {
                $item->dpjp = null;
                $item->indikasi = null;
            }
            if ($igd) {
                $item->keluhan = $igd->keluhan_utama;
                $item->kesadaran = $igd->kesadaran;
                $item->suhu = $igd->suhu;
                $item->nadi = $igd->nadi;
                $item->respirasi = $igd->rr;
                $item->tensi = $igd->td;
                $item->spo = $igd->spo;
                $item->bb = $igd->bb;
                $item->tb = $igd->tb;
            } else {
                $item->kesadaran = null;
                $item->suhu = null;
                $item->nadi = null;
                $item->respirasi = null;
                $item->tensi = null;
                $item->spo = null;
                $item->bb = null;
                $item->tb = null;
            }
        }

        // return view('vedika.export_indikasiranap', compact('data'));
        return Excel::download(
            new IndikasiRanapExport($data),
            'indikasi_ranap ' . $formatPeriode . '.xlsx'
        );
    }
}
