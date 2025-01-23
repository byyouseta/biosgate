<?php

namespace App\Http\Controllers;

use App\Imports\KlaimCairImport;
use App\KlaimCair;
use App\KlaimPending;
use App\sepManual;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class KlaimCompareController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Klaim Compare');
        session()->forget('cucu');
        set_time_limit(0);

        if (isset($request->tanggal)) {
            $tanggal = $request->tanggal;
            $tanggalSelesai = $request->tanggalSelesai;
        } else {
            $tanggal = Carbon::now()->format('Y-m-d');
            $tanggalSelesai = Carbon::now()->format('Y-m-d');
        }

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->leftJoin('bridging_sep', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.almt_pj',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'reg_periksa.stts',
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.no_peserta',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'bridging_sep.no_sep',
                'dokter.nm_dokter'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            // ->whereDate('reg_periksa.tgl_registrasi', '=', $tanggal)
            ->whereBetween('reg_periksa.tgl_registrasi', [$tanggal, $tanggalSelesai])
            ->where('reg_periksa.stts', '!=', 'Batal')
            ->get();

        // dd($data);

        return view('vedika.compare', compact('data'));
    }

    public function import_excel(Request $request)
    {
        set_time_limit(0);
        // validasi
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        // menangkap file excel
        $file = $request->file('file');

        // membuat nama file unik
        $nama_file = rand() . $file->getClientOriginalName();

        // upload ke folder file_siswa di dalam folder public
        $file->move('klaim_cair', $nama_file);

        // // import data
        // Excel::import(new DataEklaimImport, public_path('/eklaim/' . $nama_file));

        // // notifikasi dengan session
        try {
            $data = Excel::toArray(new KlaimCairImport(), public_path('/klaim_cair/' . $nama_file));

            foreach ($data[0] as $dataEklaim) {
                $cek = KlaimCair::where('no_sep', $dataEklaim['no_sep'])
                    ->get();


                if ($cek->count() == '0') {

                    $simpan = new KlaimCair();
                    $simpan->tgl_verif = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dataEklaim['tgl_verif']);
                    $simpan->no_sep = $dataEklaim['no_sep'];
                    $simpan->riil = $dataEklaim['riil'];
                    $simpan->diajukan = $dataEklaim['diajukan'];
                    $simpan->disetujui = $dataEklaim['disetujui'];
                    $simpan->jenis_rawat = $dataEklaim['jenis_rawat'];
                    $simpan->save();
                }
            }


            // notifikasi dengan session
            Session::flash('sukses', 'Data Berhasil Diimport!');
        } catch (\Throwable $th) {

            // notifikasi dengan session
            Session::flash('error', 'Cek kembali data file Anda!');
        }

        // alihkan halaman kembali
        return redirect('/vedika/klaimcompare');
    }

    public function template()
    {
        //PDF file is stored under project/public/download/info.pdf
        $file = public_path() . "/klaim_cair/template_klaim_cair.xlsx";

        $headers = [
            'Content-Type' => 'application/xlsx',
        ];

        return response()->download($file, 'template_klaim_cair.xlsx', $headers);
    }

    public function ambilResponeVklaim(Request $request)
    {
        $request->validate([
            'periodeBulan' => 'required|date', // Validasi tanggal (wajib diisi dan harus berupa tanggal yang valid)
            'status' => 'required',   // Validasi radio button (wajib dipilih)
        ]);

        $carbonDate = Carbon::parse($request->periodeBulan . '-01');
        $jumlahHari = $carbonDate->daysInMonth;

        if ($request->status == 2) { //Untuk data pending
            for ($i = 1; $i <= $jumlahHari; $i++) {
                // Untuk data Ranap
                $ambil = SepController::getStatusKlaim("$request->periodeBulan-$i", "1", $request->status);
                if ($ambil) {
                    //Untuk data pending
                    foreach ($ambil as $responseAmbil) {
                        $cekKlaim = KlaimPending::where('no_sep', $responseAmbil->noSEP)->first();
                        if ($cekKlaim) {
                            $cekKlaim->status = $responseAmbil->status;
                            $cekKlaim->biaya_tarif_rs = $responseAmbil->biaya->byTarifRS;
                            $cekKlaim->biaya_tarif_grouper = $responseAmbil->biaya->byTarifGruper;
                            $cekKlaim->biaya_pengajuan = $responseAmbil->biaya->byPengajuan;
                            $cekKlaim->biaya_disetujui = $responseAmbil->biaya->bySetujui;
                        } else {
                            $new = new KlaimPending;
                            $new->no_sep = $responseAmbil->noSEP;
                            $new->tgl_sep = $responseAmbil->tglSep;
                            $new->tgl_pulang = $responseAmbil->tglPulang;
                            $new->kelas_rawat = $responseAmbil->kelasRawat;
                            $new->poli = $responseAmbil->poli;
                            $new->status = $responseAmbil->status;
                            $new->biaya_pengajuan = $responseAmbil->biaya->byPengajuan;
                            $new->biaya_tarif_grouper = $responseAmbil->biaya->byTarifGruper;
                            $new->biaya_tarif_rs = $responseAmbil->biaya->byTarifRS;
                            $new->biaya_disetujui = $responseAmbil->biaya->bySetujui;
                            $new->jenis_rawat = 'RI';
                            $new->save();
                        }
                    }
                }
                // Untuk data Rajal
                $ambil = SepController::getStatusKlaim("$request->periodeBulan-$i", "2", $request->status);
                if ($ambil) {
                    // dd($ambil, 'Rajal');
                    foreach ($ambil as $responseAmbil) {
                        $cekKlaim = KlaimPending::where('no_sep', $responseAmbil->noSEP)->first();
                        if ($cekKlaim) {
                            $cekKlaim->status = $responseAmbil->status;
                            $cekKlaim->biaya_tarif_rs = $responseAmbil->biaya->byTarifRS;
                            $cekKlaim->biaya_tarif_grouper = $responseAmbil->biaya->byTarifGruper;
                            $cekKlaim->biaya_pengajuan = $responseAmbil->biaya->byPengajuan;
                            $cekKlaim->biaya_disetujui = $responseAmbil->biaya->bySetujui;
                        } else {
                            $new = new KlaimPending;
                            $new->no_sep = $responseAmbil->noSEP;
                            $new->tgl_sep = $responseAmbil->tglSep;
                            $new->tgl_pulang = $responseAmbil->tglPulang;
                            $new->kelas_rawat = $responseAmbil->kelasRawat;
                            $new->poli = $responseAmbil->poli;
                            $new->status = $responseAmbil->status;
                            $new->biaya_pengajuan = $responseAmbil->biaya->byPengajuan;
                            $new->biaya_tarif_grouper = $responseAmbil->biaya->byTarifGruper;
                            $new->biaya_tarif_rs = $responseAmbil->biaya->byTarifRS;
                            $new->biaya_disetujui = $responseAmbil->biaya->bySetujui;
                            $new->jenis_rawat = 'RJ';
                            $new->save();
                        }
                    }
                }
            }
        } elseif ($request->status == 3) { //Untuk data Cair
            // dd($request, $jumlahHari);
            for ($i = 1; $i <= $jumlahHari; $i++) {
                // Untuk data Ranap
                $ambil = SepController::getStatusKlaim("$request->periodeBulan-$i", "1", $request->status);
                if ($ambil) {
                    //Untuk data pending
                    foreach ($ambil as $responseAmbil) {
                        // dd($responseAmbil, 'Rajal');
                        $cekKlaim = KlaimCair::where('no_sep', $responseAmbil->noSEP)->first();
                        if ($cekKlaim) {
                            $cekKlaim->riil = $responseAmbil->biaya->byTarifRS;
                            $cekKlaim->diajukan = $responseAmbil->biaya->byPengajuan;
                            $cekKlaim->disetujui = $responseAmbil->biaya->bySetujui;
                        } else {
                            $new = new KlaimCair();
                            $new->no_sep = $responseAmbil->noSEP;
                            $new->tgl_verif = $responseAmbil->tglSep;
                            $new->diajukan = $responseAmbil->biaya->byPengajuan;
                            $new->riil = $responseAmbil->biaya->byTarifRS;
                            $new->disetujui = $responseAmbil->biaya->bySetujui;
                            $new->jenis_rawat = 'RI';
                            $new->save();
                        }
                    }
                }
                // Untuk data Rajal
                $ambil = SepController::getStatusKlaim("$request->periodeBulan-$i", "2", $request->status);
                if ($ambil) {
                    foreach ($ambil as $responseAmbil) {
                        // dd($responseAmbil, 'Rajal');
                        $cekKlaim = KlaimPending::where('no_sep', $responseAmbil->noSEP)->first();
                        if ($cekKlaim) {
                            $cekKlaim->riil = $responseAmbil->biaya->byTarifRS;
                            $cekKlaim->diajukan = $responseAmbil->biaya->byPengajuan;
                            $cekKlaim->disetujui = $responseAmbil->biaya->bySetujui;
                        } else {
                            $new = new KlaimCair();
                            $new->no_sep = $responseAmbil->noSEP;
                            $new->tgl_verif = $responseAmbil->tglSep;
                            $new->diajukan = $responseAmbil->biaya->byPengajuan;
                            $new->riil = $responseAmbil->biaya->byTarifRS;
                            $new->disetujui = $responseAmbil->biaya->bySetujui;
                            $new->jenis_rawat = 'RJ';
                            $new->save();
                        }
                    }
                }
            }
        }

        if ($ambil) {
            Session::flash('sukses', 'Data berhasil diambil');
        }

        return redirect()->back();
    }

    public function lihatDpjpPending(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'DPJP Klaim Pending');
        session()->forget('cucu');
        set_time_limit(0);

        if (isset($request->tanggal)) {
            $tanggal = $request->tanggal;
        } else {
            $tanggal = Carbon::now()->format('Y-m-15');
        }

        // $request->validate([
        //     'tanggal' => 'required|date' // Validasi tanggal (wajib diisi dan harus berupa tanggal yang valid)
        // ]);
        $data = KlaimPending::whereYear('tgl_pulang', Carbon::parse($tanggal)->format('Y'))
            ->whereMonth('tgl_pulang', Carbon::parse($tanggal)->format('m'))
            // ->limit(10)
            ->get();


        foreach ($data as $listData) {
            $alasan = KlaimCompareController::getAlasan($listData->no_sep);
            if ($alasan) {
                $listData->alasan = $alasan;
            } else {
                $listData->alasan = null;
            }

            $dpjp = KlaimCompareController::getDpjp($listData->no_sep);
            if ($dpjp) {
                $listData->dpjp = $dpjp->nmdpdjp ?? $dpjp['nmdpdjp'];
                $listData->no_rawat = $dpjp->no_rawat ?? $dpjp['no_rawat'];
                $listData->nama_pasien = $dpjp->nama_pasien ?? $dpjp['nama_pasien'];
            } else {
                $listData->dpjp = null;
                $listData->no_rawat = null;
                $listData->nama_pasien = null;
            }
        }

        // dd($data);

        return view('vedika.pending_dpjp', compact('data'));
    }

    public function getAlasan($nosep)
    {
        $data = DB::connection('mysqlpayroll')->table('alasan_pendings')
            ->where('no_sep', $nosep)
            ->first();

        if ($data) {
            return $data->alasan;
        } else {
            return null;
        }
    }

    public function getDpjp($nosep)
    {
        $data = DB::connection('mysqlkhanza')->table('bridging_sep')
            ->join('maping_dokter_dpjpvclaim', 'maping_dokter_dpjpvclaim.kd_dokter_bpjs', '=', 'bridging_sep.kddpjp')
            ->select(
                'bridging_sep.no_rawat',
                'bridging_sep.nama_pasien',
                'maping_dokter_dpjpvclaim.nm_dokter_bpjs as nmdpdjp'
            )
            ->where('no_sep', $nosep)
            ->first();

        if ($data) {
            // dd($data);
            return $data;
        } else {
            $cekLokal = sepManual::where('no_sep', $nosep)->first();
            if ($cekLokal) {
                $cekData = DB::connection('mysqlkhanza')->table('reg_periksa')
                    ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                    ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
                    ->select(
                        'reg_periksa.no_rawat',
                        'reg_periksa.status_lanjut',
                        'pasien.nm_pasien',
                        'dokter.nm_dokter'
                    )
                    ->where('no_rawat', $cekLokal->noRawat)
                    ->first();


                if ($cekData && $cekData->status_lanjut == 'Ralan') {
                    $finis = [
                        'no_rawat' => $cekData->no_rawat,
                        'nama_pasien' => $cekData->nm_pasien,
                        'nmdpdjp' => $cekData->nm_dokter
                    ];

                    return $finis;
                } elseif ($cekData && $cekData->status_lanjut == 'Ranap') {
                    $cekDpjp = DB::connection('mysqlkhanza')->table('dpjp_ranap')
                        ->join('dokter', 'dokter.kd_dokter', '=', 'dpjp_ranap.kd_dokter')
                        ->select(
                            'dpjp_ranap.no_rawat',
                            'dokter.nm_dokter'
                        )
                        ->where('no_rawat', $cekLokal->noRawat)
                        ->first();

                    $finis = [
                        'no_rawat' => $cekData->no_rawat,
                        'nama_pasien' => $cekData->nm_pasien,
                        'nmdpdjp' => $cekDpjp->nm_dokter
                    ];

                    return $finis;
                }
            } else {
                return null;
            }
        }
    }
}
