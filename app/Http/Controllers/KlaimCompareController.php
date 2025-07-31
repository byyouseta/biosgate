<?php

namespace App\Http\Controllers;

use App\DataPengajuanKlaim;
use App\Imports\KlaimCairImport;
use App\Imports\TidakLayakImport;
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

        foreach ($data->where('no_sep', null) as $listData) {
            // dd($listData);
            $sepManual = sepManual::where('noRawat', $listData->no_rawat)->first();

            if ($sepManual) {
                $listData->no_sep = $sepManual->no_sep;
            }
        }

        $noRawatList = $data->pluck('no_rawat')->unique();
        $sepList = $data->pluck('no_sep')->unique();

        $sepManualList = sepManual::whereIn('noRawat', $noRawatList)->get()->pluck('no_sep')->unique();
        $gabungSepList = $sepList->merge($sepManualList)->unique()->values();

        // dd($sepList, $sepManualList, $gabungSepList);


        $waktuKeluarMap = DB::connection('mysqlkhanza')->table('kamar_inap')
            ->leftJoin('dpjp_ranap', 'dpjp_ranap.no_rawat', '=', 'kamar_inap.no_rawat')
            ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'dpjp_ranap.kd_dokter')
            ->select(
                'kamar_inap.no_rawat',
                'kamar_inap.tgl_keluar',
                'kamar_inap.jam_keluar',
                DB::raw("CONCAT(kamar_inap.tgl_keluar, ' ', kamar_inap.jam_keluar) AS waktuKeluar"),
                'dokter.nm_dokter'
            )
            ->whereIn('kamar_inap.no_rawat', $noRawatList)
            ->orderBy('waktuKeluar', 'DESC')
            ->get();
        $waktu = $waktuKeluarMap->keyBy('no_rawat');

        $bill = DB::connection('mysqlkhanza')->table('billing')
            ->select('no_rawat', DB::raw('SUM(totalbiaya) as total_biaya'))
            ->whereIn('no_rawat', $noRawatList)
            ->groupBy('no_rawat')
            ->get()
            ->keyBy('no_rawat');

        $cairMap = KlaimCair::whereIn('no_sep', $gabungSepList)->get();
        $cair = $cairMap->pluck('disetujui', 'no_sep');

        $pendingMap = KlaimPending::select('no_sep', 'status', 'biaya_disetujui')
            ->whereIn('no_sep', $gabungSepList)->get();

        $dataPending = $pendingMap->keyBy('no_sep');

        // dd($pendingMap, $dataPending, $cair);

        $kronis = DB::connection('mysqlkhanza')->table('resep_obat')
            ->join('resep_obat_kronis', 'resep_obat_kronis.no_resep', '=', 'resep_obat.no_resep')
            ->select(
                'resep_obat.no_rawat',
                DB::raw("
                    SUM(
                        CASE
                            WHEN resep_obat_kronis.obat_kronis IS NULL OR resep_obat_kronis.obat_kronis = ''
                                THEN 0
                            ELSE
                                CAST(
                                    REPLACE(REPLACE(TRIM(SUBSTRING_INDEX(resep_obat_kronis.obat_kronis, '(', 1)), ',', ''), '.', '')
                                    AS UNSIGNED
                                )
                        END
                    ) AS total_biaya
                ")
            )
            ->whereIn('resep_obat.no_rawat', $noRawatList)
            ->groupBy('resep_obat.no_rawat')
            ->get()
            ->keyBy('no_rawat');

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
            ->orderBy('prosedur_pasien.no_rawat', 'ASC')
            ->orderBy('prosedur_pasien.prioritas', 'ASC')
            ->get();

        $pengajuanMap = DataPengajuanKlaim::whereIn('no_sep', $gabungSepList)->get();
        $dataPengajuan = $pengajuanMap->keyBy('no_sep');

        // dd($dataPengajuan);

        return view('vedika.compare', compact(
            'data',
            'waktu',
            'bill',
            'kronis',
            'cair',
            'dataPending',
            'diagnosa',
            'prosedur',
            'dataPengajuan'
        ));
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

    public function importTidakLayak(Request $request)
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
        $file->move('tidak_layak', $nama_file);

        // // import data
        // Excel::import(new DataEklaimImport, public_path('/eklaim/' . $nama_file));

        // // notifikasi dengan session
        try {
            $data = Excel::toArray(new TidakLayakImport(), public_path('/tidak_layak/' . $nama_file));

            foreach ($data[0] as $dataEklaim) {
                // dd($dataEklaim);
                $cek = KlaimPending::where('no_sep', $dataEklaim['no_sep'])
                    ->first();

                if ($cek) {
                    $cek->status = '4#Klaim Tidak Layak';
                    $cek->save();

                    $cekAlasan = DB::connection('mysqlpayroll')->table('alasan_pendings')
                        ->where('no_sep', $dataEklaim['no_sep'])
                        ->first();

                    if ($cekAlasan) {
                        //Buat update jika ada
                        DB::connection('mysqlpayroll')->table('alasan_pendings')
                            ->where('no_sep', $dataEklaim['no_sep'])
                            ->update([
                                'alasan' => $dataEklaim['alasan'], // Ganti dengan kolom yang ingin diperbarui
                                'updated_at' => now()
                            ]);
                    }
                }
            }

            // notifikasi dengan session
            Session::flash('sukses', 'Data Berhasil Diimport!');
        } catch (\Throwable $th) {
            dd($th);
            // notifikasi dengan session
            Session::flash('error', 'Cek kembali data file Anda!');
        }

        // alihkan halaman kembali
        return redirect()->back();
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

    public function templateTidakLayak()
    {
        //PDF file is stored under project/public/download/info.pdf
        $file = public_path() . "/tidak_layak/template_tidak_layak.xlsx";

        $headers = [
            'Content-Type' => 'application/xlsx',
        ];

        return response()->download($file, 'template_tidak_layak.xlsx', $headers);
    }

    public function ambilResponeVklaim(Request $request)
    {
        set_time_limit(0);

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
                            // exclude klaim tidak layak karena data tidak layak tidak sesuai dengan data excel dari bpjs
                            if ($responseAmbil->status != '4#Klaim Tidak Layak') {
                                $cekKlaim->status = $responseAmbil->status;
                                $cekKlaim->biaya_tarif_rs = $responseAmbil->biaya->byTarifRS;
                                $cekKlaim->biaya_tarif_grouper = $responseAmbil->biaya->byTarifGruper;
                                $cekKlaim->biaya_pengajuan = $responseAmbil->biaya->byPengajuan;
                                $cekKlaim->biaya_disetujui = $responseAmbil->biaya->bySetujui;
                                $cekKlaim->save();
                            }
                        } else {
                            if ($responseAmbil->status != '4#Klaim Tidak Layak') {
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
                }
                // Untuk data Rajal
                $ambil = SepController::getStatusKlaim("$request->periodeBulan-$i", "2", $request->status);
                if ($ambil) {
                    // dd($ambil, 'Rajal');
                    foreach ($ambil as $responseAmbil) {
                        $cekKlaim = KlaimPending::where('no_sep', $responseAmbil->noSEP)->first();
                        if ($cekKlaim) {
                            // exclude klaim tidak layak karena data tidak layak tidak sesuai dengan data excel dari bpjs
                            if ($responseAmbil->status != '4#Klaim Tidak Layak') {
                                $cekKlaim->status = $responseAmbil->status;
                                $cekKlaim->biaya_tarif_rs = $responseAmbil->biaya->byTarifRS;
                                $cekKlaim->biaya_tarif_grouper = $responseAmbil->biaya->byTarifGruper;
                                $cekKlaim->biaya_pengajuan = $responseAmbil->biaya->byPengajuan;
                                $cekKlaim->biaya_disetujui = $responseAmbil->biaya->bySetujui;
                                $cekKlaim->save();
                            }
                        } else {
                            if ($responseAmbil->status != '4#Klaim Tidak Layak') {
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
                            // dd($responseAmbil, $cekKlaim);
                            $cekKlaim->riil = $responseAmbil->biaya->byTarifRS;
                            $cekKlaim->diajukan = $responseAmbil->biaya->byPengajuan;
                            $cekKlaim->disetujui = $responseAmbil->biaya->bySetujui;
                            $cekKlaim->save();
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
                        $cekKlaim = KlaimCair::where('no_sep', $responseAmbil->noSEP)->first();
                        if ($cekKlaim) {
                            $cekKlaim->riil = $responseAmbil->biaya->byTarifRS;
                            $cekKlaim->diajukan = $responseAmbil->biaya->byPengajuan;
                            $cekKlaim->disetujui = $responseAmbil->biaya->bySetujui;
                            $cekKlaim->save();
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

        $request->validate([
            'tanggal' => 'nullable|date' // Validasi tanggal (optional diisi dan harus berupa tanggal yang valid)
        ]);

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
                try {
                    if ($listData->jenis_rawat == 'RJ') {
                        $listData->dpjp = $dpjp->nmdpdjp ?? $dpjp['nmdpdjp'];
                    } elseif ($listData->jenis_rawat == 'RI') {
                        $dokterInap = KlaimCompareController::getDpjpInap($dpjp->no_rawat);
                        if ($dokterInap) {
                            $listData->dpjp = $dokterInap;
                        } else {
                            $listData->dpjp = null;
                        }
                    }
                } catch (\Throwable $th) {
                    // dd($th, $dpjp);
                    Session::flash('error', $th);

                    return redirect()->back();
                }

                $listData->no_rawat = $dpjp->no_rawat ?? $dpjp['no_rawat'];
                $listData->nama_pasien = $dpjp->nama_pasien ?? $dpjp['nama_pasien'];
                $listData->no_rm = $dpjp->no_rkm_medis ?? $dpjp['no_rkm_medis'];
                $listData->drsep = $dpjp->nmdrsep ? $dpjp->nmdrsep : null;
            } else {
                $listData->dpjp = null;
                $listData->no_rawat = null;
                $listData->nama_pasien = null;
                $listData->no_rm = null;
                $listData->drsep = null;
            }
        }

        // dd($data);

        return view('vedika.pending_dpjp', compact('data'));
    }

    public function lihatDpjpGagal(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'DPJP Tidak Layak');
        session()->forget('cucu');
        set_time_limit(0);

        if (isset($request->tanggal)) {
            $tanggal = $request->tanggal;
        } else {
            $tanggal = Carbon::now()->format('Y-m-15');
        }

        $request->validate([
            'tanggal' => 'nullable|date' // Validasi tanggal (wajib diisi dan harus berupa tanggal yang valid)
        ]);

        $data = KlaimPending::whereYear('tgl_pulang', Carbon::parse($tanggal)->format('Y'))
            ->whereMonth('tgl_pulang', Carbon::parse($tanggal)->format('m'))
            ->where('status', '4#Klaim Tidak Layak')
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

        return view('vedika.tidak_layak', compact('data'));
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

    public function getDpjpInap($no_rawat)
    {
        $data = DB::connection('mysqlkhanza')->table('dpjp_ranap')
            ->join('dokter', 'dokter.kd_dokter', '=', 'dpjp_ranap.kd_dokter')
            ->select(
                'dpjp_ranap.*',
                'dokter.nm_dokter'
            )
            ->where('dpjp_ranap.no_rawat', $no_rawat)
            ->orderBy('dpjp_ranap.no_rawat', 'DESC')
            ->first();

        if ($data) {
            return $data->nm_dokter;
        } else {
            return null;
        }
    }

    public function getDpjp($nosep)
    {
        // $data = DB::connection('mysqlkhanza')->table('bridging_sep')
        //     ->join('maping_dokter_dpjpvclaim', 'maping_dokter_dpjpvclaim.kd_dokter_bpjs', '=', 'bridging_sep.kddpjp')
        //     ->select(
        //         'bridging_sep.no_rawat',
        //         'bridging_sep.nama_pasien',
        //         'maping_dokter_dpjpvclaim.nm_dokter_bpjs as nmdpdjp'
        //     )
        //     ->where('no_sep', $nosep)
        //     ->first();
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('bridging_sep', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->leftJoin('maping_dokter_dpjpvclaim', 'maping_dokter_dpjpvclaim.kd_dokter_bpjs', '=', 'bridging_sep.kddpjp')

            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.status_lanjut',
                'bridging_sep.no_rawat',
                'bridging_sep.nama_pasien',
                'dokter.nm_dokter as nmdpdjp',
                'maping_dokter_dpjpvclaim.nm_dokter_bpjs as nmdrsep'
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
                        'reg_periksa.no_rkm_medis',
                        'reg_periksa.status_lanjut',
                        'pasien.nm_pasien',
                        'dokter.nm_dokter'
                    )
                    ->where('no_rawat', $cekLokal->noRawat)
                    ->first();

                $sep = EklaimController::getDetail($cekLokal->no_sep);
                $sep = (object)$sep;

                // dd((object)$sep, $cekData, $cekLokal);

                if ($cekData && $cekData->status_lanjut == 'Ralan') {
                    $finis = [
                        'no_rawat' => $cekData->no_rawat,
                        'no_rkm_medis' => $cekData->no_rkm_medis,
                        'status_lanjut' => $cekData->status_lanjut,
                        'nama_pasien' => $cekData->nm_pasien,
                        'nmdpdjp' => $cekData->nm_dokter,
                        'nmdrsep' => $sep->nama_dokter
                    ];

                    $data2 = (object)$finis;

                    return $data2;
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
                        'no_rkm_medis' => $cekData->no_rkm_medis,
                        'status_lanjut' => $cekData->status_lanjut,
                        'nama_pasien' => $cekData->nm_pasien,
                        'nmdpdjp' => $cekDpjp->nm_dokter,
                        'nmdrsep' => $sep->nama_dokter
                    ];

                    $data2 = (object)$finis;

                    return $data2;
                }
            } else {
                return null;
            }
        }
    }
}
