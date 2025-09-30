<?php

namespace App\Http\Controllers;

use App\BerkasVedika;
use App\DataPengajuanKlaim;
use App\DataPengajuanKronis;
use App\DataPengajuanUlang;
use App\MasterBerkasVedika;
use App\PeriodeKlaim;
use App\PeriodePengajuanUlang;
use App\sepManual;
use App\Setting;
use App\TambahanRadiologi;
use App\Vedika;
use App\VedikaVerif;
use ArrayObject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Error;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File as FileLokal;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use \setasign\Fpdi\Fpdi;
use ZipArchive;

class VedikaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:vedika-list', ['only' => ['rajal', 'billingRajal', 'labRajal', 'radioRajal']]);
    }

    public function rajal(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Rajal');
        session()->forget('cucu');
        set_time_limit(0);

        if (isset($request->tanggalMulai)) {
            $tanggalMulai = $request->tanggalMulai;
            $tanggalSelesai = $request->tanggalSelesai;
        } else {
            $tanggalMulai = Carbon::now()->format('Y-m-d');
            $tanggalSelesai = Carbon::now()->format('Y-m-d');
        }

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
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
                'dokter.nm_dokter'
                // 'diagnosa_pasien.kd_penyakit',
                // 'diagnosa_pasien.prioritas',
                // 'diagnosa_pasien.status',
                // 'penyakit.nm_penyakit'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.tgl_registrasi', '>=', $tanggalMulai)
            ->where('reg_periksa.tgl_registrasi', '<=', $tanggalSelesai)
            ->where('reg_periksa.stts', '!=', 'Batal')
            // ->where('diagnosa_pasien.prioritas', '=', 1)
            // ->where('diagnosa_pasien.status', '=', 'Ralan')
            ->get();

        $noRawatList = $data->pluck('no_rawat')->unique();
        $verifMap = \App\VedikaVerif::whereIn('noRawat', $noRawatList)
            ->where('statusRawat', 'Rajal')->get();
        $statusVerif = $verifMap->keyBy('noRawat');
        $pengajuanMap = \App\DataPengajuanKlaim::whereIn('no_rawat', $noRawatList)
            ->where('jenis_rawat', 'Rawat Jalan')->get();
        $statusPengajuan = $pengajuanMap->keyBy('no_rawat');
        $preOpMap = DB::connection('mysqlkhanza')->table('booking_jadwal_operasi')
            ->whereIn('booking_jadwal_operasi.no_rawat', $noRawatList)
            ->get();
        $cekBokingOp = $preOpMap->keyBy('no_rawat');

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

        // dd($noRawatList, $verifMap, $statusPengajuan, $cekBokingOp, $diagnosa, $prosedur);

        return view('vedika.pasien_rajal', compact(
            'data',
            'statusVerif',
            'statusPengajuan',
            'cekBokingOp',
            'diagnosa',
            'prosedur'
        ));
    }

    public function ranap(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Ranap');
        session()->forget('cucu');

        if (isset($request->tanggalMulai)) {
            $tanggalMulai = $request->tanggalMulai;
            $tanggalSelesai = $request->tanggalSelesai;
        } else {
            $tanggalMulai = Carbon::now()->format('Y-m-d');
            $tanggalSelesai = Carbon::now()->format('Y-m-d');
        }

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('dpjp_ranap', 'dpjp_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'dpjp_ranap.kd_dokter')
            // ->join('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            // ->leftJoin('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
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
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.no_peserta',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'dokter.nm_dokter'
                // 'diagnosa_pasien.kd_penyakit',
                // 'diagnosa_pasien.prioritas',
                // 'diagnosa_pasien.status',
                // 'penyakit.nm_penyakit'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ranap')
            ->where('reg_periksa.tgl_registrasi', '>=', $tanggalMulai)
            ->where('reg_periksa.tgl_registrasi', '<=', $tanggalSelesai)
            // ->where('diagnosa_pasien.status', '=', 'Ranap')
            // ->where('diagnosa_pasien.prioritas', '=', 1)
            ->get();

        // dd($data);

        return view('vedika.pasien_ranap', compact('data'));
    }

    public function detailRajal($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Rajal');
        session()->put('cucu', 'Detail');

        $id = Crypt::decrypt($id);

        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pasien.no_peserta',
                'dokter.nm_dokter',
                'penjab.png_jawab'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        // dd($pasien);

        if ($pasien) {
            //Ambil data billing
            $billing = VedikaController::billingRajal($pasien->no_rawat);
            //Ambil data untuk Bukti Pelayanan
            $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
            $diagnosa = $buktiPelayanan[0];
            $prosedur = $buktiPelayanan[1];
            //Ambil data Radiologi
            $radiologi = VedikaController::radioRajal($pasien->no_rawat);
            dd($radiologi);
            $dataRadiologiRajal = $radiologi[0];
            $dokterRadiologiRajal = $radiologi[1];
            $hasilRadiologiRajal = $radiologi[2];
            $dataSep = VedikaController::getSep($pasien->no_rawat, 2);
            if ($dataSep) {
                if (!empty($dataSep->no_sep)) {
                    $dataDetailEklaim = EklaimController::getDetail($dataSep->no_sep);
                    // dd($dataDetailEklaim);
                } elseif ($dataSep->noSep) {
                    $dataDetailEklaim = EklaimController::getDetail($dataSep->noSep);
                } else {
                    $dataDetailEklaim = null;
                }

                if ($dataDetailEklaim) {
                    $dataKlaim = json_decode(json_encode($dataDetailEklaim));
                } else {
                    $dataKlaim = null;
                }
            } else {
                $dataKlaim = null;
            }

            // dd($dataKlaim, $dataSep);
            //Ambil data Triase dan Ringkasan IGD
            if ($pasien->nm_poli == "IGD") {
                $triase = VedikaController::triase($pasien->no_rawat);
                $dataTriase = $triase[0];
                $primer = $triase[1];
                $sekunder = $triase[2];
                $skala = $triase[3];

                $ringkasan = VedikaController::ringkasanIgd($pasien->no_rawat);
                $dataRingkasan = $ringkasan[0];
                $resumeIgd = $ringkasan[1];
            } else {
                $dataTriase = $primer = $sekunder = $skala = $dataRingkasan = $resumeIgd = null;
            }
            //Ambil Berkas tambahan
            $berkas = VedikaController::berkas($pasien->no_rawat);
            $dataBerkas = $berkas[0];
            $masterBerkas = $berkas[1];
            $pathBerkas = $berkas[2];
            //Data Lab
            $lab = VedikaController::lab($pasien->no_rawat);
            $permintaanLab = $lab[0];
            $hasilLab = $lab[1];
            $kesanLab = $lab[2];
            //Data Obat
            $obat = VedikaController::obat($pasien->no_rawat);
            $resepObat = $obat[0];
            $obatJadi = $obat[1];
            $obatRacik = $obat[2];
            $bbPasien = $obat[3];
            //data SOAP
            $soap = VedikaController::dataSoap($pasien->no_rawat);

            $dataOperasi = VedikaController::OperasiRajal($pasien->no_rawat);
            //Data Pemeriksaan
            $dataRalan = VedikaController::pemeriksaanRalan($pasien->no_rawat, $pasien->kd_dokter);
            // Periode Klaim BPJS
            $periodeKlaim = PeriodeKlaim::where('status', 0)
                ->orderBy('periode', 'DESC')
                ->get();

            // Periode Klaim BPJS Ulang
            $periodePending = PeriodePengajuanUlang::where('status', 0)
                ->orderBy('periode', 'DESC')
                ->get();

            $dataPengajuanPending = DataPengajuanUlang::where('no_rawat', $pasien->no_rawat)
                ->get();

            //Data USG
            $dataUsg = VedikaController::getUsg($pasien->no_rawat);
            // $dataUsgRanap = VedikaController::getUsgRanap($pasien->no_rawat);
            $dataUsgGynecologi = VedikaController::getUsgGynecologi($pasien->no_rawat);

            $dataSpiro = VedikaController::getSpiro($pasien->no_rawat);
            $dataTindakanMata = VedikaController::getLaporanTindakanMata($pasien->no_rawat);
            // dd($dataSpiro);
            // dd($dataKlaim);

            return view('vedika.detailRajal', compact(
                'pasien',
                'billing',
                'dataKlaim',
                'diagnosa',
                'prosedur',
                'permintaanLab',
                'hasilLab',
                'kesanLab',
                'dataRadiologiRajal',
                'dokterRadiologiRajal',
                'hasilRadiologiRajal',
                'dataTriase',
                'primer',
                'sekunder',
                'skala',
                'dataRingkasan',
                'resumeIgd',
                'resepObat',
                'obatJadi',
                'obatRacik',
                'bbPasien',
                'dataBerkas',
                'dataSep',
                'soap',
                'dataUsg',
                'dataUsgGynecologi',
                'dataSpiro',
                'dataTindakanMata',
                'masterBerkas',
                'dataRalan',
                'dataOperasi',
                'periodeKlaim',
                'periodePending',
                'dataPengajuanPending',
                'pathBerkas'
            ));
        } else {
            Session::flash('error', 'Data Pasien tidak ditemukan');
            return redirect()->back();
        }
    }

    public function detailRanap($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Ranap');
        session()->put('cucu', 'Detail');

        $id = Crypt::decrypt($id);

        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->join('dpjp_ranap', 'dpjp_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'dpjp_ranap.kd_dokter')
            ->leftJoin('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->leftJoin('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'kamar.kd_kamar',
                'kamar_inap.tgl_masuk',
                'kamar_inap.jam_masuk',
                'bangsal.nm_bangsal',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pasien.no_peserta',
                'dokter.nm_dokter',
                'penjab.png_jawab',
                DB::raw("CONCAT(kamar_inap.tgl_masuk,' ',kamar_inap.jam_masuk) AS waktu_masuk_ranap")
            )
            // ->select(DB::raw('CONCAT(kamar_inap.tgl_masuk,kamar_inap,jam_masuk) AS waktu_masuk_ranap'))
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ranap')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->orderBy('waktu_masuk_ranap', 'DESC')
            ->first();

        // dd($pasien);
        if ($pasien) {
            //Ambil data billing
            $billing = VedikaController::billingRanap($pasien->no_rawat);
            //Ambil data untuk Bukti Pelayanan
            $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
            $diagnosa = $buktiPelayanan[0];
            $prosedur = $buktiPelayanan[1];
            //Ambil data Radiologi
            $dataRadiologi = VedikaController::radioRanap($pasien->no_rawat);
            $dataRadiologiRanap = $dataRadiologi[0];
            $dokterRadiologiRanap = $dataRadiologi[1];
            $hasilRadiologiRanap = $dataRadiologi[2];
            $radiologi = VedikaController::radioRajal($pasien->no_rawat);
            $dataRadiologiRajal = $radiologi[0];
            $dokterRadiologiRajal = $radiologi[1];
            $hasilRadiologiRajal = $radiologi[2];
            $historyRadiologi = VedikaController::perawatanRadioRajal($pasien->no_rkm_medis);
            $tambahanRadiologi = TambahanRadiologi::where('no_rawat', $pasien->no_rawat)->get();
            // dd($pasien, $dataRadiologiRanap, $dokterRadiologiRanap, $hasilRadiologiRanap);

            if ($tambahanRadiologi) {
                $tambahanDataRadiologi = [];
                $tambahanDokterRadiologi = [];
                $tambahanHasilRadiologi = [];
                foreach ($tambahanRadiologi as $listTambahan) {
                    $dataTambahan = VedikaController::radioRajal($listTambahan->no_rawat_tambahan);
                    // dd($dataTambahan);
                    if (!empty($dataTambahan[0]) && !empty($dataTambahan[1]) && !empty($dataTambahan[2])) {
                        // Menambah data ke array masing-masing
                        array_push($tambahanDataRadiologi, $dataTambahan[0][0]);
                        array_push($tambahanDokterRadiologi, $dataTambahan[1][0]);
                        array_push($tambahanHasilRadiologi, $dataTambahan[2][0]);
                    }
                }
            }

            // dd($tambahanDataRadiologi, $tambahanDokterRadiologi, $tambahanHasilRadiologi, $dataRadiologiRajal);

            $dataSep = VedikaController::getSep($pasien->no_rawat, 1);
            // dd($dataSep);
            if ($dataSep != null) {
                if (!empty($dataSep->no_sep)) {
                    $dataDetailEklaim = EklaimController::getDetail($dataSep->no_sep);
                    // dd($dataDetailEklaim);
                } else {
                    $dataDetailEklaim = EklaimController::getDetail($dataSep->noSep);
                }
                $dataKlaim = json_decode(json_encode($dataDetailEklaim));
            } else {
                $dataKlaim = null;
            }
            // dd($dataSep, $dataKlaim);
            //Ambil Berkas tambahan
            $berkas = VedikaController::berkas($pasien->no_rawat);
            // dd($berkas);

            $dataBerkas = $berkas[0];
            $masterBerkas = $berkas[1];
            $pathBerkas = $berkas[2];
            //Data Lab
            $lab = VedikaController::lab($pasien->no_rawat);
            $permintaanLab = $lab[0];
            $hasilLab = $lab[1];
            $kesanLab = $lab[2];

            // dd($lab);
            //Data Obat
            // $obat = VedikaController::obat($pasien->no_rawat);
            // $resepObat = $obat[0];
            // $obatJadi = $obat[1];
            // $obatRacik = $obat[2];
            // $bbPasien = $obat[3];
            $dataOperasi = VedikaController::OperasiRanap($pasien->no_rawat);
            $dataOperasi2 = $dataOperasi[0];
            $dataOperasi1 = $dataOperasi[1];

            $dataAnestesi = VedikaController::getPrasedasi($pasien->no_rawat);
            $dataAnestesi2 = VedikaController::getPraInduksi($pasien->no_rawat);

            // dd($dataAnestesi, $dataAnestesi2);

            //Data Pemeriksaan
            // $dataRalan = VedikaController::pemeriksaanRalan($pasien->no_rawat, $pasien->kd_dokter);
            //Ambil data Triase dan Ringkasan IGD
            if ($pasien->nm_poli == "IGD") {
                $triase = VedikaController::triase($pasien->no_rawat);
                $dataTriase = $triase[0];
                $primer = $triase[1];
                $sekunder = $triase[2];
                $skala = $triase[3];

                $ringkasan = VedikaController::ringkasanIgd($pasien->no_rawat);
                $dataRingkasan = $ringkasan[0];
                $resumeIgd = $ringkasan[1];
            } else {
                $dataTriase = $primer = $sekunder = $skala = $dataRingkasan = $resumeIgd = null;
            }

            // Periode Klaim BPJS
            $periodeKlaim = PeriodeKlaim::where('status', 0)
                ->orderBy('periode', 'DESC')
                ->get();
            // Periode Klaim BPJS Ulang
            $periodePending = PeriodePengajuanUlang::where('status', 0)
                ->orderBy('periode', 'DESC')
                ->get();

            $dataPengajuanPending = DataPengajuanUlang::where('no_rawat', $pasien->no_rawat)
                ->get();
            //Data SPRI
            $spri = VedikaController::getPerintahRanap($pasien->no_rawat);
            $skor_psi = VedikaController::getPsi($pasien->no_rawat);
            $skor_curb = VedikaController::getCurb($pasien->no_rawat);
            //Data USG
            // $dataUsg = VedikaController::getUsg($pasien->no_rawat);
            $dataUsgRanap = VedikaController::getUsgRanap($pasien->no_rawat);
            // $dataUsgGynecologi = VedikaController::getUsgGynecologi($pasien->no_rawat);
            $dataSpiro = VedikaController::getSpiro($pasien->no_rawat);
            $dataTransfusi = VedikaController::getTransfusi($pasien->no_rawat);
            $dataObserFibri = VedikaController::getObservasiFibrinolitic($pasien->no_rawat);
            $dataChecklistFibri = VedikaController::getCheckFibrinolitic($pasien->no_rawat);
            // dd($dataChecklistFibri);

            //Resume Ranap
            $resumeRanap = VedikaController::getResumeRanap($pasien->no_rawat);
            if ($resumeRanap) {
                $resumeRanap1 = $resumeRanap[0] ? $resumeRanap[0] : null;
                $resumeRanap2 = $resumeRanap[1] ? $resumeRanap[1] : null;
                $resumeRanap3 = $resumeRanap[2] ? $resumeRanap[2] : null;
                $resumeRanap4 = $resumeRanap[3] ? $resumeRanap[3] : null;
            } else {
                $resumeRanap1 = null;
                $resumeRanap2 = null;
                $resumeRanap3 = null;
                $resumeRanap4 = null;
            }


            return view('vedika.detailRanap', compact(
                'pasien',
                'billing',
                'diagnosa',
                'prosedur',
                'permintaanLab',
                'hasilLab',
                'kesanLab',
                'dataRadiologiRanap',
                'dokterRadiologiRanap',
                'hasilRadiologiRanap',
                'dataRadiologiRajal',
                'dokterRadiologiRajal',
                'hasilRadiologiRajal',
                'historyRadiologi',
                'tambahanRadiologi',
                'tambahanDataRadiologi',
                'tambahanDokterRadiologi',
                'tambahanHasilRadiologi',
                'dataTriase',
                'primer',
                'sekunder',
                'skala',
                'dataRingkasan',
                'resumeIgd',
                'skor_psi',
                'skor_curb',
                // 'dataUsg',
                'dataUsgRanap',
                // 'dataUsgGynecologi',
                'dataSpiro',
                'dataTransfusi',
                'dataObserFibri',
                'dataChecklistFibri',
                // 'resepObat',
                // 'obatJadi',
                // 'obatRacik',
                // 'bbPasien',
                'dataBerkas',
                'dataSep',
                'dataKlaim',
                'masterBerkas',
                // 'dataRalan',
                'dataOperasi1',
                'dataOperasi2',
                'dataAnestesi',
                'dataAnestesi2',
                'periodeKlaim',
                'periodePending',
                'dataPengajuanPending',
                'spri',
                'resumeRanap1',
                'resumeRanap2',
                'resumeRanap3',
                'resumeRanap4',
                'pathBerkas'
            ));
        } else {
            Session::flash('error', 'Data Pasien tidak ditemukan');
            return redirect()->back();
        }
    }

    public function detailCronis($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Obat Kronis');
        session()->put('cucu', 'Detail');

        $id = Crypt::decrypt($id);

        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pasien.no_peserta',
                'dokter.nm_dokter',
                'penjab.png_jawab'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        if (empty($pasien)) {
            Session::flash('error', 'Data pasien tidak ditemukan, pastikan data pasien sesuai');
            return redirect()->back();
        }

        //Ambil data billing
        $billing = VedikaController::getBillFarmasi($pasien->no_rawat);
        //Ambil data untuk Bukti Pelayanan
        $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
        $diagnosa = $buktiPelayanan[0];
        $prosedur = $buktiPelayanan[1];
        $dataSep = VedikaController::getSep($pasien->no_rawat, 2);
        // dd($dataSep);
        //Ambil Berkas tambahan
        $berkas = VedikaController::berkas($pasien->no_rawat);
        $dataBerkas = $berkas[0];
        $masterBerkas = $berkas[1];
        $pathBerkas = $berkas[2];
        //Data Obat
        $obat = VedikaController::obat($pasien->no_rawat);
        $resepObat = $obat[0];
        $obatJadi = $obat[1];
        $obatRacik = $obat[2];
        $bbPasien = $obat[3];
        //Data Lab
        $lab = VedikaController::lab($pasien->no_rawat);
        $permintaanLab = $lab[0];
        $hasilLab = $lab[1];
        $kesanLab = $lab[2];
        $dataOperasi = VedikaController::OperasiRajal($pasien->no_rawat);
        //Data Pemeriksaan
        $dataRalan = VedikaController::pemeriksaanRalan($pasien->no_rawat, $pasien->kd_dokter);
        $periodeKlaim = PeriodeKlaim::where('status', 0)
            ->orderBy('periode', 'DESC')
            ->get();

        return view('vedika.detailCronis', compact(
            'pasien',
            'billing',
            'diagnosa',
            'prosedur',
            'resepObat',
            'obatJadi',
            'obatRacik',
            'bbPasien',
            'permintaanLab',
            'hasilLab',
            'kesanLab',
            'dataBerkas',
            'dataSep',
            'masterBerkas',
            'dataRalan',
            'periodeKlaim',
            'pathBerkas'
        ));
    }

    public function detailRajalPdf($id)
    {
        $id = Crypt::decrypt($id);
        // mengambil data id rapat
        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pasien.no_peserta',
                'dokter.nm_dokter',
                'penjab.png_jawab'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        // dd($pasien);

        $dataSep = VedikaController::getSep($pasien->no_rawat, 2);
        if ($dataSep != null) {
            if (!empty($dataSep->no_sep)) {
                $dataDetailEklaim = EklaimController::getDetail($dataSep->no_sep);
                // dd($dataDetailEklaim);
            } else {
                $dataDetailEklaim = EklaimController::getDetail($dataSep->noSep);
            }
            $dataKlaim = json_decode(json_encode($dataDetailEklaim));
        } else {
            $dataKlaim = null;
        }
        //Ambil data billing
        $billing = VedikaController::billingRajal($pasien->no_rawat);
        //Ambil data untuk Bukti Pelayanan
        $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
        $diagnosa = $buktiPelayanan[0];
        $prosedur = $buktiPelayanan[1];
        //Ambil data Radiologi
        $radiologi = VedikaController::radioRajal($pasien->no_rawat);
        $dataRadiologiRajal = $radiologi[0];
        $dokterRadiologiRajal = $radiologi[1];
        $hasilRadiologiRajal = $radiologi[2];
        //Ambil data Triase dan Ringkasan IGD
        if ($pasien->nm_poli == "IGD") {
            $triase = VedikaController::triase($pasien->no_rawat);
            $dataTriase = $triase[0];
            $primer = $triase[1];
            $sekunder = $triase[2];
            $skala = $triase[3];

            $ringkasan = VedikaController::ringkasanIgd($pasien->no_rawat);
            $dataRingkasan = $ringkasan[0];
            $resumeIgd = $ringkasan[1];
        } else {
            $dataTriase = $primer = $sekunder = $skala = $dataRingkasan = $resumeIgd = null;
        }
        //Ambil Berkas tambahan
        $berkas = VedikaController::berkas($pasien->no_rawat);
        $dataBerkas = $berkas[0];
        $masterBerkas = $berkas[1];
        $pathBerkas = $berkas[2];
        //Data Lab
        $lab = VedikaController::lab($pasien->no_rawat);
        $permintaanLab = $lab[0];
        $hasilLab = $lab[1];
        //Data Obat
        $obat = VedikaController::obat($pasien->no_rawat);
        $resepObat = $obat[0];
        $obatJadi = $obat[1];
        $obatRacik = $obat[2];
        $bbPasien = $obat[3];
        //Data Pemeriksaan
        $dataRalan = VedikaController::pemeriksaanRalan($pasien->no_rawat, $pasien->kd_dokter);
        //data SOAP
        $soap = VedikaController::dataSoap($pasien->no_rawat);
        $dataOperasi = VedikaController::OperasiRajal($pasien->no_rawat);
        // dd($primer, $sekunder);
        //Data USG
        $dataUsg = VedikaController::getUsg($pasien->no_rawat);
        // $dataUsgRanap = VedikaController::getUsgRanap($pasien->no_rawat);
        $dataUsgGynecologi = VedikaController::getUsgGynecologi($pasien->no_rawat);
        $dataSpiro = VedikaController::getSpiro($pasien->no_rawat);
        $dataTindakanMata = VedikaController::getLaporanTindakanMata($pasien->no_rawat);

        $pdf = Pdf::loadView('vedika.detailRajal_pdf', [
            'pasien' => $pasien,
            'billing' => $billing,
            'dataSep' => $dataSep,
            'dataKlaim' => $dataKlaim,
            'diagnosa' => $diagnosa,
            'prosedur' => $prosedur,
            'permintaanLab' => $permintaanLab,
            'hasilLab' => $hasilLab,
            'dataRadiologiRajal' => $dataRadiologiRajal,
            'dokterRadiologiRajal' => $dokterRadiologiRajal,
            'hasilRadiologiRajal' => $hasilRadiologiRajal,
            'dataTriase' => $dataTriase,
            'primer' => $primer,
            'sekunder' => $sekunder,
            'skala' => $skala,
            'dataRingkasan' => $dataRingkasan,
            'resumeIgd' => $resumeIgd,
            'resepObat' => $resepObat,
            'obatJadi' => $obatJadi,
            'obatRacik' => $obatRacik,
            'bbPasien' => $bbPasien,
            'dataOperasi' => $dataOperasi,
            // 'masterBerkas' => $masterBerkas,
            'dataRalan' => $dataRalan,
            'dataUsg' => $dataUsg,
            'dataUsgGynecologi' => $dataUsgGynecologi,
            'dataSpiro' => $dataSpiro,
            'dataTindakanMata' => $dataTindakanMata,
            // 'pathBerkas' => $pathBerkas
            'soap' => $soap
        ]);

        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'potraid');

        // $pdf->setOptions(['isRemoteEnabled' => true]);

        // Render the HTML as PDF
        //$pdf->render();
        //Watermark
        // $pdf->setPaper('L');
        // $pdf->output();
        // $canvas = $pdf->getDomPDF()->getCanvas();

        // $height = $canvas->get_height();
        // $width = $canvas->get_width();

        // $canvas->set_opacity(.2, "Multiply");

        // $canvas->set_opacity(.2);

        // $canvas->page_text(
        //     $width / 5,
        //     $height / 2,
        //     'VedikaRSUPGate',
        //     null,
        //     55,
        //     array(0, 0, 0),
        //     2,
        //     2,
        //     -30
        // );

        return $pdf->stream();
    }

    public function downloadRajalPdf($id)
    {
        $id = Crypt::decrypt($id);
        // mengambil data id rapat
        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pasien.no_peserta',
                'dokter.nm_dokter',
                'penjab.png_jawab'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        // dd($pasien);

        $dataSep = VedikaController::getSep($pasien->no_rawat, 2);
        if ($dataSep != null) {
            if (!empty($dataSep->no_sep)) {
                $dataDetailEklaim = EklaimController::getDetail($dataSep->no_sep);
                // dd($dataDetailEklaim);
            } else {
                $dataDetailEklaim = EklaimController::getDetail($dataSep->noSep);
            }
            $dataKlaim = json_decode(json_encode($dataDetailEklaim));
        } else {
            $dataKlaim = null;
        }
        //Ambil data billing
        $billing = VedikaController::billingRajal($pasien->no_rawat);
        //Ambil data untuk Bukti Pelayanan
        $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
        $diagnosa = $buktiPelayanan[0];
        $prosedur = $buktiPelayanan[1];
        //Ambil data Radiologi
        $radiologi = VedikaController::radioRajal($pasien->no_rawat);
        $dataRadiologiRajal = $radiologi[0];
        $dokterRadiologiRajal = $radiologi[1];
        $hasilRadiologiRajal = $radiologi[2];
        //Ambil data Triase dan Ringkasan IGD
        if ($pasien->nm_poli == "IGD") {
            $triase = VedikaController::triase($pasien->no_rawat);
            $dataTriase = $triase[0];
            $primer = $triase[1];
            $sekunder = $triase[2];
            $skala = $triase[3];

            $ringkasan = VedikaController::ringkasanIgd($pasien->no_rawat);
            $dataRingkasan = $ringkasan[0];
            $resumeIgd = $ringkasan[1];
        } else {
            $dataTriase = $primer = $sekunder = $skala = $dataRingkasan = $resumeIgd = null;
        }
        //Ambil Berkas tambahan
        $berkas = VedikaController::berkas($pasien->no_rawat);
        $dataBerkas = $berkas[0];
        $masterBerkas = $berkas[1];
        $pathBerkas = $berkas[2];
        //Data Lab
        $lab = VedikaController::lab($pasien->no_rawat);
        $permintaanLab = $lab[0];
        $hasilLab = $lab[1];
        //Data Obat
        $obat = VedikaController::obat($pasien->no_rawat);
        $resepObat = $obat[0];
        $obatJadi = $obat[1];
        $obatRacik = $obat[2];
        $bbPasien = $obat[3];
        //Data Pemeriksaan
        $dataRalan = VedikaController::pemeriksaanRalan($pasien->no_rawat, $pasien->kd_dokter);
        //data SOAP
        $soap = VedikaController::dataSoap($pasien->no_rawat);
        $dataOperasi = VedikaController::OperasiRajal($pasien->no_rawat);
        // dd($primer, $sekunder);
        //Data USG
        $dataUsg = VedikaController::getUsg($pasien->no_rawat);
        // $dataUsgRanap = VedikaController::getUsgRanap($pasien->no_rawat);
        $dataUsgGynecologi = VedikaController::getUsgGynecologi($pasien->no_rawat);
        $dataSpiro = VedikaController::getSpiro($pasien->no_rawat);
        $dataTindakanMata = VedikaController::getLaporanTindakanMata($pasien->no_rawat);

        $pdf = Pdf::loadView('vedika.detailRajal_pdf', [
            'pasien' => $pasien,
            'billing' => $billing,
            'dataSep' => $dataSep,
            'dataKlaim' => $dataKlaim,
            'diagnosa' => $diagnosa,
            'prosedur' => $prosedur,
            'permintaanLab' => $permintaanLab,
            'hasilLab' => $hasilLab,
            'dataRadiologiRajal' => $dataRadiologiRajal,
            'dokterRadiologiRajal' => $dokterRadiologiRajal,
            'hasilRadiologiRajal' => $hasilRadiologiRajal,
            'dataTriase' => $dataTriase,
            'primer' => $primer,
            'sekunder' => $sekunder,
            'skala' => $skala,
            'dataRingkasan' => $dataRingkasan,
            'resumeIgd' => $resumeIgd,
            'resepObat' => $resepObat,
            'obatJadi' => $obatJadi,
            'obatRacik' => $obatRacik,
            'bbPasien' => $bbPasien,
            'dataOperasi' => $dataOperasi,
            // 'masterBerkas' => $masterBerkas,
            'dataRalan' => $dataRalan,
            'dataUsg' => $dataUsg,
            'dataUsgGynecologi' => $dataUsgGynecologi,
            'dataSpiro' => $dataSpiro,
            'dataTindakanMata' => $dataTindakanMata,
            // 'pathBerkas' => $pathBerkas
            'soap' => $soap
        ]);

        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'potraid');

        if (empty($dataSep->no_sep)) {
            $dataSep->no_sep = $dataSep->noSep;
        }
        // dd($dataSep);

        // Tentukan nama dan path penyimpanan file
        $fileName = "laporan_$dataSep->no_sep" . '.pdf';
        $folderPath = public_path("pdfklaim/$dataSep->no_sep");
        $filePath = $folderPath . '/' . $fileName;

        // Buat folder jika belum ada
        if (!FileLokal::exists($folderPath)) {
            FileLokal::makeDirectory($folderPath, 0755, true);
        }

        // Simpan file PDF ke lokasi tujuan
        $pdf->save($filePath);
        $pdfFiles = [
            public_path("pdfklaim/$dataSep->no_sep/$fileName")
        ];

        // dd('done');
        $berkas = VedikaController::berkas($pasien->no_rawat);
        // dd($berkas);

        $dataBerkas = $berkas[0];

        // dd($dataBerkas);
        if ($dataBerkas) {
            foreach ($dataBerkas as $list) {
                if ($list->kode != '047') {
                    $ambilNama = explode('/upload/', $list->lokasi_file);
                    $filePath = public_path("berkas_vedika/$ambilNama[1]");
                    if (FileLokal::exists($filePath)) {
                        // File exists
                        array_push($pdfFiles, public_path("berkas_vedika/$ambilNama[1]"));
                    } else {
                        // dd('kosong');
                        $cek = Storage::disk('sftp')->exists($list->lokasi_file);
                        if ($cek == true) {
                            // $realFile = explode('/', $id);
                            //Cek file di lokal ada tidak
                            $cek2 = Storage::disk()->exists($ambilNama[1]);
                            if ($cek2 == false) {
                                Storage::disk('local')
                                    ->put("$ambilNama[1]", Storage::disk('sftp')
                                        ->get($list->lokasi_file));
                                $contents = Storage::disk('sftp')->get($list->lokasi_file);
                                file_put_contents("berkas_vedika/$ambilNama[1]", $contents);
                            }
                            array_push($pdfFiles, public_path("berkas_vedika/$ambilNama[1]"));
                        }
                    }
                }
            }
        }

        // dd($pdfFiles);

        $outputFilePath = public_path("pdfklaim/$dataSep->no_sep/$dataSep->no_sep.pdf");

        $gabung = VedikaController::mergePdfs($pdfFiles, $outputFilePath);

        // dd($gabung['success']);

        if (file_exists($outputFilePath) && ($gabung['success'] == TRUE)) {
            // Hapus file laporan digital to pdf
            $hapusLaporan = public_path("pdfklaim/$dataSep->no_sep/laporan_$dataSep->no_sep.pdf");
            if (file_exists($hapusLaporan)) {
                unlink($hapusLaporan);
            }
            // Kembalikan file PDF sebagai response
            return response()->file($outputFilePath);
        } else {
            // Jika file tidak ada, kembalikan error atau pesan yang sesuai
            // abort(404);
            Session::flash('error', $gabung['message']);
            return redirect()->back();
        }
    }

    public function detailRanapPdf($id)
    {
        $id = Crypt::decrypt($id);
        // mengambil data id rapat
        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->join('dpjp_ranap', 'dpjp_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'dpjp_ranap.kd_dokter')
            ->leftJoin('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->leftJoin('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'kamar.kd_kamar',
                'kamar_inap.tgl_masuk',
                'kamar_inap.jam_masuk',
                'bangsal.nm_bangsal',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pasien.no_peserta',
                'dokter.nm_dokter',
                'penjab.png_jawab',
                DB::raw("CONCAT(kamar_inap.tgl_masuk,' ',kamar_inap.jam_masuk) AS waktu_masuk_ranap")
            )
            // ->select(DB::raw('CONCAT(kamar_inap.tgl_masuk,kamar_inap,jam_masuk) AS waktu_masuk_ranap'))
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ranap')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->orderBy('waktu_masuk_ranap', 'DESC')
            ->first();

        // dd($pasien);
        //Ambil data billing
        $billing = VedikaController::billingRanap($pasien->no_rawat);
        //Ambil data untuk Bukti Pelayanan
        $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
        $diagnosa = $buktiPelayanan[0];
        $prosedur = $buktiPelayanan[1];
        //Ambil data Radiologi
        $dataRadiologi = VedikaController::radioRanap($pasien->no_rawat);
        $dataRadiologiRanap = $dataRadiologi[0];
        $dokterRadiologiRanap = $dataRadiologi[1];
        $hasilRadiologiRanap = $dataRadiologi[2];
        $radiologi = VedikaController::radioRajal($pasien->no_rawat);
        $dataRadiologiRajal = $radiologi[0];
        $dokterRadiologiRajal = $radiologi[1];
        $hasilRadiologiRajal = $radiologi[2];
        // dd($dataRadiologiRajal, $hasilRadiologiRajal);
        $tambahanRadiologi = TambahanRadiologi::where('no_rawat', $pasien->no_rawat)->get();
        // dd($tambahanRadiologi, $hasilRadiologiRajal);

        if ($tambahanRadiologi) {
            $tambahanDataRadiologi = [];
            $tambahanDokterRadiologi = [];
            $tambahanHasilRadiologi = [];
            foreach ($tambahanRadiologi as $listTambahan) {
                $dataTambahan = VedikaController::radioRajal($listTambahan->no_rawat_tambahan);
                if (!empty($dataTambahan[0]) && !empty($dataTambahan[1]) && !empty($dataTambahan[2])) {
                    // Menambah data ke array masing-masing
                    array_push($tambahanDataRadiologi, $dataTambahan[0][0]);
                    array_push($tambahanDokterRadiologi, $dataTambahan[1][0]);
                    array_push($tambahanHasilRadiologi, $dataTambahan[2][0]);
                }
            }
        }

        $dataSep = VedikaController::getSep($pasien->no_rawat, 1);
        // dd($dataSep);
        if ($dataSep != null) {
            if (!empty($dataSep->no_sep)) {
                $dataDetailEklaim = EklaimController::getDetail($dataSep->no_sep);
                // dd($dataDetailEklaim);
            } else {
                $dataDetailEklaim = EklaimController::getDetail($dataSep->noSep);
            }
            $dataKlaim = json_decode(json_encode($dataDetailEklaim));
        } else {
            $dataKlaim = null;
        }
        // dd($dataSep, $dataKlaim);

        //Data Lab
        $lab = VedikaController::lab($pasien->no_rawat);
        $permintaanLab = $lab[0];
        $hasilLab = $lab[1];
        $kesanLab = $lab[2];

        // dd($lab);
        //Data Obat
        // $obat = VedikaController::obat($pasien->no_rawat);
        // $resepObat = $obat[0];
        // $obatJadi = $obat[1];
        // $obatRacik = $obat[2];
        // $bbPasien = $obat[3];
        //Operasi
        $dataOperasi = VedikaController::OperasiRanap($pasien->no_rawat);
        $dataOperasi2 = $dataOperasi[0];
        $dataOperasi1 = $dataOperasi[1];

        $dataAnestesi = VedikaController::getPrasedasi($pasien->no_rawat);
        $dataAnestesi2 = VedikaController::getPraInduksi($pasien->no_rawat);
        //Data Pemeriksaan
        // $dataRalan = VedikaController::pemeriksaanRalan($pasien->no_rawat, $pasien->kd_dokter);
        //Ambil data Triase dan Ringkasan IGD
        if ($pasien->nm_poli == "IGD") {
            $triase = VedikaController::triase($pasien->no_rawat);
            $dataTriase = $triase[0];
            $primer = $triase[1];
            $sekunder = $triase[2];
            $skala = $triase[3];

            $ringkasan = VedikaController::ringkasanIgd($pasien->no_rawat);
            $dataRingkasan = $ringkasan[0];
            $resumeIgd = $ringkasan[1];
        } else {
            $dataTriase = $primer = $sekunder = $skala = $dataRingkasan = $resumeIgd = null;
        }
        // Periode Klaim BPJS
        $periodeKlaim = PeriodeKlaim::where('status', 0)
            ->orderBy('periode', 'DESC')
            ->get();
        //Data SPRI
        $spri = VedikaController::getPerintahRanap($pasien->no_rawat);
        $skor_psi = VedikaController::getPsi($pasien->no_rawat);
        $skor_curb = VedikaController::getCurb($pasien->no_rawat);
        //Data USG
        $dataUsg = VedikaController::getUsg($pasien->no_rawat);
        $dataUsgRanap = VedikaController::getUsgRanap($pasien->no_rawat);
        $dataUsgGynecologi = VedikaController::getUsgGynecologi($pasien->no_rawat);

        $dataTransfusi = VedikaController::getTransfusi($pasien->no_rawat);
        $dataObserFibri = VedikaController::getObservasiFibrinolitic($pasien->no_rawat);
        $dataChecklistFibri = VedikaController::getCheckFibrinolitic($pasien->no_rawat);
        //Resume Ranap
        $resumeRanap = VedikaController::getResumeRanap($pasien->no_rawat);
        if ($resumeRanap) {
            $resumeRanap1 = $resumeRanap[0];
            $resumeRanap2 = $resumeRanap[1];
            $resumeRanap3 = $resumeRanap[2];
            $resumeRanap4 = $resumeRanap[3];
        } else {
            $resumeRanap1 = null;
            $resumeRanap2 = null;
            $resumeRanap3 = null;
            $resumeRanap4 = null;
        }

        $pdf = Pdf::loadView('vedika.detailRanap_pdf', [
            'pasien' => $pasien,
            'billing' => $billing,
            'dataSep' => $dataSep,
            'diagnosa' => $diagnosa,
            'prosedur' => $prosedur,
            'permintaanLab' => $permintaanLab,
            'hasilLab' => $hasilLab,
            'kesanLab' => $kesanLab,
            'dataRadiologiRajal' => $dataRadiologiRajal,
            'dokterRadiologiRajal' => $dokterRadiologiRajal,
            'hasilRadiologiRajal' => $hasilRadiologiRajal,
            'dataRadiologiRanap' => $dataRadiologiRanap,
            'dokterRadiologiRanap' => $dokterRadiologiRanap,
            'hasilRadiologiRanap' => $hasilRadiologiRanap,
            'tambahanDataRadiologi' => $tambahanDataRadiologi,
            'tambahanDokterRadiologi' => $tambahanDokterRadiologi,
            'tambahanHasilRadiologi' => $tambahanHasilRadiologi,
            'dataUsg' => $dataUsg,
            'dataUsgRanap' => $dataUsgRanap,
            'dataUsgGynecologi' => $dataUsgGynecologi,
            'dataTransfusi' => $dataTransfusi,
            'dataObserFibri' => $dataObserFibri,
            'dataChecklistFibri' => $dataChecklistFibri,
            'dataTriase' => $dataTriase,
            'primer' => $primer,
            'sekunder' => $sekunder,
            'skala' => $skala,
            'dataRingkasan' => $dataRingkasan,
            'resumeIgd' => $resumeIgd,
            'skor_psi' => $skor_psi,
            'skor_curb' => $skor_curb,
            'dataKlaim' => $dataKlaim,
            'spri' => $spri,
            'dataOperasi1' => $dataOperasi1,
            'dataOperasi2' => $dataOperasi2,
            'dataAnestesi' => $dataAnestesi,
            'dataAnestesi2' => $dataAnestesi2,
            'resumeRanap1' => $resumeRanap1,
            'resumeRanap2' => $resumeRanap2,
            'resumeRanap3' => $resumeRanap3,
            'resumeRanap4' => $resumeRanap4
            // 'resepObat' => $resepObat,
            // 'obatJadi' => $obatJadi,
            // 'obatRacik' => $obatRacik,
            // 'bbPasien' => $bbPasien
        ]);

        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'potraid');

        return $pdf->stream();
    }

    public function downloadRanapPdf($id)
    {
        $id = Crypt::decrypt($id);
        // mengambil data id rapat
        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->join('dpjp_ranap', 'dpjp_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'dpjp_ranap.kd_dokter')
            ->leftJoin('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->leftJoin('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'kamar.kd_kamar',
                'kamar_inap.tgl_masuk',
                'kamar_inap.jam_masuk',
                'bangsal.nm_bangsal',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pasien.no_peserta',
                'dokter.nm_dokter',
                'penjab.png_jawab',
                DB::raw("CONCAT(kamar_inap.tgl_masuk,' ',kamar_inap.jam_masuk) AS waktu_masuk_ranap")
            )
            // ->select(DB::raw('CONCAT(kamar_inap.tgl_masuk,kamar_inap,jam_masuk) AS waktu_masuk_ranap'))
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ranap')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->orderBy('waktu_masuk_ranap', 'DESC')
            ->first();

        // dd($pasien);
        //Ambil data billing
        $billing = VedikaController::billingRanap($pasien->no_rawat);
        //Ambil data untuk Bukti Pelayanan
        $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
        $diagnosa = $buktiPelayanan[0];
        $prosedur = $buktiPelayanan[1];
        //Ambil data Radiologi
        $dataRadiologi = VedikaController::radioRanap($pasien->no_rawat);
        $dataRadiologiRanap = $dataRadiologi[0];
        $dokterRadiologiRanap = $dataRadiologi[1];
        $hasilRadiologiRanap = $dataRadiologi[2];
        $radiologi = VedikaController::radioRajal($pasien->no_rawat);
        $dataRadiologiRajal = $radiologi[0];
        $dokterRadiologiRajal = $radiologi[1];
        $hasilRadiologiRajal = $radiologi[2];
        // dd($dataRadiologiRajal, $hasilRadiologiRajal);
        $tambahanRadiologi = TambahanRadiologi::where('no_rawat', $pasien->no_rawat)->get();
        // dd($tambahanRadiologi, $hasilRadiologiRajal);

        if ($tambahanRadiologi) {
            $tambahanDataRadiologi = [];
            $tambahanDokterRadiologi = [];
            $tambahanHasilRadiologi = [];
            foreach ($tambahanRadiologi as $listTambahan) {
                $dataTambahan = VedikaController::radioRajal($listTambahan->no_rawat_tambahan);
                if (!empty($dataTambahan[0]) && !empty($dataTambahan[1]) && !empty($dataTambahan[2])) {
                    // Menambah data ke array masing-masing
                    array_push($tambahanDataRadiologi, $dataTambahan[0][0]);
                    array_push($tambahanDokterRadiologi, $dataTambahan[1][0]);
                    array_push($tambahanHasilRadiologi, $dataTambahan[2][0]);
                }
            }
        }

        $dataSep = VedikaController::getSep($pasien->no_rawat, 1);
        if ($dataSep != null) {
            if (!empty($dataSep->no_sep)) {
                $dataDetailEklaim = EklaimController::getDetail($dataSep->no_sep);
                // dd($dataDetailEklaim);
            } else {
                $dataDetailEklaim = EklaimController::getDetail($dataSep->noSep);
            }
            $dataKlaim = json_decode(json_encode($dataDetailEklaim));
        } else {
            $dataKlaim = null;
        }
        // dd($dataSep, $dataKlaim);

        //Data Lab
        $lab = VedikaController::lab($pasien->no_rawat);
        $permintaanLab = $lab[0];
        $hasilLab = $lab[1];
        $kesanLab = $lab[2];

        // dd($lab);
        //Data Obat
        // $obat = VedikaController::obat($pasien->no_rawat);
        // $resepObat = $obat[0];
        // $obatJadi = $obat[1];
        // $obatRacik = $obat[2];
        // $bbPasien = $obat[3];
        //Operasi
        $dataOperasi = VedikaController::OperasiRanap($pasien->no_rawat);
        $dataOperasi2 = $dataOperasi[0];
        $dataOperasi1 = $dataOperasi[1];

        $dataAnestesi = VedikaController::getPrasedasi($pasien->no_rawat);
        $dataAnestesi2 = VedikaController::getPraInduksi($pasien->no_rawat);
        //Ambil data Triase dan Ringkasan IGD
        if ($pasien->nm_poli == "IGD") {
            $triase = VedikaController::triase($pasien->no_rawat);
            $dataTriase = $triase[0];
            $primer = $triase[1];
            $sekunder = $triase[2];
            $skala = $triase[3];

            $ringkasan = VedikaController::ringkasanIgd($pasien->no_rawat);
            $dataRingkasan = $ringkasan[0];
            $resumeIgd = $ringkasan[1];
        } else {
            $dataTriase = $primer = $sekunder = $skala = $dataRingkasan = $resumeIgd = null;
        }
        // Periode Klaim BPJS
        $periodeKlaim = PeriodeKlaim::where('status', 0)
            ->orderBy('periode', 'DESC')
            ->get();
        //Data SPRI
        $spri = VedikaController::getPerintahRanap($pasien->no_rawat);
        $skor_psi = VedikaController::getPsi($pasien->no_rawat);
        $skor_curb = VedikaController::getCurb($pasien->no_rawat);
        //Data USG
        $dataUsg = VedikaController::getUsg($pasien->no_rawat);
        $dataUsgRanap = VedikaController::getUsgRanap($pasien->no_rawat);
        $dataUsgGynecologi = VedikaController::getUsgGynecologi($pasien->no_rawat);

        $dataTransfusi = VedikaController::getTransfusi($pasien->no_rawat);
        $dataObserFibri = VedikaController::getObservasiFibrinolitic($pasien->no_rawat);
        $dataChecklistFibri = VedikaController::getCheckFibrinolitic($pasien->no_rawat);
        //Resume Ranap
        $resumeRanap = VedikaController::getResumeRanap($pasien->no_rawat);
        if ($resumeRanap) {
            $resumeRanap1 = $resumeRanap[0];
            $resumeRanap2 = $resumeRanap[1];
            $resumeRanap3 = $resumeRanap[2];
            $resumeRanap4 = $resumeRanap[3];
        } else {
            $resumeRanap1 = null;
            $resumeRanap2 = null;
            $resumeRanap3 = null;
            $resumeRanap4 = null;
        }


        $pdf = Pdf::loadView('vedika.detailRanap_pdf', [
            'pasien' => $pasien,
            'billing' => $billing,
            'dataSep' => $dataSep,
            'diagnosa' => $diagnosa,
            'prosedur' => $prosedur,
            'permintaanLab' => $permintaanLab,
            'hasilLab' => $hasilLab,
            'kesanLab' => $kesanLab,
            'dataRadiologiRajal' => $dataRadiologiRajal,
            'dokterRadiologiRajal' => $dokterRadiologiRajal,
            'hasilRadiologiRajal' => $hasilRadiologiRajal,
            'dataRadiologiRanap' => $dataRadiologiRanap,
            'dokterRadiologiRanap' => $dokterRadiologiRanap,
            'hasilRadiologiRanap' => $hasilRadiologiRanap,
            'tambahanDataRadiologi' => $tambahanDataRadiologi,
            'tambahanDokterRadiologi' => $tambahanDokterRadiologi,
            'tambahanHasilRadiologi' => $tambahanHasilRadiologi,
            'dataUsg' => $dataUsg,
            'dataUsgRanap' => $dataUsgRanap,
            'dataUsgGynecologi' => $dataUsgGynecologi,
            'dataTransfusi' => $dataTransfusi,
            'dataObserFibri' => $dataObserFibri,
            'dataChecklistFibri' => $dataChecklistFibri,
            'dataTriase' => $dataTriase,
            'primer' => $primer,
            'sekunder' => $sekunder,
            'skala' => $skala,
            'dataRingkasan' => $dataRingkasan,
            'resumeIgd' => $resumeIgd,
            'skor_psi' => $skor_psi,
            'skor_curb' => $skor_curb,
            'dataKlaim' => $dataKlaim,
            'spri' => $spri,
            'dataOperasi1' => $dataOperasi1,
            'dataOperasi2' => $dataOperasi2,
            'dataAnestesi' => $dataAnestesi,
            'dataAnestesi2' => $dataAnestesi2,
            'resumeRanap1' => $resumeRanap1,
            'resumeRanap2' => $resumeRanap2,
            'resumeRanap3' => $resumeRanap3,
            'resumeRanap4' => $resumeRanap4
            // 'resepObat' => $resepObat,
            // 'obatJadi' => $obatJadi,
            // 'obatRacik' => $obatRacik,
            // 'bbPasien' => $bbPasien
        ]);

        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'potraid');

        if (empty($dataSep->no_sep)) {
            $dataSep->no_sep = $dataSep->noSep;
        }
        // dd($dataSep);

        // Tentukan nama dan path penyimpanan file
        $fileName = "laporan_$dataSep->no_sep" . '.pdf';
        $folderPath = public_path("pdfklaim/$dataSep->no_sep");
        $filePath = $folderPath . '/' . $fileName;

        // Buat folder jika belum ada
        if (!FileLokal::exists($folderPath)) {
            FileLokal::makeDirectory($folderPath, 0755, true);
        }

        // Simpan file PDF ke lokasi tujuan
        $pdf->save($filePath);
        $pdfFiles = [
            public_path("pdfklaim/$dataSep->no_sep/$fileName")
        ];

        // dd('done');
        $berkas = VedikaController::berkas($pasien->no_rawat);
        // dd($berkas);

        $dataBerkas = $berkas[0];

        // dd($dataBerkas);
        if ($dataBerkas) {
            foreach ($dataBerkas as $list) {
                if ($list->kode != '047') {
                    $ambilNama = explode('/upload/', $list->lokasi_file);
                    $filePath = public_path("berkas_vedika/$ambilNama[1]");
                    if (FileLokal::exists($filePath)) {
                        // File exists
                        array_push($pdfFiles, public_path("berkas_vedika/$ambilNama[1]"));
                    } else {
                        // dd('kosong');
                        $cek = Storage::disk('sftp')->exists($list->lokasi_file);
                        if ($cek == true) {
                            // $realFile = explode('/', $id);
                            //Cek file di lokal ada tidak
                            $cek2 = Storage::disk()->exists($ambilNama[1]);
                            if ($cek2 == false) {
                                Storage::disk('local')
                                    ->put("$ambilNama[1]", Storage::disk('sftp')
                                        ->get($list->lokasi_file));
                                $contents = Storage::disk('sftp')->get($list->lokasi_file);
                                file_put_contents("berkas_vedika/$ambilNama[1]", $contents);
                            }
                            array_push($pdfFiles, public_path("berkas_vedika/$ambilNama[1]"));
                        }
                    }
                }
            }
        }

        // dd($pdfFiles);

        $outputFilePath = public_path("pdfklaim/$dataSep->no_sep/$dataSep->no_sep.pdf");

        $gabung = VedikaController::mergePdfs($pdfFiles, $outputFilePath);

        // dd('selesai');

        if (file_exists($outputFilePath) && ($gabung['success'] == TRUE)) {
            // Hapus file laporan digital to pdf
            $hapusLaporan = public_path("pdfklaim/$dataSep->no_sep/laporan_$dataSep->no_sep.pdf");
            if (file_exists($hapusLaporan)) {
                unlink($hapusLaporan);
            }
            // Kembalikan file PDF sebagai response
            return response()->file($outputFilePath);
        } else {
            // Jika file tidak ada, kembalikan error atau pesan yang sesuai
            // abort(404);
            Session::flash('error', $gabung['message']);
            return redirect()->back();
        }
    }

    public function gabungKronisPdf($id)
    {
        $id = Crypt::decrypt($id);
        // mengambil data id rapat
        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pasien.no_peserta',
                'dokter.nm_dokter',
                'penjab.png_jawab'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        // dd($pasien);

        //Ambil data billing
        // $billing = VedikaController::billingRajal($pasien->no_rawat);
        $billing = VedikaController::getBillFarmasi($pasien->no_rawat);
        //Ambil data untuk Bukti Pelayanan
        $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
        $diagnosa = $buktiPelayanan[0];
        $prosedur = $buktiPelayanan[1];
        //Ambil data Radiologi
        $radiologi = VedikaController::radioRajal($pasien->no_rawat);
        $dataRadiologiRajal = $radiologi[0];
        $dokterRadiologiRajal = $radiologi[1];
        $hasilRadiologiRajal = $radiologi[2];
        //Ambil data Triase dan Ringkasan IGD
        if ($pasien->nm_poli == "IGD") {
            $triase = VedikaController::triase($pasien->no_rawat);
            $dataTriase = $triase[0];
            $primer = $triase[1];
            $sekunder = $triase[2];
            $skala = $triase[3];

            $ringkasan = VedikaController::ringkasanIgd($pasien->no_rawat);
            $dataRingkasan = $ringkasan[0];
            $resumeIgd = $ringkasan[1];
        } else {
            $dataTriase = $primer = $sekunder = $skala = $dataRingkasan = $resumeIgd = null;
        }
        //Ambil Berkas tambahan
        $berkas = VedikaController::berkas($pasien->no_rawat);
        $dataBerkas = $berkas[0];
        $masterBerkas = $berkas[1];
        $pathBerkas = $berkas[2];
        //Data Lab
        $lab = VedikaController::lab($pasien->no_rawat);
        $permintaanLab = $lab[0];
        $hasilLab = $lab[1];
        //Data Obat
        $obat = VedikaController::obat($pasien->no_rawat);
        $resepObat = $obat[0];
        $obatJadi = $obat[1];
        $obatRacik = $obat[2];
        $bbPasien = $obat[3];
        //Data Pemeriksaan
        $dataRalan = VedikaController::pemeriksaanRalan($pasien->no_rawat, $pasien->kd_dokter);
        // dd($obat, $resepObat);

        $pdf = Pdf::loadView('vedika.cronisRajal_pdf', [
            'pasien' => $pasien,
            'billing' => $billing,
            'diagnosa' => $diagnosa,
            'prosedur' => $prosedur,
            'permintaanLab' => $permintaanLab,
            'hasilLab' => $hasilLab,
            'dataRadiologiRajal' => $dataRadiologiRajal,
            'dokterRadiologiRajal' => $dokterRadiologiRajal,
            'hasilRadiologiRajal' => $hasilRadiologiRajal,
            'dataTriase' => $dataTriase,
            'primer' => $primer,
            'sekunder' => $sekunder,
            'skala' => $skala,
            'dataRingkasan' => $dataRingkasan,
            'resumeIgd' => $resumeIgd,
            'resepObat' => $resepObat,
            'obatJadi' => $obatJadi,
            'obatRacik' => $obatRacik,
            'bbPasien' => $bbPasien,
            'dataBerkas' => $dataBerkas,
            'masterBerkas' => $masterBerkas,
            'dataRalan' => $dataRalan,
            'pathBerkas' => $pathBerkas
        ]);

        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'potraid');

        //Get No Resep
        $getPengajuan = DataPengajuanKronis::where('no_rawat', $id)
            ->first();
        $periode = Carbon::parse($getPengajuan->periodeKlaim->periode)->format('Ym');

        // Tentukan nama dan path penyimpanan file
        $fileName = "laporan_$getPengajuan->no_resep" . '.pdf';
        $folderPath = public_path("pdfkronis/$periode");
        $filePath = $folderPath . '/' . $fileName;

        // Buat folder jika belum ada
        if (!FileLokal::exists($folderPath)) {
            FileLokal::makeDirectory($folderPath, 0755, true);
        }

        // Simpan file PDF ke lokasi tujuan
        $pdf->save($filePath);
        $pdfFiles = [
            public_path("pdfkronis/$periode/$fileName")
        ];

        // dd('done');
        $berkas = VedikaController::berkas($pasien->no_rawat);
        // dd($berkas);

        $dataBerkas = $berkas[0];

        // dd($dataBerkas);
        if ($dataBerkas) {
            foreach ($dataBerkas as $list) {
                if ($list->kode == '047') {
                    $ambilNama = explode('/upload/', $list->lokasi_file);
                    $filePath = public_path("berkas_vedika/$ambilNama[1]");
                    if (FileLokal::exists($filePath)) {
                        // File exists
                        array_push($pdfFiles, public_path("berkas_vedika/$ambilNama[1]"));
                    } else {
                        // dd('kosong');
                        $cek = Storage::disk('sftp')->exists($list->lokasi_file);
                        if ($cek == true) {
                            // $realFile = explode('/', $id);
                            //Cek file di lokal ada tidak
                            $cek2 = Storage::disk()->exists($ambilNama[1]);
                            if ($cek2 == false) {
                                Storage::disk('local')
                                    ->put("$ambilNama[1]", Storage::disk('sftp')
                                        ->get($list->lokasi_file));
                                $contents = Storage::disk('sftp')->get($list->lokasi_file);
                                file_put_contents("berkas_vedika/$ambilNama[1]", $contents);
                            }
                            array_push($pdfFiles, public_path("berkas_vedika/$ambilNama[1]"));
                        }
                    }
                }
            }
        }

        // dd($pdfFiles);

        $outputFilePath = public_path("pdfkronis/$periode/$getPengajuan->no_resep" . '.pdf');

        $gabung = VedikaController::mergePdfs($pdfFiles, $outputFilePath);

        // dd($gabung['success']);

        if (file_exists($outputFilePath) && ($gabung['success'] == TRUE)) {
            // Hapus file laporan digital to pdf
            $hapusLaporan = public_path("pdfkronis/$periode/laporan_$getPengajuan->no_resep.pdf");
            if (file_exists($hapusLaporan)) {
                unlink($hapusLaporan);
            }
            // Kembalikan file PDF sebagai response
            return response()->file($outputFilePath);
        } else {
            // Jika file tidak ada, kembalikan error atau pesan yang sesuai
            // abort(404);
            Session::flash('error', $gabung['message']);
            return redirect()->back();
        }
    }

    public function viewGabungPdf($no_sep)
    {
        $no_sep = Crypt::decrypt($no_sep);
        // dd($no_sep);

        $outputFilePath = public_path("pdfklaim/$no_sep/$no_sep.pdf");

        if (file_exists($outputFilePath)) {
            // Kembalikan file PDF sebagai response
            return response()->file($outputFilePath);
        } else {
            // Jika file tidak ada, kembalikan error atau pesan yang sesuai
            Session::flash('error', 'File gabung tidak ditemukan!');
            return redirect()->back();
        }
    }

    public function lihatKronisPdf($no_rawat)
    {
        $no_rawat = Crypt::decrypt($no_rawat);
        // dd($no_rawat);
        //Get No Resep
        $getPengajuan = DataPengajuanKronis::where('no_rawat', $no_rawat)
            ->first();

        // dd($getPengajuan);
        $periode = Carbon::parse($getPengajuan->periodeKlaim->periode)->format('Ym');

        $outputFilePath = public_path("pdfkronis/$periode/$getPengajuan->no_resep.pdf");
        // dd($outputFilePath);


        if (file_exists($outputFilePath)) {
            // Kembalikan file PDF sebagai response
            return response()->file($outputFilePath);
        } else {
            // Jika file tidak ada, kembalikan error atau pesan yang sesuai
            Session::flash('error', 'File gabung tidak ditemukan!');
            return redirect()->back();
        }
    }

    public function deletePdf($no_sep)
    {
        $no_sep = Crypt::decrypt($no_sep);
        $outputFilePath = public_path("pdfklaim/$no_sep/$no_sep.pdf");

        if (file_exists($outputFilePath)) {
            unlink($outputFilePath);
            Session::flash('success', 'File gabung berhasil dihapus!');
        } else {
            Session::flash('error', 'File gabung tidak ditemukan!');
        }

        return redirect()->back();
    }

    public function gabungRanap($no_rawat)
    {
        // mengambil data
        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->join('dpjp_ranap', 'dpjp_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'dpjp_ranap.kd_dokter')
            ->leftJoin('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->leftJoin('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'kamar.kd_kamar',
                'kamar_inap.tgl_masuk',
                'kamar_inap.jam_masuk',
                'bangsal.nm_bangsal',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pasien.no_peserta',
                'dokter.nm_dokter',
                'penjab.png_jawab',
                DB::raw("CONCAT(kamar_inap.tgl_masuk,' ',kamar_inap.jam_masuk) AS waktu_masuk_ranap")
            )
            // ->select(DB::raw('CONCAT(kamar_inap.tgl_masuk,kamar_inap,jam_masuk) AS waktu_masuk_ranap'))
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ranap')
            ->where('reg_periksa.no_rawat', '=', $no_rawat)
            ->orderBy('waktu_masuk_ranap', 'DESC')
            ->first();


        //Ambil data billing
        $billing = VedikaController::billingRanap($pasien->no_rawat);
        //Ambil data untuk Bukti Pelayanan
        $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
        $diagnosa = $buktiPelayanan[0];
        $prosedur = $buktiPelayanan[1];
        //Ambil data Radiologi
        $dataRadiologi = VedikaController::radioRanap($pasien->no_rawat);
        $dataRadiologiRanap = $dataRadiologi[0];
        $dokterRadiologiRanap = $dataRadiologi[1];
        $hasilRadiologiRanap = $dataRadiologi[2];
        $radiologi = VedikaController::radioRajal($pasien->no_rawat);
        $dataRadiologiRajal = $radiologi[0];
        $dokterRadiologiRajal = $radiologi[1];
        $hasilRadiologiRajal = $radiologi[2];

        $dataSep = VedikaController::getSep($pasien->no_rawat, 1);
        if ($dataSep != null) {
            if (!empty($dataSep->no_sep)) {
                $dataDetailEklaim = EklaimController::getDetail($dataSep->no_sep);
                // dd($dataDetailEklaim);
            } else {
                $dataDetailEklaim = EklaimController::getDetail($dataSep->noSep);
            }
            $dataKlaim = json_decode(json_encode($dataDetailEklaim));
        } else {
            $dataKlaim = null;
        }

        //Data Lab
        $lab = VedikaController::lab($pasien->no_rawat);
        $permintaanLab = $lab[0];
        $hasilLab = $lab[1];
        $kesanLab = $lab[2];

        //Data Obat
        // $obat = VedikaController::obat($pasien->no_rawat);
        // $resepObat = $obat[0];
        // $obatJadi = $obat[1];
        // $obatRacik = $obat[2];
        // $bbPasien = $obat[3];
        //Operasi
        $dataOperasi = VedikaController::OperasiRanap($pasien->no_rawat);
        $dataOperasi2 = $dataOperasi[0];
        $dataOperasi1 = $dataOperasi[1];
        //Ambil data Triase dan Ringkasan IGD
        if ($pasien->nm_poli == "IGD") {
            $triase = VedikaController::triase($pasien->no_rawat);
            $dataTriase = $triase[0];
            $primer = $triase[1];
            $sekunder = $triase[2];
            $skala = $triase[3];

            $ringkasan = VedikaController::ringkasanIgd($pasien->no_rawat);
            $dataRingkasan = $ringkasan[0];
            $resumeIgd = $ringkasan[1];
        } else {
            $dataTriase = $primer = $sekunder = $skala = $dataRingkasan = $resumeIgd = null;
        }
        // Periode Klaim BPJS
        $periodeKlaim = PeriodeKlaim::where('status', 0)
            ->orderBy('periode', 'DESC')
            ->get();
        //Data SPRI
        $spri = VedikaController::getPerintahRanap($pasien->no_rawat);
        $skor_psi = VedikaController::getPsi($pasien->no_rawat);
        $skor_curb = VedikaController::getCurb($pasien->no_rawat);
        //Data USG
        $dataUsg = VedikaController::getUsg($pasien->no_rawat);
        $dataUsgRanap = VedikaController::getUsgRanap($pasien->no_rawat);
        $dataUsgGynecologi = VedikaController::getUsgGynecologi($pasien->no_rawat);

        $dataTransfusi = VedikaController::getTransfusi($pasien->no_rawat);
        $dataObserFibri = VedikaController::getObservasiFibrinolitic($pasien->no_rawat);
        $dataChecklistFibri = VedikaController::getCheckFibrinolitic($pasien->no_rawat);
        //Resume Ranap
        $resumeRanap = VedikaController::getResumeRanap($pasien->no_rawat);
        if ($resumeRanap) {
            $resumeRanap1 = $resumeRanap[0];
            $resumeRanap2 = $resumeRanap[1];
            $resumeRanap3 = $resumeRanap[2];
            $resumeRanap4 = $resumeRanap[3];
        } else {
            $resumeRanap1 = null;
            $resumeRanap2 = null;
            $resumeRanap3 = null;
            $resumeRanap4 = null;
        }

        $pdf = Pdf::loadView('vedika.detailRanap_pdf', [
            'pasien' => $pasien,
            'billing' => $billing,
            'dataSep' => $dataSep,
            'diagnosa' => $diagnosa,
            'prosedur' => $prosedur,
            'permintaanLab' => $permintaanLab,
            'hasilLab' => $hasilLab,
            'kesanLab' => $kesanLab,
            'dataRadiologiRajal' => $dataRadiologiRajal,
            'dokterRadiologiRajal' => $dokterRadiologiRajal,
            'hasilRadiologiRajal' => $hasilRadiologiRajal,
            'dataRadiologiRanap' => $dataRadiologiRanap,
            'dokterRadiologiRanap' => $dokterRadiologiRanap,
            'hasilRadiologiRanap' => $hasilRadiologiRanap,
            'dataTriase' => $dataTriase,
            'primer' => $primer,
            'sekunder' => $sekunder,
            'skala' => $skala,
            'dataRingkasan' => $dataRingkasan,
            'resumeIgd' => $resumeIgd,
            'dataKlaim' => $dataKlaim,
            'spri' => $spri,
            'skor_psi' => $skor_psi,
            'skor_curb' => $skor_curb,
            'dataUsg' => $dataUsg,
            'dataUsgRanap' => $dataUsgRanap,
            'dataUsgGynecologi' => $dataUsgGynecologi,
            'dataTransfusi' => $dataTransfusi,
            'dataObserFibri' => $dataObserFibri,
            'dataChecklistFibri' => $dataChecklistFibri,
            'dataOperasi1' => $dataOperasi1,
            'dataOperasi2' => $dataOperasi2,
            'resumeRanap1' => $resumeRanap1,
            'resumeRanap2' => $resumeRanap2,
            'resumeRanap3' => $resumeRanap3,
            'resumeRanap4' => $resumeRanap4
            // 'resepObat' => $resepObat,
            // 'obatJadi' => $obatJadi,
            // 'obatRacik' => $obatRacik,
            // 'bbPasien' => $bbPasien
        ]);

        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'potraid');

        if (empty($dataSep->no_sep)) {
            $dataSep->no_sep = $dataSep->noSep;
        }

        // Tentukan nama dan path penyimpanan file
        $fileName = "laporan_$dataSep->no_sep" . '.pdf';
        $folderPath = public_path("pdfklaim/$dataSep->no_sep");
        $filePath = $folderPath . '/' . $fileName;

        // Buat folder jika belum ada
        if (!FileLokal::exists($folderPath)) {
            FileLokal::makeDirectory($folderPath, 0755, true);
        }

        // Simpan file PDF ke lokasi tujuan
        $pdf->save($filePath);
        $pdfFiles = [
            public_path("pdfklaim/$dataSep->no_sep/$fileName")
        ];

        $berkas = VedikaController::berkas($pasien->no_rawat);

        $dataBerkas = $berkas[0];

        if ($dataBerkas) {
            foreach ($dataBerkas as $list) {
                $ambilNama = explode('/upload/', $list->lokasi_file);
                $filePath = public_path("berkas_vedika/$ambilNama[1]");
                if (FileLokal::exists($filePath)) {
                    // File exists
                    array_push($pdfFiles, public_path("berkas_vedika/$ambilNama[1]"));
                } else {
                    $cek = Storage::disk('sftp')->exists($list->lokasi_file);
                    if ($cek == true) {
                        // $realFile = explode('/', $id);
                        //Cek file di lokal ada tidak
                        $cek2 = Storage::disk()->exists($ambilNama[1]);
                        if ($cek2 == false) {
                            Storage::disk('local')
                                ->put("$ambilNama[1]", Storage::disk('sftp')
                                    ->get($list->lokasi_file));
                            $contents = Storage::disk('sftp')->get($list->lokasi_file);
                            file_put_contents("berkas_vedika/$ambilNama[1]", $contents);
                        }
                        array_push($pdfFiles, public_path("berkas_vedika/$ambilNama[1]"));
                    }
                }
            }
        }

        $outputFilePath = public_path("pdfklaim/$dataSep->no_sep/$dataSep->no_sep.pdf");

        VedikaController::mergePdfs($pdfFiles, $outputFilePath);

        // if (file_exists($outputFilePath)) {
        //     // Kembalikan file PDF sebagai response
        //     return true;
        // } else {
        //     // Jika file tidak ada, kembalikan error atau pesan yang sesuai
        //     // abort(404);
        //     return false;
        // }
    }

    public function gabungRajal($no_rawat)
    {
        // mengambil data id rapat
        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pasien.no_peserta',
                'dokter.nm_dokter',
                'penjab.png_jawab'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.no_rawat', '=', $no_rawat)
            ->first();

        // dd($pasien);

        $dataSep = VedikaController::getSep($pasien->no_rawat, 2);
        if ($dataSep != null) {
            if (!empty($dataSep->no_sep)) {
                $dataDetailEklaim = EklaimController::getDetail($dataSep->no_sep);
                // dd($dataDetailEklaim);
            } else {
                $dataDetailEklaim = EklaimController::getDetail($dataSep->noSep);
            }
            $dataKlaim = json_decode(json_encode($dataDetailEklaim));
        } else {
            $dataKlaim = null;
        }
        //Ambil data billing
        $billing = VedikaController::billingRajal($pasien->no_rawat);
        //Ambil data untuk Bukti Pelayanan
        $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
        $diagnosa = $buktiPelayanan[0];
        $prosedur = $buktiPelayanan[1];
        //Ambil data Radiologi
        $radiologi = VedikaController::radioRajal($pasien->no_rawat);
        $dataRadiologiRajal = $radiologi[0];
        $dokterRadiologiRajal = $radiologi[1];
        $hasilRadiologiRajal = $radiologi[2];

        //Data USG
        $dataUsg = VedikaController::getUsg($pasien->no_rawat);
        // $dataUsgRanap = VedikaController::getUsgRanap($pasien->no_rawat);
        $dataUsgGynecologi = VedikaController::getUsgGynecologi($pasien->no_rawat);
        $dataSpiro = VedikaController::getSpiro($pasien->no_rawat);
        //Ambil data Triase dan Ringkasan IGD
        if ($pasien->nm_poli == "IGD") {
            $triase = VedikaController::triase($pasien->no_rawat);
            $dataTriase = $triase[0];
            $primer = $triase[1];
            $sekunder = $triase[2];
            $skala = $triase[3];

            $ringkasan = VedikaController::ringkasanIgd($pasien->no_rawat);
            $dataRingkasan = $ringkasan[0];
            $resumeIgd = $ringkasan[1];
        } else {
            $dataTriase = $primer = $sekunder = $skala = $dataRingkasan = $resumeIgd = null;
        }
        //Ambil Berkas tambahan
        $berkas = VedikaController::berkas($pasien->no_rawat);
        $dataBerkas = $berkas[0];
        $masterBerkas = $berkas[1];
        $pathBerkas = $berkas[2];
        //Data Lab
        $lab = VedikaController::lab($pasien->no_rawat);
        $permintaanLab = $lab[0];
        $hasilLab = $lab[1];
        //Data Obat
        $obat = VedikaController::obat($pasien->no_rawat);
        $resepObat = $obat[0];
        $obatJadi = $obat[1];
        $obatRacik = $obat[2];
        $bbPasien = $obat[3];
        //Data Pemeriksaan
        $dataRalan = VedikaController::pemeriksaanRalan($pasien->no_rawat, $pasien->kd_dokter);
        //data SOAP
        $soap = VedikaController::dataSoap($pasien->no_rawat);
        $dataOperasi = VedikaController::OperasiRajal($pasien->no_rawat);
        // dd($pasien, $billing);

        $pdf = Pdf::loadView('vedika.detailRajal_pdf', [
            'pasien' => $pasien,
            'billing' => $billing,
            'dataSep' => $dataSep,
            'dataKlaim' => $dataKlaim,
            'diagnosa' => $diagnosa,
            'prosedur' => $prosedur,
            'permintaanLab' => $permintaanLab,
            'hasilLab' => $hasilLab,
            'dataRadiologiRajal' => $dataRadiologiRajal,
            'dokterRadiologiRajal' => $dokterRadiologiRajal,
            'hasilRadiologiRajal' => $hasilRadiologiRajal,
            'dataUsg' => $dataUsg,
            // 'dataUsgRanap' => $dataUsgRanap,
            'dataUsgGynecologi' => $dataUsgGynecologi,
            'dataSpiro' => $dataSpiro,
            'dataTriase' => $dataTriase,
            'primer' => $primer,
            'sekunder' => $sekunder,
            'skala' => $skala,
            'dataRingkasan' => $dataRingkasan,
            'resumeIgd' => $resumeIgd,
            'resepObat' => $resepObat,
            'obatJadi' => $obatJadi,
            'obatRacik' => $obatRacik,
            'bbPasien' => $bbPasien,
            'dataOperasi' => $dataOperasi,
            // 'masterBerkas' => $masterBerkas,
            'dataRalan' => $dataRalan,
            // 'pathBerkas' => $pathBerkas
            'soap' => $soap
        ]);

        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'potraid');

        if (empty($dataSep->no_sep)) {
            $dataSep->no_sep = $dataSep->noSep;
        }
        // dd($dataSep);

        // Tentukan nama dan path penyimpanan file
        $fileName = "laporan_$dataSep->no_sep" . '.pdf';
        $folderPath = public_path("pdfklaim/$dataSep->no_sep");
        $filePath = $folderPath . '/' . $fileName;

        // Buat folder jika belum ada
        if (!FileLokal::exists($folderPath)) {
            FileLokal::makeDirectory($folderPath, 0755, true);
        }

        // Simpan file PDF ke lokasi tujuan
        $pdf->save($filePath);
        $pdfFiles = [
            public_path("pdfklaim/$dataSep->no_sep/$fileName")
        ];

        // dd('done');
        $berkas = VedikaController::berkas($pasien->no_rawat);
        // dd($berkas);

        $dataBerkas = $berkas[0];

        // dd($dataBerkas);
        if ($dataBerkas) {
            foreach ($dataBerkas as $list) {
                $ambilNama = explode('/upload/', $list->lokasi_file);
                $filePath = public_path("berkas_vedika/$ambilNama[1]");
                if (FileLokal::exists($filePath)) {
                    // File exists
                    array_push($pdfFiles, public_path("berkas_vedika/$ambilNama[1]"));
                } else {
                    // dd('kosong');
                    $cek = Storage::disk('sftp')->exists($list->lokasi_file);
                    if ($cek == true) {
                        // $realFile = explode('/', $id);
                        //Cek file di lokal ada tidak
                        $cek2 = Storage::disk()->exists($ambilNama[1]);
                        if ($cek2 == false) {
                            Storage::disk('local')
                                ->put("$ambilNama[1]", Storage::disk('sftp')
                                    ->get($list->lokasi_file));
                            $contents = Storage::disk('sftp')->get($list->lokasi_file);
                            file_put_contents("berkas_vedika/$ambilNama[1]", $contents);
                        }
                        array_push($pdfFiles, public_path("berkas_vedika/$ambilNama[1]"));
                    }
                }
            }
        }

        // dd($pdfFiles);

        $outputFilePath = public_path("pdfklaim/$dataSep->no_sep/$dataSep->no_sep.pdf");

        VedikaController::mergePdfs($pdfFiles, $outputFilePath);

        // dd('selesai');

        if (file_exists($outputFilePath)) {
            // Kembalikan file PDF sebagai response
            return true;
        } else {
            // Jika file tidak ada, kembalikan error atau pesan yang sesuai
            // abort(404);
            return false;
        }
    }

    public function gabungBerkasAll($periode)
    {
        set_time_limit(0);
        $periode = Crypt::decrypt($periode);

        $getData = DataPengajuanKlaim::where('periode_klaim_id', $periode)
            ->get();

        // $periodeKlaim = PeriodeKlaim::find($periode);
        $fileError = $fileSukses = 0;

        // dd($periode, $getData->where('jenis_rawat', 'Rawat Inap')->take(10));

        if ($getData) {
            // Buat pdf penggabungan file
            foreach ($getData as $data) {
                if ($data->jenis_rawat == 'Rawat Jalan') {
                    try {
                        VedikaController::gabungRajal($data->no_rawat);
                    } catch (\Exception $e) {
                        ++$fileError;
                        dd($e);
                        // Flash pesan error untuk ditampilkan di UI
                        // Session::flash('error', "Terjadi kesalahan $data->no_sep $data->nama_pasien: " . $e->getMessage());
                        // Menyimpan error ke dalam log (opsional)
                        Log::error("Error in gabungRajal $data->no_sep $data->nama_pasien: " . $e->getMessage());
                    }
                    ++$fileSukses;
                } elseif ($data->jenis_rawat == 'Rawat Inap') {
                    // dd($data);
                    try {
                        VedikaController::gabungRanap($data->no_rawat);
                    } catch (\Exception $e) {
                        ++$fileError;
                        // dd($e);
                        // Session::flash('error', "Terjadi kesalahan $data->no_sep $data->nama_pasien: " . $e->getMessage());
                        Log::error("Error in gabungRanap $data->no_sep $data->nama_pasien: " . $e->getMessage(), [
                            'no_rawat' => $data->no_rawat,
                            'exception' => $e,
                        ]);
                    }
                    ++$fileSukses;
                }
            }
        }

        Session::flash('sukses', 'Data berhasil diproses ' . $fileSukses . ', file gagal diproses ' . $fileError);

        return redirect()->back();
    }

    public function generateZipRajal($periode)
    {
        set_time_limit(0);
        $periode = Crypt::decrypt($periode);

        $getData = DataPengajuanKlaim::where('periode_klaim_id', $periode)
            ->get();

        $periodeKlaim = PeriodeKlaim::find($periode);


        if ($getData) {

            $pdfFolderPath = public_path('pdfklaim'); // Folder utama tempat file PDF berada
            $zipFileName = public_path("compressed/zipped_klaim_" . Carbon::parse($periodeKlaim->periode)->format('mY') . "_rajal.zip");

            $fileNotFound = 0;
            // Daftar file dengan path relatif terhadap folder utama
            $listOfFiles = [];

            foreach ($getData as $listBerkas) {
                if ($listBerkas->jenis_rawat == 'Rawat Jalan') {
                    array_push($listOfFiles, "$listBerkas->no_sep" . DIRECTORY_SEPARATOR . "$listBerkas->no_sep.pdf");
                }
            }

            // dd($periode, $getData->where('jenis_rawat', 'Rawat Inap')->take(10));

            // Buat objek ZipArchive
            $zip = new ZipArchive;

            // Buka atau buat file ZIP
            if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                foreach ($listOfFiles as $relativePath) {
                    // Pastikan file yang ada di daftar benar-benar ada di sistem
                    $fullPath = $pdfFolderPath . DIRECTORY_SEPARATOR . $relativePath;
                    if (FileLokal::exists($fullPath)) {
                        $info = pathinfo($relativePath);
                        // dd($relativePath, $fullPath, $pecah);

                        // Masukkan file ke dalam ZIP dengan struktur folder aslinya
                        $relativeZipPath = Carbon::parse($periodeKlaim->periode)->format('mY') . "_rajal/" . $info['basename'];
                        $zip->addFile($fullPath, $relativeZipPath);
                    } else {
                        ++$fileNotFound;
                    }
                }

                // Tutup ZIP setelah selesai
                $zip->close();

                if ($fileNotFound > 0) {
                    Session::flash('error', "Zip berhasil digenerate, ada file yang tidak ditemukan sebanyak $fileNotFound.");
                } else {
                    Session::flash('sukses', 'Zip Rajal berhasil digenerate');
                }
            } else {
                Session::flash('error', 'Gak bisa masuk file zip Rajal');
            }
        } else {
            Session::flash('error', 'Periode tidak memiliki data');
        }

        return redirect()->back();
    }

    public function generateZipRanap($periode)
    {
        set_time_limit(0);
        $periode = Crypt::decrypt($periode);

        $getData = DataPengajuanKlaim::where('periode_klaim_id', $periode)
            ->get();

        $periodeKlaim = PeriodeKlaim::find($periode);


        if ($getData) {

            $pdfFolderPath = public_path('pdfklaim'); // Folder utama tempat file PDF berada
            $zipFileName = public_path("compressed/zipped_klaim_" . Carbon::parse($periodeKlaim->periode)->format('mY') . "_ranap.zip");

            $fileNotFound = 0;
            // Daftar file dengan path relatif terhadap folder utama
            $listOfFiles = [];

            foreach ($getData as $listBerkas) {
                if ($listBerkas->jenis_rawat == 'Rawat Inap') {
                    array_push($listOfFiles, "$listBerkas->no_sep" . DIRECTORY_SEPARATOR . "$listBerkas->no_sep.pdf");
                }
            }

            // dd($periode, $getData->where('jenis_rawat', 'Rawat Inap')->take(10));

            // Buat objek ZipArchive
            $zip = new ZipArchive;

            // Buka atau buat file ZIP
            if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                foreach ($listOfFiles as $relativePath) {
                    // Pastikan file yang ada di daftar benar-benar ada di sistem
                    $fullPath = $pdfFolderPath . DIRECTORY_SEPARATOR . $relativePath;
                    if (FileLokal::exists($fullPath)) {
                        $info = pathinfo($relativePath);
                        // Masukkan file ke dalam ZIP dengan struktur folder aslinya
                        $relativeZipPath = Carbon::parse($periodeKlaim->periode)->format('mY') . "_ranap/" . $info['basename'];
                        $zip->addFile($fullPath, $relativeZipPath);
                    } else {
                        ++$fileNotFound;
                    }
                }

                // Tutup ZIP setelah selesai
                $zip->close();

                if ($fileNotFound > 0) {
                    Session::flash('error', "Zip berhasil digenerate, ada file yang tidak ditemukan sebanyak $fileNotFound.");
                } else {
                    Session::flash('sukses', 'Zip Ranap berhasil digenerate');
                }
            } else {
                Session::flash('error', 'Gak bisa masuk file zip Ranap');
            }
        } else {
            Session::flash('error', 'Periode tidak memiliki data');
        }

        return redirect()->back();
    }

    public function downloadZip($jenis, $periode)
    {
        $periode = Crypt::decrypt($periode);

        $periodeKlaim = PeriodeKlaim::find($periode);

        $filePath = public_path("compressed/zipped_klaim_" . Carbon::parse($periodeKlaim->periode)->format('mY') . "_$jenis.zip");

        if (FileLokal::exists($filePath)) {
            //download file
            return response()->download($filePath, "klaim_" . Carbon::parse($periodeKlaim->periode)->format('mY') . "_$jenis.zip");
        } else {
            Session::flash('error', 'file tidak ditemukan');

            return redirect()->back();
        }
    }

    public function generateZipRajalPending($periode)
    {
        set_time_limit(0);
        $periode = Crypt::decrypt($periode);

        $getData = DataPengajuanUlang::where('periode_pengajuan_ulang_id', $periode)
            ->get();

        $periodeKlaim = PeriodePengajuanUlang::find($periode);


        if ($getData) {

            $pdfFolderPath = public_path('pdfklaim'); // Folder utama tempat file PDF berada
            $zipFileName = public_path("compressed/zipped_klaim_pending_" . Carbon::parse($periodeKlaim->periode)->format('mY') . "_rajal.zip");

            $fileNotFound = 0;
            // Daftar file dengan path relatif terhadap folder utama
            $listOfFiles = [];

            foreach ($getData as $listBerkas) {
                if ($listBerkas->jenis_rawat == 'Rawat Jalan') {
                    array_push($listOfFiles, "$listBerkas->no_sep" . DIRECTORY_SEPARATOR . "$listBerkas->no_sep.pdf");
                }
            }

            // dd($periode, $getData->where('jenis_rawat', 'Rawat Inap')->take(10));

            // Buat objek ZipArchive
            $zip = new ZipArchive;

            // Buka atau buat file ZIP
            if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                foreach ($listOfFiles as $relativePath) {
                    // Pastikan file yang ada di daftar benar-benar ada di sistem
                    $fullPath = $pdfFolderPath . DIRECTORY_SEPARATOR . $relativePath;
                    if (FileLokal::exists($fullPath)) {
                        $info = pathinfo($relativePath);
                        // Masukkan file ke dalam ZIP dengan struktur folder aslinya
                        $relativeZipPath = Carbon::parse($periodeKlaim->periode)->format('mY') . "_rajal/" . $info['basename'];
                        $zip->addFile($fullPath, $relativeZipPath);
                    } else {
                        ++$fileNotFound;
                    }
                }

                // Tutup ZIP setelah selesai
                $zip->close();

                if ($fileNotFound > 0) {
                    Session::flash('error', "Zip berhasil digenerate, ada file yang tidak ditemukan sebanyak $fileNotFound.");
                } else {
                    Session::flash('sukses', 'Zip Rajal berhasil digenerate');
                }
            } else {
                Session::flash('error', 'Gak bisa masuk file zip Rajal');
            }
        } else {
            Session::flash('error', 'Periode tidak memiliki data');
        }

        return redirect()->back();
    }

    public function generateZipRanapPending($periode)
    {
        set_time_limit(0);
        $periode = Crypt::decrypt($periode);

        $getData = DataPengajuanUlang::where('periode_pengajuan_ulang_id', $periode)
            ->get();

        $periodeKlaim = PeriodePengajuanUlang::find($periode);


        if ($getData) {

            $pdfFolderPath = public_path('pdfklaim'); // Folder utama tempat file PDF berada
            $zipFileName = public_path("compressed/zipped_klaim_pending_" . Carbon::parse($periodeKlaim->periode)->format('mY') . "_ranap.zip");

            $fileNotFound = 0;
            // Daftar file dengan path relatif terhadap folder utama
            $listOfFiles = [];

            foreach ($getData as $listBerkas) {
                if ($listBerkas->jenis_rawat == 'Rawat Inap') {
                    array_push($listOfFiles, "$listBerkas->no_sep" . DIRECTORY_SEPARATOR . "$listBerkas->no_sep.pdf");
                }
            }

            // dd($periode, $getData->where('jenis_rawat', 'Rawat Inap')->take(10));

            // Buat objek ZipArchive
            $zip = new ZipArchive;

            // Buka atau buat file ZIP
            if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                foreach ($listOfFiles as $relativePath) {
                    // Pastikan file yang ada di daftar benar-benar ada di sistem
                    $fullPath = $pdfFolderPath . DIRECTORY_SEPARATOR . $relativePath;
                    if (FileLokal::exists($fullPath)) {
                        $info = pathinfo($relativePath);
                        // Masukkan file ke dalam ZIP dengan struktur folder aslinya
                        $relativeZipPath = Carbon::parse($periodeKlaim->periode)->format('mY') . "_ranap/" . $info['basename'];
                        $zip->addFile($fullPath, $relativeZipPath);
                    } else {
                        ++$fileNotFound;
                    }
                }

                // Tutup ZIP setelah selesai
                $zip->close();

                if ($fileNotFound > 0) {
                    Session::flash('error', "Zip berhasil digenerate, ada file yang tidak ditemukan sebanyak $fileNotFound.");
                } else {
                    Session::flash('sukses', 'Zip Ranap berhasil digenerate');
                }
            } else {
                Session::flash('error', 'Gak bisa masuk file zip Ranap');
            }
        } else {
            Session::flash('error', 'Periode tidak memiliki data');
        }

        return redirect()->back();
    }

    public function generateZipKronis($periode)
    {
        set_time_limit(0);
        $periode = Crypt::decrypt($periode);

        $getData = DataPengajuanKronis::where('periode_klaim_id', $periode)
            ->get();

        $periodeKlaim = PeriodeKlaim::find($periode);

        if ($getData) {

            $pdfFolderPath = public_path('pdfkronis'); // Folder utama tempat file PDF berada
            $zipFileName = public_path("compressed/zipped_klaim_kronis_" . Carbon::parse($periodeKlaim->periode)->format('mY') . ".zip");

            $fileNotFound = 0;
            // Daftar file dengan path relatif terhadap folder utama
            $listOfFiles = [];

            foreach ($getData as $listBerkas) {
                // dd($listBerkas);
                // if ($listBerkas->jenis_rawat == 'Rawat Jalan') {
                array_push($listOfFiles, Carbon::parse($periodeKlaim->periode)->format('Ym') . DIRECTORY_SEPARATOR . "$listBerkas->no_resep.pdf");
                // }
            }

            // dd($periode, $getData->where('jenis_rawat', 'Rawat Inap')->take(10));

            // Buat objek ZipArchive
            $zip = new ZipArchive;

            // Buka atau buat file ZIP
            if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                foreach ($listOfFiles as $relativePath) {
                    // dd($relativePath);
                    // Pastikan file yang ada di daftar benar-benar ada di sistem
                    $fullPath = $pdfFolderPath . DIRECTORY_SEPARATOR . $relativePath;
                    if (FileLokal::exists($fullPath)) {
                        // dd($relativePath, $fullPath);
                        // Masukkan file ke dalam ZIP dengan struktur folder aslinya
                        $relativeZipPath = $relativePath;
                        $zip->addFile($fullPath, $relativeZipPath);
                    } else {
                        ++$fileNotFound;
                    }
                }

                // Tutup ZIP setelah selesai
                $zip->close();

                if ($fileNotFound > 0) {
                    Session::flash('error', "Zip berhasil digenerate, ada file yang tidak ditemukan sebanyak $fileNotFound.");
                } else {
                    Session::flash('sukses', 'Zip Rajal berhasil digenerate');
                }
            } else {
                Session::flash('error', 'Gak bisa masuk file zip Rajal');
            }
        } else {
            Session::flash('error', 'Periode tidak memiliki data');
        }

        return redirect()->back();
    }

    public function downloadZipPending($jenis, $periode)
    {
        $periode = Crypt::decrypt($periode);

        $periodeKlaim = PeriodePengajuanUlang::find($periode);

        $filePath = public_path("compressed/zipped_klaim_pending_" . Carbon::parse($periodeKlaim->periode)->format('mY') . "_$jenis.zip");

        if (FileLokal::exists($filePath)) {
            //download file
            return response()->download($filePath, "klaim_pending_" . Carbon::parse($periodeKlaim->periode)->format('mY') . "_$jenis.zip");
        } else {
            Session::flash('error', 'file tidak ditemukan');

            return redirect()->back();
        }
    }

    public function downloadZipKronis($periode)
    {
        $periode = Crypt::decrypt($periode);

        $periodeKlaim = PeriodeKlaim::find($periode);

        $filePath = public_path("compressed/zipped_klaim_kronis_" . Carbon::parse($periodeKlaim->periode)->format('mY') . ".zip");

        if (FileLokal::exists($filePath)) {
            //download file
            return response()->download($filePath, "klaim_kronis_" . Carbon::parse($periodeKlaim->periode)->format('mY') . ".zip");
        } else {
            Session::flash('error', 'file tidak ditemukan');

            return redirect()->back();
        }
    }

    public function cronisRajalPdf($id)
    {
        $id = Crypt::decrypt($id);
        // mengambil data id rapat
        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pasien.no_peserta',
                'dokter.nm_dokter',
                'penjab.png_jawab'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        // dd($pasien);

        //Ambil data billing
        // $billing = VedikaController::billingRajal($pasien->no_rawat);
        $billing = VedikaController::getBillFarmasi($pasien->no_rawat);
        //Ambil data untuk Bukti Pelayanan
        $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
        $diagnosa = $buktiPelayanan[0];
        $prosedur = $buktiPelayanan[1];
        //Ambil data Radiologi
        $radiologi = VedikaController::radioRajal($pasien->no_rawat);
        $dataRadiologiRajal = $radiologi[0];
        $dokterRadiologiRajal = $radiologi[1];
        $hasilRadiologiRajal = $radiologi[2];
        //Ambil data Triase dan Ringkasan IGD
        if ($pasien->nm_poli == "IGD") {
            $triase = VedikaController::triase($pasien->no_rawat);
            $dataTriase = $triase[0];
            $primer = $triase[1];
            $sekunder = $triase[2];
            $skala = $triase[3];

            $ringkasan = VedikaController::ringkasanIgd($pasien->no_rawat);
            $dataRingkasan = $ringkasan[0];
            $resumeIgd = $ringkasan[1];
        } else {
            $dataTriase = $primer = $sekunder = $skala = $dataRingkasan = $resumeIgd = null;
        }
        //Ambil Berkas tambahan
        $berkas = VedikaController::berkas($pasien->no_rawat);
        $dataBerkas = $berkas[0];
        $masterBerkas = $berkas[1];
        $pathBerkas = $berkas[2];
        //Data Lab
        $lab = VedikaController::lab($pasien->no_rawat);
        $permintaanLab = $lab[0];
        $hasilLab = $lab[1];
        //Data Obat
        $obat = VedikaController::obat($pasien->no_rawat);
        $resepObat = $obat[0];
        $obatJadi = $obat[1];
        $obatRacik = $obat[2];
        $bbPasien = $obat[3];
        //Data Pemeriksaan
        $dataRalan = VedikaController::pemeriksaanRalan($pasien->no_rawat, $pasien->kd_dokter);
        // dd($obat, $resepObat);

        $pdf = Pdf::loadView('vedika.cronisRajal_pdf', [
            'pasien' => $pasien,
            'billing' => $billing,
            'diagnosa' => $diagnosa,
            'prosedur' => $prosedur,
            'permintaanLab' => $permintaanLab,
            'hasilLab' => $hasilLab,
            'dataRadiologiRajal' => $dataRadiologiRajal,
            'dokterRadiologiRajal' => $dokterRadiologiRajal,
            'hasilRadiologiRajal' => $hasilRadiologiRajal,
            'dataTriase' => $dataTriase,
            'primer' => $primer,
            'sekunder' => $sekunder,
            'skala' => $skala,
            'dataRingkasan' => $dataRingkasan,
            'resumeIgd' => $resumeIgd,
            'resepObat' => $resepObat,
            'obatJadi' => $obatJadi,
            'obatRacik' => $obatRacik,
            'bbPasien' => $bbPasien,
            'dataBerkas' => $dataBerkas,
            'masterBerkas' => $masterBerkas,
            'dataRalan' => $dataRalan,
            'pathBerkas' => $pathBerkas
        ]);

        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'potraid');

        // $pdf->setOptions(['isRemoteEnabled' => true]);

        // Render the HTML as PDF
        //$pdf->render();
        //Watermark
        // $pdf->setPaper('L');
        // $pdf->output();
        // $canvas = $pdf->getDomPDF()->getCanvas();

        // $height = $canvas->get_height();
        // $width = $canvas->get_width();

        // $canvas->set_opacity(.2, "Multiply");

        // $canvas->set_opacity(.2);

        // $canvas->page_text(
        //     $width / 5,
        //     $height / 2,
        //     'VedikaRSUPGate',
        //     null,
        //     55,
        //     array(0, 0, 0),
        //     2,
        //     2,
        //     -30
        // );

        return $pdf->stream();
    }

    public function sepManual($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Input SEP Manual');
        session()->forget('cucu');

        $id = Crypt::decrypt($id);

        $cek = sepManual::where('noRawat', $id)->count();

        if (empty($cek)) {
            $data = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                // ->join('bridging_sep', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
                ->select(
                    'reg_periksa.no_rawat',
                    'reg_periksa.kd_poli',
                    'pasien.nm_pasien',
                    'pasien.jk',
                    'pasien.tmp_lahir',
                    'pasien.tgl_lahir',
                    'pasien.alamat'
                    // 'bridging_sep.no_sep'
                )
                ->where('reg_periksa.no_rawat', '=', $id)
                ->first();
        } else {
            Session::flash('error', 'Data SEP sudah ada!');

            return redirect()->back();
        }

        // dd($data);

        return view('vedika.add_noSep', compact('data'));
    }

    public function hapusSepManual($id)
    {
        $norawat = Crypt::decrypt($id);
        $cari = sepManual::where('noRawat', $norawat)
            ->delete();

        Session::flash('error', 'Data SEP sudah dihapus!');

        return redirect()->back();
    }

    public function simpanVerif(request $request)
    {
        $data = new VedikaVerif();
        $data->noRawat = $request->no_rawat;
        $data->statusRawat = $request->statusRawat;
        $data->verifikasi = $request->catatan;
        $data->status = $request->status;
        $data->user_id = Auth::user()->id;
        $data->save();

        return redirect()->back();
    }

    public function updateVerif($id, Request $request)
    {
        // $this->validate($request, [
        //     'signed' => 'required',
        // ], [
        //     'signed.required' => 'Tanda tangan pasien kosong'
        // ]);
        // dd($id, $request);
        $id = Crypt::decrypt($id);
        $data = VedikaVerif::find($request->id_verif);
        $data->verifikasi = $request->catatan;
        $data->status = $request->status;
        $data->user_id = Auth::user()->id;
        $data->save();
        // dd($id, $data);

        if ($data) {
            Session::flash('sukses', 'Data berhasil disimpan');
        } else {
            Session::flash('error', 'Gagal menyimpan data');
        }

        return redirect()->back();
    }

    public function simpanSep(Request $request)
    {
        // $this->validate($request, [
        //     'signed' => 'required',
        // ], [
        //     'signed.required' => 'Tanda tangan pasien kosong'
        // ]);

        // dd($request);
        $data = new sepManual();
        $data->noRawat = $request->noRawat;
        $data->no_sep = $request->no_sep;
        $data->tandaTangan = $request->signed;
        $data->save();

        return redirect('/vedika/rajal');
    }

    public static function billingRajal($id)
    {
        // session()->put('ibu', 'Vedika');
        // session()->put('anak', 'Pasien Rajal');
        // session()->put('cucu', 'Billing');

        // $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('billing')
            ->select(
                'billing.no as no_status',
                'billing.nm_perawatan',
                'billing.pemisah',
                'billing.biaya',
                'billing.jumlah',
                'billing.tambahan',
                'billing.totalbiaya',
                'billing.status',
                'billing.tgl_byr'
            )
            ->where('billing.no_rawat', '=', $id)
            ->get();

        return $data;

        // return view('vedika.billing', compact('data'));
    }

    public static function obat($id)
    {
        $cek = DB::connection('mysqlkhanza')->table('resep_obat')
            ->where('resep_obat.no_rawat', '=', $id)
            ->get();
        // dd($cek->count());
        $pasien = $data = $racikan = $bbPasien = [];
        if ($cek->count() == 1) {
            $dataPasien = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
                ->join('resep_obat', 'resep_obat.no_rawat', '=', 'reg_periksa.no_rawat')
                ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'resep_obat.kd_dokter')
                // ->join('penilaian_awal_keperawatan_ralan', 'penilaian_awal_keperawatan_ralan.no_rawat', '=', 'reg_periksa.no_rawat')
                ->leftJoin('kelurahan', 'kelurahan.kd_kel', '=', 'pasien.kd_kel')
                ->leftJoin('kecamatan', 'kecamatan.kd_kec', '=', 'pasien.kd_kec')
                ->leftJoin('kabupaten', 'kabupaten.kd_kab', '=', 'pasien.kd_kab')
                ->select(
                    'reg_periksa.no_rkm_medis',
                    'reg_periksa.no_rawat',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.jam_reg',
                    'reg_periksa.status_lanjut',
                    'reg_periksa.kd_pj',
                    'reg_periksa.stts',
                    'reg_periksa.kd_dokter',
                    'poliklinik.nm_poli',
                    'pasien.nm_pasien',
                    'pasien.no_ktp',
                    'pasien.no_peserta',
                    'pasien.alamat',
                    'pasien.tgl_lahir',
                    'kelurahan.nm_kel',
                    'kecamatan.nm_kec',
                    'kabupaten.nm_kab',
                    'resep_obat.no_resep',
                    'resep_obat.tgl_perawatan',
                    'resep_obat.jam',
                    'resep_obat.tgl_peresepan',
                    'resep_obat.jam_peresepan',
                    // 'penilaian_awal_keperawatan_ralan.bb',
                    'dokter.nm_dokter'
                )
                // ->where('reg_periksa.kd_pj', '=', 'BPJ')
                // ->where('reg_periksa.status_lanjut', '=', 'Ralan')
                ->where('reg_periksa.no_rawat', '=', $id)
                ->first();

            array_push($pasien, $dataPasien);
            // dd($pasien);
            if (!empty($dataPasien)) {
                if ($dataPasien->status_lanjut == 'Ralan') {
                    $dataBB = DB::connection('mysqlkhanza')->table('pemeriksaan_ralan')
                        ->select(
                            'pemeriksaan_ralan.no_rawat',
                            'pemeriksaan_ralan.berat'
                        )
                        ->where('pemeriksaan_ralan.no_rawat', $id)
                        ->first();
                } else {
                    $dataBB = DB::connection('mysqlkhanza')->table('pemeriksaan_ranap')
                        ->select(
                            'pemeriksaan_ranap.no_rawat',
                            'pemeriksaan_ranap.tgl_perawatan',
                            'pemeriksaan_ranap.jam_rawat',
                            'pemeriksaan_ranap.berat'
                        )
                        ->orderBy('pemeriksaan_ranap.tgl_perawatan', 'DESC')
                        ->orderBy('pemeriksaan_ranap.jam_rawat', 'DESC')
                        ->where('pemeriksaan_ranap.no_rawat', $id)
                        ->first();
                }
            } else {
                $dataBB = null;
            }

            array_push($bbPasien, $dataBB);

            $dataObat = DB::connection('mysqlkhanza')->table('detail_pemberian_obat')
                // ->join('resep_obat', 'resep_obat.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
                ->join('resep_obat', function ($join) {
                    $join->on('resep_obat.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
                        ->on('resep_obat.tgl_perawatan', '=', 'detail_pemberian_obat.tgl_perawatan')
                        ->on('resep_obat.jam', '=', 'detail_pemberian_obat.jam');
                })
                ->join('databarang', 'databarang.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
                ->leftJoin('kodesatuan', 'kodesatuan.kode_sat', '=', 'databarang.kode_sat')
                ->select(
                    'detail_pemberian_obat.tgl_perawatan',
                    'detail_pemberian_obat.jam',
                    'detail_pemberian_obat.no_rawat',
                    'detail_pemberian_obat.kode_brng',
                    'detail_pemberian_obat.biaya_obat',
                    'detail_pemberian_obat.jml',
                    'detail_pemberian_obat.total',
                    'detail_pemberian_obat.status',
                    'resep_obat.kd_dokter',
                    'resep_obat.tgl_perawatan',
                    'resep_obat.jam',
                    'resep_obat.tgl_peresepan',
                    'resep_obat.jam_peresepan',
                    'databarang.nama_brng',
                    'kodesatuan.satuan'
                )
                ->where('detail_pemberian_obat.no_rawat', '=', $id)
                ->where('detail_pemberian_obat.status', '=', 'Ralan')
                ->get();

            array_push($data, $dataObat);

            $dataRacikan = DB::connection('mysqlkhanza')->table('obat_racikan')
                ->join('metode_racik', 'metode_racik.kd_racik', '=', 'obat_racikan.kd_racik')
                ->select(
                    'obat_racikan.tgl_perawatan',
                    'obat_racikan.jam',
                    'obat_racikan.no_rawat',
                    'obat_racikan.no_racik',
                    'obat_racikan.nama_racik',
                    'metode_racik.nm_racik',
                    'obat_racikan.jml_dr',
                    'obat_racikan.aturan_pakai',
                    'obat_racikan.keterangan'
                )
                ->where('obat_racikan.no_rawat', $id)
                ->get();

            array_push($racikan, $dataRacikan);

            return array($pasien, $data, $racikan, $bbPasien);
        } else {
            foreach ($cek as $index => $noResep) {
                // dd($noResep);
                $dataPasien = DB::connection('mysqlkhanza')->table('reg_periksa')
                    ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                    ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
                    ->join('resep_obat', 'resep_obat.no_rawat', '=', 'reg_periksa.no_rawat')
                    ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'resep_obat.kd_dokter')
                    // ->join('penilaian_awal_keperawatan_ralan', 'penilaian_awal_keperawatan_ralan.no_rawat', '=', 'reg_periksa.no_rawat')
                    ->leftJoin('kelurahan', 'kelurahan.kd_kel', '=', 'pasien.kd_kel')
                    ->leftJoin('kecamatan', 'kecamatan.kd_kec', '=', 'pasien.kd_kec')
                    ->leftJoin('kabupaten', 'kabupaten.kd_kab', '=', 'pasien.kd_kab')
                    ->select(
                        'reg_periksa.no_rkm_medis',
                        'reg_periksa.no_rawat',
                        'reg_periksa.tgl_registrasi',
                        'reg_periksa.jam_reg',
                        'reg_periksa.status_lanjut',
                        'reg_periksa.kd_pj',
                        'reg_periksa.stts',
                        'reg_periksa.kd_dokter',
                        'poliklinik.nm_poli',
                        'pasien.nm_pasien',
                        'pasien.no_ktp',
                        'pasien.no_peserta',
                        'pasien.alamat',
                        'pasien.tgl_lahir',
                        'kelurahan.nm_kel',
                        'kecamatan.nm_kec',
                        'kabupaten.nm_kab',
                        'resep_obat.no_resep',
                        'resep_obat.tgl_perawatan',
                        'resep_obat.jam',
                        'resep_obat.tgl_peresepan',
                        'resep_obat.jam_peresepan',
                        // 'penilaian_awal_keperawatan_ralan.bb',
                        'dokter.nm_dokter'
                    )
                    ->where('resep_obat.no_resep', '=', $noResep->no_resep)
                    ->first();

                array_push($pasien, $dataPasien);

                // dd($pasien);
                if (!empty($dataPasien)) {
                    if ($dataPasien->status_lanjut == 'Ralan') {
                        $dataBB = DB::connection('mysqlkhanza')->table('pemeriksaan_ralan')
                            ->select(
                                'pemeriksaan_ralan.no_rawat',
                                'pemeriksaan_ralan.berat'
                            )
                            ->where('pemeriksaan_ralan.no_rawat', $dataPasien->no_rawat)
                            ->first();
                    } else {
                        $dataBB = DB::connection('mysqlkhanza')->table('pemeriksaan_ranap')
                            ->select(
                                'pemeriksaan_ranap.no_rawat',
                                'pemeriksaan_ranap.tgl_perawatan',
                                'pemeriksaan_ranap.jam_rawat',
                                'pemeriksaan_ranap.berat'
                            )
                            ->orderBy('pemeriksaan_ranap.tgl_perawatan', 'DESC')
                            ->orderBy('pemeriksaan_ranap.jam_rawat', 'DESC')
                            ->where('pemeriksaan_ranap.no_rawat', $dataPasien->no_rawat)
                            ->first();
                    }
                } else {
                    $dataBB = null;
                }
                array_push($bbPasien, $dataBB);

                $dataObat = DB::connection('mysqlkhanza')->table('detail_pemberian_obat')
                    // ->join('resep_obat', 'resep_obat.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
                    ->join('databarang', 'databarang.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
                    ->leftJoin('kodesatuan', 'kodesatuan.kode_sat', '=', 'databarang.kode_sat')
                    ->select(
                        'detail_pemberian_obat.tgl_perawatan',
                        'detail_pemberian_obat.jam',
                        'detail_pemberian_obat.no_rawat',
                        'detail_pemberian_obat.kode_brng',
                        'detail_pemberian_obat.biaya_obat',
                        'detail_pemberian_obat.jml',
                        'detail_pemberian_obat.total',
                        'detail_pemberian_obat.status',
                        // 'resep_obat.kd_dokter',
                        // 'resep_obat.tgl_perawatan',
                        // 'resep_obat.jam',
                        // 'resep_obat.tgl_peresepan',
                        // 'resep_obat.jam_peresepan',
                        'databarang.nama_brng',
                        'kodesatuan.satuan'
                    )
                    ->where('detail_pemberian_obat.no_rawat', '=', $dataPasien->no_rawat)
                    ->where('detail_pemberian_obat.jam', '=', $dataPasien->jam)
                    ->where('detail_pemberian_obat.status', '=', 'Ralan')
                    ->get();

                array_push($data, $dataObat);

                $dataRacikan = DB::connection('mysqlkhanza')->table('obat_racikan')
                    ->join('metode_racik', 'metode_racik.kd_racik', '=', 'obat_racikan.kd_racik')
                    ->select(
                        'obat_racikan.tgl_perawatan',
                        'obat_racikan.jam',
                        'obat_racikan.no_rawat',
                        'obat_racikan.no_racik',
                        'obat_racikan.nama_racik',
                        'metode_racik.nm_racik',
                        'obat_racikan.jml_dr',
                        'obat_racikan.aturan_pakai',
                        'obat_racikan.keterangan'
                    )
                    ->where('obat_racikan.no_rawat', $dataPasien->no_rawat)
                    ->get();

                array_push($racikan, $dataRacikan);
            }

            return array($pasien, $data, $racikan, $bbPasien);
        }
    }

    public function OperasiRajal($id)
    {
        $data = DB::connection('mysqlkhanza')->table('operasi')
            ->join('pemeriksaan_ralan', 'pemeriksaan_ralan.no_rawat', '=', 'operasi.no_rawat')
            ->join('laporan_operasi', 'laporan_operasi.no_rawat', '=', 'operasi.no_rawat')
            ->select(
                'operasi.no_rawat',
                'operasi.tgl_operasi',
                'operasi.jenis_anasthesi',
                'operasi.kategori',
                'operasi.operator1',
                'operasi.operator2',
                'operasi.operator3',
                'operasi.asisten_operator1',
                'operasi.asisten_operator2',
                'operasi.asisten_operator3',
                'operasi.instrumen',
                'operasi.dokter_anak',
                'operasi.perawaat_resusitas',
                'operasi.dokter_anestesi',
                'operasi.asisten_anestesi',
                'operasi.asisten_anestesi2',
                'operasi.bidan',
                'operasi.bidan2',
                'operasi.perawat_luar',
                'operasi.omloop',
                'operasi.omloop2',
                'operasi.omloop3',
                'operasi.dokter_umum',
                'operasi.kode_paket',
                'operasi.biayaoperator1',
                'operasi.status',
                'pemeriksaan_ralan.tgl_perawatan',
                'pemeriksaan_ralan.jam_rawat',
                'pemeriksaan_ralan.suhu_tubuh',
                'pemeriksaan_ralan.tensi',
                'pemeriksaan_ralan.nadi',
                'pemeriksaan_ralan.respirasi',
                'pemeriksaan_ralan.tinggi',
                'pemeriksaan_ralan.berat',
                'pemeriksaan_ralan.spo2',
                'pemeriksaan_ralan.gcs',
                'pemeriksaan_ralan.kesadaran',
                'pemeriksaan_ralan.keluhan',
                'pemeriksaan_ralan.pemeriksaan',
                'pemeriksaan_ralan.alergi',
                'pemeriksaan_ralan.rtl',
                'pemeriksaan_ralan.penilaian',
                'laporan_operasi.diagnosa_preop',
                'laporan_operasi.diagnosa_postop',
                'laporan_operasi.jaringan_dieksekusi',
                'laporan_operasi.selesaioperasi',
                'laporan_operasi.permintaan_pa',
                'laporan_operasi.laporan_operasi'
            )
            ->where('operasi.no_rawat', '=', $id)
            ->first();
        // dd($data);

        return $data;
    }

    public function OperasiRanap($id)
    {
        $data = DB::connection('mysqlkhanza')->table('operasi')
            // ->join('pemeriksaan_ralan', 'pemeriksaan_ralan.no_rawat', '=', 'operasi.no_rawat')
            ->join('laporan_operasi', 'laporan_operasi.no_rawat', '=', 'operasi.no_rawat')
            ->select(
                'operasi.no_rawat',
                'operasi.tgl_operasi',
                'operasi.jenis_anasthesi',
                'operasi.kategori',
                'operasi.operator1',
                'operasi.operator2',
                'operasi.operator3',
                'operasi.asisten_operator1',
                'operasi.asisten_operator2',
                'operasi.asisten_operator3',
                'operasi.instrumen',
                'operasi.dokter_anak',
                'operasi.perawaat_resusitas',
                'operasi.dokter_anestesi',
                'operasi.asisten_anestesi',
                'operasi.asisten_anestesi2',
                'operasi.bidan',
                'operasi.bidan2',
                'operasi.perawat_luar',
                'operasi.omloop',
                'operasi.omloop2',
                'operasi.omloop3',
                'operasi.dokter_umum',
                'operasi.kode_paket',
                'operasi.biayaoperator1',
                'operasi.status'
                // 'laporan_operasi.diagnosa_preop',
                // 'laporan_operasi.diagnosa_postop',
                // 'laporan_operasi.jaringan_dieksekusi',
                // 'laporan_operasi.selesaioperasi',
                // 'laporan_operasi.permintaan_pa',
                // 'laporan_operasi.laporan_operasi'
            )
            ->where('operasi.no_rawat', '=', $id)
            ->groupBy('operasi.kode_paket')
            ->get();

        foreach ($data as $listData) {
            $cek = DB::connection('mysqlkhanza')->table('laporan_operasi')
                ->select(
                    'laporan_operasi.no_rawat',
                    'laporan_operasi.tanggal',
                    'laporan_operasi.diagnosa_preop',
                    'laporan_operasi.diagnosa_postop',
                    'laporan_operasi.jaringan_dieksekusi',
                    'laporan_operasi.selesaioperasi',
                    'laporan_operasi.permintaan_pa',
                    'laporan_operasi.laporan_operasi'
                )
                ->where('laporan_operasi.no_rawat', '=', $listData->no_rawat)
                ->where('laporan_operasi.tanggal', '=', $listData->tgl_operasi)
                ->first();

            // Tambahkan data ke objek $listData
            if ($cek) {
                $listData->diagnosa_preop = $cek->diagnosa_preop;
                $listData->diagnosa_postop = $cek->diagnosa_postop;
                $listData->jaringan_dieksekusi = $cek->jaringan_dieksekusi;
                $listData->selesaioperasi = $cek->selesaioperasi;
                $listData->permintaan_pa = $cek->permintaan_pa;
                $listData->laporan_operasi = $cek->laporan_operasi;
            }
        }

        // dd($data);

        $tglMasuk = DB::connection('mysqlkhanza')->table('kamar_inap')
            ->select(
                'kamar_inap.no_rawat',
                'kamar_inap.tgl_masuk'
            )
            ->where('kamar_inap.no_rawat', '=', $id)
            ->orderBy('tgl_masuk', 'ASC')
            ->first();

        $dataPemeriksaan = DB::connection('mysqlkhanza')->table('pemeriksaan_ranap')
            ->select(
                'pemeriksaan_ranap.no_rawat',
                DB::raw("CONCAT(pemeriksaan_ranap.tgl_perawatan, ' ', pemeriksaan_ranap.jam_rawat) AS tgl_jam_rawat"),
                'pemeriksaan_ranap.tgl_perawatan',
                'pemeriksaan_ranap.jam_rawat',
                'pemeriksaan_ranap.suhu_tubuh',
                'pemeriksaan_ranap.tensi',
                'pemeriksaan_ranap.nadi',
                'pemeriksaan_ranap.respirasi',
                'pemeriksaan_ranap.tinggi',
                'pemeriksaan_ranap.berat',
                'pemeriksaan_ranap.spo2',
                'pemeriksaan_ranap.gcs',
                'pemeriksaan_ranap.kesadaran',
                'pemeriksaan_ranap.keluhan',
                'pemeriksaan_ranap.pemeriksaan',
                'pemeriksaan_ranap.alergi',
                'pemeriksaan_ranap.rtl',
                'pemeriksaan_ranap.penilaian',
                'pemeriksaan_ranap.instruksi',
                'pemeriksaan_ranap.evaluasi'
            )
            ->where('pemeriksaan_ranap.no_rawat', '=', $id)
            // ->where('pemeriksaan_ranap.tgl_perawatan', '=', $tglMasuk->tgl_masuk)
            ->orderBy('pemeriksaan_ranap.tgl_perawatan', 'ASC') // Mengurutkan berdasarkan tanggal
            ->orderBy('pemeriksaan_ranap.jam_rawat', 'DESC')   // Mengurutkan berdasarkan jam terakhir
            ->first();


        // dd($data, $tglMasuk, $dataPemeriksaan);

        return array($data, $dataPemeriksaan);
    }

    public function obatRajal($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Rajal');
        session()->put('cucu', 'Obat');

        $id = Crypt::decrypt($id);

        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('resep_obat', 'resep_obat.no_rawat', '=', 'reg_periksa.no_rawat')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.no_peserta',
                'resep_obat.no_resep',
                'resep_obat.tgl_perawatan',
                'dokter.nm_dokter'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        // if ($pasien->nm_poli == 'IGD') {
        // dd($pasien);
        // }

        $data = DB::connection('mysqlkhanza')->table('detail_pemberian_obat')
            ->join('resep_obat', 'resep_obat.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
            ->join('databarang', 'databarang.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
            ->select(
                'detail_pemberian_obat.tgl_perawatan',
                'detail_pemberian_obat.jam',
                'detail_pemberian_obat.no_rawat',
                'detail_pemberian_obat.kode_brng',
                'detail_pemberian_obat.biaya_obat',
                'detail_pemberian_obat.jml',
                'detail_pemberian_obat.total',
                'detail_pemberian_obat.status',
                'resep_obat.kd_dokter',
                'databarang.nama_brng',
                'databarang.kode_sat'
            )
            ->where('detail_pemberian_obat.no_rawat', '=', $id)
            ->where('detail_pemberian_obat.status', '=', 'Ralan')
            ->get();

        // dd($pasien, $data);

        return view('vedika.obat', compact('pasien', 'data'));
    }

    public function obatRanap($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Ranap');
        session()->put('cucu', 'Obat');

        $id = Crypt::decrypt($id);

        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('resep_obat', 'resep_obat.no_rawat', '=', 'reg_periksa.no_rawat')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                // 'reg_periksa.tgl_registrasi',
                // 'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.no_peserta',
                'resep_obat.no_resep',
                'resep_obat.tgl_perawatan',
                'resep_obat.jam',
                'dokter.nm_dokter'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ranap')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->get();

        $data = DB::connection('mysqlkhanza')->table('detail_pemberian_obat')
            // ->leftJoin('resep_obat', 'resep_obat.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
            ->join('databarang', 'databarang.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
            ->select(
                'detail_pemberian_obat.tgl_perawatan',
                'detail_pemberian_obat.jam',
                'detail_pemberian_obat.no_rawat',
                'detail_pemberian_obat.kode_brng',
                'detail_pemberian_obat.biaya_obat',
                'detail_pemberian_obat.jml',
                'detail_pemberian_obat.total',
                'detail_pemberian_obat.status',
                // 'resep_obat.kd_dokter',
                'databarang.nama_brng',
                'databarang.kode_sat'
            )
            ->where('detail_pemberian_obat.no_rawat', '=', $id)
            ->where('detail_pemberian_obat.status', '=', 'Ranap')
            ->get();

        // dd($pasien, $data);

        return view('vedika.obat_ranap', compact('pasien', 'data'));
    }

    public static function billingRanap($id)
    {
        // session()->put('ibu', 'Vedika');
        // session()->put('anak', 'Pasien Ranap');
        // session()->put('cucu', 'Billing');

        // $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('billing')
            ->select(
                'billing.no as no_status',
                'billing.nm_perawatan',
                'billing.pemisah',
                'billing.biaya',
                'billing.jumlah',
                'billing.tambahan',
                'billing.totalbiaya',
                'billing.status',
                'billing.tgl_byr'
            )
            ->where('billing.no_rawat', '=', $id)
            ->get();

        // dd($data);
        return $data;

        // return view('vedika.billing', compact('data'));
    }

    public static function lab($id)
    {
        $cek = DB::connection('mysqlkhanza')->table('permintaan_lab')
            ->join('dokter', 'dokter.kd_dokter', '=', 'permintaan_lab.dokter_perujuk')
            // ->leftJoin('periksa_lab', function ($join) {
            //     $join->on('periksa_lab.no_rawat', '=', 'permintaan_lab.no_rawat')
            //         ->on('periksa_lab.tgl_periksa', '=', 'permintaan_lab.tgl_hasil')
            //         ->on('periksa_lab.jam', '=', 'permintaan_lab.jam_hasil');
            // })
            ->select(
                'permintaan_lab.noorder',
                'permintaan_lab.no_rawat',
                'permintaan_lab.tgl_permintaan',
                'permintaan_lab.jam_permintaan',
                'permintaan_lab.tgl_hasil',
                // 'periksa_lab.tgl_periksa as tgl_hasil',
                'permintaan_lab.jam_hasil',
                // 'periksa_lab.jam as jam_hasil',
                'permintaan_lab.status',
                'dokter.nm_dokter'
            )
            ->where('permintaan_lab.no_rawat', '=', $id)
            ->get();

        // foreach ($cek as $dataCek) {
        //     if ($dataCek->jam_hasil == null) {
        //         $getJamHasil = DB::connection('mysqlkhanza')->table('permintaan_pemeriksaan_lab')
        //             ->join('permintaan_lab', 'permintaan_lab.noorder', '=', 'permintaan_pemeriksaan_lab.noorder')
        //             ->leftJoin('periksa_lab', function ($join) {
        //                 $join->on('periksa_lab.no_rawat', '=', 'permintaan_lab.no_rawat')
        //                     ->on('periksa_lab.kd_jenis_prw', '=', 'permintaan_pemeriksaan_lab.kd_jenis_prw');
        //                 // ->on('periksa_lab.jam', '=', 'permintaan_lab.jam_hasil');
        //             })
        //             ->select(
        //                 'permintaan_pemeriksaan_lab.noorder',
        //                 'permintaan_lab.no_rawat',
        //                 'periksa_lab.tgl_periksa as tgl_hasil',
        //                 'periksa_lab.jam as jam_hasil',
        //             )
        //             ->where('permintaan_lab.no_rawat', '=', $dataCek->no_rawat)
        //             ->where('permintaan_pemeriksaan_lab.noorder', '=', $dataCek->noorder)
        //             ->first();

        //         if ($getJamHasil) {
        //             $dataCek->tgl_hasil = $getJamHasil->tgl_hasil;
        //             $dataCek->jam_hasil = $getJamHasil->jam_hasil;
        //         }
        //     }
        // }

        if ($cek->count() > 0) {
            $hasil_periksa = DB::connection('mysqlkhanza')->table('detail_periksa_lab')
                ->join('periksa_lab', 'periksa_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
                ->leftJoin('template_laboratorium', 'template_laboratorium.id_template', '=', 'detail_periksa_lab.id_template')
                ->select(
                    // 'jns_perawatan_lab.nm_perawatan',
                    'detail_periksa_lab.no_rawat',
                    'detail_periksa_lab.tgl_periksa',
                    'detail_periksa_lab.jam',
                    'detail_periksa_lab.kd_jenis_prw',
                    'detail_periksa_lab.nilai',
                    'detail_periksa_lab.nilai_rujukan',
                    'detail_periksa_lab.keterangan',
                    'periksa_lab.nip as petugas',
                    'periksa_lab.dokter_perujuk',
                    'periksa_lab.kd_dokter as dokter_lab',
                    'template_laboratorium.Pemeriksaan',
                    'template_laboratorium.id_template',
                    'template_laboratorium.satuan'
                )
                ->where('detail_periksa_lab.no_rawat', $id)
                ->groupBy('template_laboratorium.id_template', 'detail_periksa_lab.kd_jenis_prw', 'detail_periksa_lab.jam')
                ->get();

            $kesan = DB::connection('mysqlkhanza')->table('saran_kesan_lab')
                ->select(
                    'saran_kesan_lab.no_rawat',
                    'saran_kesan_lab.tgl_periksa',
                    'saran_kesan_lab.jam',
                    'saran_kesan_lab.saran',
                    'saran_kesan_lab.kesan'
                )
                ->where('saran_kesan_lab.no_rawat', '=', $id)
                ->get();
        } else {
            $hasil_periksa = null;
            $kesan = null;
        }

        // dd($cek, $hasil_periksa);
        return array($cek, $hasil_periksa, $kesan);
    }

    public function labRajal($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Rajal');
        session()->put('cucu', 'Lab');

        $id = Crypt::decrypt($id);

        $cek = DB::connection('mysqlkhanza')->table('permintaan_lab')
            ->join('dokter', 'dokter.kd_dokter', '=', 'permintaan_lab.dokter_perujuk')
            ->select(
                'permintaan_lab.noorder',
                'permintaan_lab.no_rawat',
                'permintaan_lab.tgl_permintaan',
                'permintaan_lab.jam_permintaan',
                'permintaan_lab.tgl_hasil',
                'permintaan_lab.jam_hasil',
                'permintaan_lab.status',
                'dokter.nm_dokter'
            )
            // ->where('permintaan_lab.status', '=', 'ralan')
            ->where('permintaan_lab.no_rawat', '=', $id)
            ->get();

        // dd($cek);

        if ($cek->count() == 1) {
            $data = DB::connection('mysqlkhanza')->table('detail_periksa_lab')
                ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'detail_periksa_lab.no_rawat')
                ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
                ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('permintaan_lab', 'permintaan_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
                ->join('periksa_lab', 'periksa_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
                ->select(
                    'pasien.no_rkm_medis',
                    'pasien.nm_pasien',
                    'pasien.jk',
                    'pasien.tgl_lahir',
                    'reg_periksa.almt_pj',
                    'reg_periksa.umurdaftar',
                    'reg_periksa.sttsumur',
                    'reg_periksa.no_rawat',
                    'reg_periksa.kd_poli',
                    'poliklinik.nm_poli',
                    'permintaan_lab.dokter_perujuk',
                    'permintaan_lab.noorder',
                    'permintaan_lab.tgl_permintaan',
                    'permintaan_lab.jam_permintaan',
                    'permintaan_lab.tgl_hasil',
                    'permintaan_lab.jam_hasil',
                    'periksa_lab.nip as petugas',
                    'periksa_lab.kd_dokter as dokter_lab'
                )
                ->where('reg_periksa.no_rawat', '=', $id)
                ->first();

            if (!empty($data)) {
                $petugas =  DB::connection('mysqlkhanza')->table('petugas')
                    ->select('petugas.nip', 'petugas.nama')
                    ->where('petugas.nip', $data->petugas)
                    ->first();
                $dokterLab =  DB::connection('mysqlkhanza')->table('dokter')
                    ->select('dokter.kd_dokter', 'dokter.nm_dokter')
                    ->where('dokter.kd_dokter', $data->dokter_lab)
                    ->first();
                $dokterPerujuk =  DB::connection('mysqlkhanza')->table('dokter')
                    ->select('dokter.kd_dokter', 'dokter.nm_dokter')
                    ->where('dokter.kd_dokter', $data->dokter_perujuk)
                    ->first();
                $hasil_periksa = DB::connection('mysqlkhanza')->table('detail_periksa_lab')
                    ->join('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'detail_periksa_lab.kd_jenis_prw')
                    ->join('template_laboratorium', 'template_laboratorium.id_template', '=', 'detail_periksa_lab.id_template')
                    ->select(
                        'jns_perawatan_lab.nm_perawatan',
                        'detail_periksa_lab.no_rawat',
                        'detail_periksa_lab.jam',
                        'detail_periksa_lab.nilai',
                        'detail_periksa_lab.nilai_rujukan',
                        'detail_periksa_lab.keterangan',
                        'template_laboratorium.Pemeriksaan',
                        'template_laboratorium.satuan'
                    )
                    ->where('detail_periksa_lab.no_rawat', $id)
                    ->get();
            }

            return view('vedika.lab', compact(
                'data',
                'petugas',
                'dokterLab',
                'dokterPerujuk',
                'hasil_periksa'
            ));
        } else {
            // dd($cek, 'multi permintaan lab');
            $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
                ->select(
                    'pasien.no_rkm_medis',
                    'pasien.nm_pasien',
                    'pasien.jk',
                    'pasien.tgl_lahir',
                    'reg_periksa.almt_pj',
                    'reg_periksa.umurdaftar',
                    'reg_periksa.sttsumur',
                    'reg_periksa.no_rawat',
                    'reg_periksa.kd_poli',
                    'dokter.nm_dokter',
                    'poliklinik.nm_poli'
                )
                ->where('reg_periksa.no_rawat', '=', $id)
                ->first();
            // dd($cek);
            foreach ($cek as $index => $loop) {
                $data[$index++] = DB::connection('mysqlkhanza')->table('detail_periksa_lab')
                    ->join('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'detail_periksa_lab.kd_jenis_prw')
                    ->join('periksa_lab', 'periksa_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
                    ->join('template_laboratorium', 'template_laboratorium.kd_jenis_prw', '=', 'detail_periksa_lab.kd_jenis_prw')
                    ->select(
                        'detail_periksa_lab.no_rawat',
                        'detail_periksa_lab.tgl_periksa',
                        'detail_periksa_lab.jam',
                        'detail_periksa_lab.id_template',
                        'detail_periksa_lab.nilai',
                        'detail_periksa_lab.nilai_rujukan',
                        'detail_periksa_lab.keterangan',
                        'jns_perawatan_lab.nm_perawatan',
                        'periksa_lab.nip as petugas',
                        'periksa_lab.dokter_perujuk',
                        'periksa_lab.kd_dokter as dokter_lab',
                        'template_laboratorium.satuan',
                        'template_laboratorium.Pemeriksaan'
                    )
                    ->where('detail_periksa_lab.jam', '=', $loop->jam_hasil)
                    // ->where('detail_periksa_lab.tgl_periksa', '=', $loop->tgl_hasil)
                    ->where('detail_periksa_lab.no_rawat', '=', $loop->no_rawat)
                    ->orderBy('detail_periksa_lab.id_template', 'ASC')
                    ->groupBy('template_laboratorium.Pemeriksaan')
                    ->get();
            }

            return view('vedika.multi_lab', compact(
                'data',
                // 'petugas',
                // 'dokterLab',
                // 'dokterPerujuk',
                'pasien',
                'cek'
            ));
        }
    }

    public function labRanap($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Ranap');
        session()->put('cucu', 'Lab');

        $id = Crypt::decrypt($id);

        $cek = DB::connection('mysqlkhanza')->table('permintaan_lab')
            ->join('dokter', 'dokter.kd_dokter', '=', 'permintaan_lab.dokter_perujuk')

            ->select(
                'permintaan_lab.noorder',
                'permintaan_lab.no_rawat',
                'permintaan_lab.tgl_permintaan',
                'permintaan_lab.jam_permintaan',
                'permintaan_lab.tgl_hasil',
                'permintaan_lab.jam_hasil',
                'permintaan_lab.status',
                'dokter.nm_dokter'
            )
            // ->where('permintaan_lab.status', '=', 'ralan')
            ->where('permintaan_lab.no_rawat', '=', $id)
            ->get();

        // dd($cek, 'multi permintaan lab');
        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'reg_periksa.almt_pj',
                'reg_periksa.no_rawat',
                'kamar_inap.kd_kamar'
            )
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        $kamar = DB::connection('mysqlkhanza')->table('kamar')
            ->join('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->select(
                'kamar.kd_kamar',
                'bangsal.nm_bangsal'
            )
            ->where('kamar.kd_kamar', '=', $pasien->kd_kamar)
            ->first();

        // dd($cek, $pasien, $kamar);
        foreach ($cek as $index => $loop) {
            $data[$index++] = DB::connection('mysqlkhanza')->table('detail_periksa_lab')
                ->join('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'detail_periksa_lab.kd_jenis_prw')
                // ->join('periksa_lab', 'periksa_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
                ->join('template_laboratorium', 'template_laboratorium.id_template', '=', 'detail_periksa_lab.id_template')
                ->select(
                    'detail_periksa_lab.no_rawat',
                    'detail_periksa_lab.tgl_periksa',
                    'detail_periksa_lab.jam',
                    'detail_periksa_lab.id_template',
                    'detail_periksa_lab.nilai',
                    'detail_periksa_lab.nilai_rujukan',
                    'detail_periksa_lab.keterangan',
                    'jns_perawatan_lab.nm_perawatan',
                    // 'periksa_lab.nip as petugas',
                    // 'periksa_lab.dokter_perujuk',
                    // 'periksa_lab.kd_dokter as dokter_lab',
                    'template_laboratorium.satuan',
                    'template_laboratorium.Pemeriksaan'
                )
                ->where('detail_periksa_lab.jam', '=', $loop->jam_hasil)
                ->where('detail_periksa_lab.tgl_periksa', '=', $loop->tgl_hasil)
                ->where('detail_periksa_lab.no_rawat', '=', $loop->no_rawat)
                // ->orderBy('detail_periksa_lab.id_template', 'ASC')
                // ->groupBy(
                //     'detail_periksa_lab.id_template',
                //     'detail_periksa_lab.nilai',
                //     'template_laboratorium.Pemeriksaan'
                // )
                ->get();
        }

        // dd($cek, $pasien, $kamar, $data);

        return view('vedika.multi_lab_ranap', compact(
            'data',
            'kamar',
            // 'dokterLab',
            // 'dokterPerujuk',
            'pasien',
            'cek'
        ));
    }

    public function perawatanRadioRajal($no_rm)
    {
        $data = DB::connection('mysqlkhanza')->table('permintaan_radiologi')
            ->join('dokter', 'dokter.kd_dokter', '=', 'permintaan_radiologi.dokter_perujuk')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_radiologi.no_rawat')
            ->join('permintaan_pemeriksaan_radiologi', 'permintaan_pemeriksaan_radiologi.noorder', '=', 'permintaan_radiologi.noorder')
            ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'reg_periksa.almt_pj',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'reg_periksa.no_rawat',
                'reg_periksa.kd_poli',
                'reg_periksa.tgl_registrasi',
                'poliklinik.nm_poli',
                'dokter.nm_dokter',
                'permintaan_radiologi.dokter_perujuk',
                'permintaan_radiologi.noorder',
                'permintaan_radiologi.tgl_permintaan',
                'permintaan_radiologi.jam_permintaan',
                'permintaan_radiologi.tgl_hasil',
                'permintaan_radiologi.jam_hasil',
                'permintaan_radiologi.status'
            )
            ->where('permintaan_radiologi.status', '=', 'Ralan')
            ->where('pasien.no_rkm_medis', '=', $no_rm)
            ->where('permintaan_radiologi.tgl_hasil', '!=', '0000-00-00')
            ->orderBy('reg_periksa.no_rawat', 'DESC')
            ->get();

        // dd($data);

        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    public function tambahRadiologi($id)
    {
        $pecah = explode('_', Crypt::decrypt($id));

        $simpan = new TambahanRadiologi();
        $simpan->no_rawat = $pecah[0];
        $simpan->no_rawat_tambahan = $pecah[1];
        $simpan->no_order = $pecah[2];
        $simpan->save();

        Session::flash('sukses', 'Data radiologi berhasil ditambahkan');

        return redirect()->back();
    }

    public function deleteRadiologi($id)
    {
        $pecah = explode('_', Crypt::decrypt($id));

        $delete = TambahanRadiologi::where('no_rawat', $pecah[0])
            ->where('no_order', $pecah[2])
            ->delete();

        Session::flash('sukses', 'Data radiologi berhasil dihapus');

        return redirect()->back();
    }

    public static function radioRajal($id)
    {
        $banding = DB::connection('mysqlkhanza')->selectOne("
                    SELECT
                        (SELECT COUNT(*) FROM periksa_radiologi WHERE status = 'Ralan' AND no_rawat = ?) AS jmlPeriksa,
                        (SELECT COUNT(*) FROM permintaan_radiologi WHERE status = 'Ralan' AND no_rawat = ?) AS jmlPermintaan
                ", [$id, $id]);

        if ($banding->jmlPeriksa == $banding->jmlPermintaan) {
            $data = DB::connection('mysqlkhanza')->table('permintaan_radiologi')
                ->join('dokter', 'dokter.kd_dokter', '=', 'permintaan_radiologi.dokter_perujuk')
                ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_radiologi.no_rawat')
                ->leftJoin('periksa_radiologi', function ($join) {
                    $join->on('periksa_radiologi.no_rawat', '=', 'permintaan_radiologi.no_rawat')
                        ->on('periksa_radiologi.tgl_periksa', '=', 'permintaan_radiologi.tgl_hasil')
                        ->on('periksa_radiologi.jam', '=', 'permintaan_radiologi.jam_hasil');
                })
                ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
                ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->select(
                    'pasien.no_rkm_medis',
                    'pasien.nm_pasien',
                    'pasien.jk',
                    'pasien.tgl_lahir',
                    'pasien.alamat',
                    'reg_periksa.almt_pj',
                    'reg_periksa.umurdaftar',
                    'reg_periksa.sttsumur',
                    'reg_periksa.no_rawat',
                    'reg_periksa.kd_poli',
                    'reg_periksa.tgl_registrasi',
                    'poliklinik.nm_poli',
                    'dokter.nm_dokter',
                    'permintaan_radiologi.dokter_perujuk',
                    'permintaan_radiologi.noorder',
                    'permintaan_radiologi.tgl_permintaan',
                    'permintaan_radiologi.jam_permintaan',
                    'periksa_radiologi.tgl_periksa as tgl_hasil',
                    'periksa_radiologi.jam as jam_hasil',
                    'periksa_radiologi.status'
                )
                ->where('periksa_radiologi.status', '=', 'Ralan')
                ->where('reg_periksa.no_rawat', '=', $id)
                ->where('permintaan_radiologi.tgl_hasil', '!=', '0000-00-00')
                ->get();
        } else {
            $data = DB::connection('mysqlkhanza')->table('permintaan_radiologi')
                ->join('dokter', 'dokter.kd_dokter', '=', 'permintaan_radiologi.dokter_perujuk')
                ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_radiologi.no_rawat')
                ->leftJoin('periksa_radiologi', function ($join) {
                    $join->on('periksa_radiologi.no_rawat', '=', 'permintaan_radiologi.no_rawat');
                })
                ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
                ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->select(
                    'pasien.no_rkm_medis',
                    'pasien.nm_pasien',
                    'pasien.jk',
                    'pasien.tgl_lahir',
                    'pasien.alamat',
                    'reg_periksa.almt_pj',
                    'reg_periksa.umurdaftar',
                    'reg_periksa.sttsumur',
                    'reg_periksa.no_rawat',
                    'reg_periksa.kd_poli',
                    'reg_periksa.tgl_registrasi',
                    'poliklinik.nm_poli',
                    'dokter.nm_dokter',
                    'permintaan_radiologi.dokter_perujuk',
                    'permintaan_radiologi.noorder',
                    'permintaan_radiologi.tgl_permintaan',
                    'permintaan_radiologi.jam_permintaan',
                    'periksa_radiologi.tgl_periksa as tgl_hasil',
                    'periksa_radiologi.jam as jam_hasil',
                    'periksa_radiologi.status'
                )
                ->where('periksa_radiologi.status', '=', 'Ralan')
                ->where('reg_periksa.no_rawat', '=', $id)
                ->where('permintaan_radiologi.tgl_hasil', '!=', '0000-00-00')
                ->groupBy('periksa_radiologi.jam')
                ->get();
        }

        if (!empty($data)) {
            $dokterRad = $hasilRad = [];
            $cekDokterRad = $cekHasilRad = [];
            foreach ($data as $listData) {

                $cek =  DB::connection('mysqlkhanza')->table('periksa_radiologi')
                    // ->join('hasil_radiologi', 'hasil_radiologi.no_rawat', '=',  'periksa_radiologi.no_rawat')
                    ->join('jns_perawatan_radiologi', 'jns_perawatan_radiologi.kd_jenis_prw', '=', 'periksa_radiologi.kd_jenis_prw')
                    ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'periksa_radiologi.kd_dokter')
                    ->select(
                        'periksa_radiologi.no_rawat',
                        'periksa_radiologi.tgl_periksa',
                        'periksa_radiologi.jam',
                        'periksa_radiologi.status',
                        'periksa_radiologi.kd_dokter',
                        'jns_perawatan_radiologi.kd_jenis_prw',
                        'jns_perawatan_radiologi.nm_perawatan',
                        // 'hasil_radiologi.tgl_periksa as tgl_hasil',
                        // 'hasil_radiologi.jam',
                        // 'hasil_radiologi.hasil',
                        'dokter.nm_dokter'
                    )
                    ->where('periksa_radiologi.no_rawat', '=', $listData->no_rawat)
                    // ->where('permintaan_pemeriksaan_radiologi.noorder', '=', $listData->noorder)
                    ->groupBy('periksa_radiologi.jam')
                    ->get();

                foreach ($cek as $dataDokter) {
                    if (!in_array($dataDokter->jam, $cekDokterRad)) {
                        array_push($cekDokterRad, $dataDokter->jam);
                        array_push($dokterRad, $dataDokter);
                    }
                }

                // if ($listData && $listData->jam_hasil) {
                //     $hasil = DB::connection('mysqlkhanza')->table('hasil_radiologi')
                //         ->where('hasil_radiologi.tgl_periksa', '=', $listData->tgl_hasil)
                //         ->where('hasil_radiologi.jam', '=', $listData->jam_hasil)
                //         ->get();

                //     // dd($cek, $hasil, $listData);
                // } else {
                $hasil = DB::connection('mysqlkhanza')->table('hasil_radiologi')
                    ->join('periksa_radiologi', function ($join) {
                        $join->on('periksa_radiologi.no_rawat', '=', 'hasil_radiologi.no_rawat')
                            ->on('periksa_radiologi.tgl_periksa', '=', 'hasil_radiologi.tgl_periksa')
                            ->on('periksa_radiologi.jam', '=', 'hasil_radiologi.jam');
                    })
                    ->select('hasil_radiologi.*')
                    ->where('hasil_radiologi.no_rawat', '=', $listData->no_rawat)
                    ->get();
                // }

                foreach ($hasil as $dataHasil) {
                    if (!in_array($dataHasil->jam, $cekHasilRad)) {
                        array_push($cekHasilRad, $dataHasil->jam);
                        array_push($hasilRad, $dataHasil);
                    }
                }
            }
        } else {
            $dokterRad = $hasilRad = null;
        }
        return array($data, $dokterRad, $hasilRad);
    }

    public static function radioRanap($id)
    {
        // session()->put('ibu', 'Vedika');
        // session()->put('anak', 'Pasien Ranap');
        // session()->put('cucu', 'Radiologi');

        // $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('permintaan_radiologi')
            // ->join('hasil_radiologi', 'hasil_radiologi.jam', '=', 'permintaan_radiologi.jam_hasil')
            // ->join('permintaan_pemeriksaan_radiologi', 'permintaan_pemeriksaan_radiologi.noorder', '=', 'permintaan_radiologi.noorder')
            ->join('dokter', 'dokter.kd_dokter', '=', 'permintaan_radiologi.dokter_perujuk')
            // ->join('periksa_radiologi', 'periksa_radiologi.no_rawat', '=', 'permintaan_radiologi.no_rawat')
            ->leftJoin('periksa_radiologi', function ($join) {
                $join->on('periksa_radiologi.no_rawat', '=', 'permintaan_radiologi.no_rawat')
                    ->on('periksa_radiologi.tgl_periksa', '=', 'permintaan_radiologi.tgl_hasil')
                    ->on('periksa_radiologi.jam', '=', 'permintaan_radiologi.jam_hasil');
            })
            ->leftJoin('jns_perawatan_radiologi', 'jns_perawatan_radiologi.kd_jenis_prw', '=', 'periksa_radiologi.kd_jenis_prw')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_radiologi.no_rawat')
            // ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'reg_periksa.almt_pj',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'reg_periksa.no_rawat',
                'reg_periksa.kd_poli',
                'periksa_radiologi.kd_dokter as dokter_rad',
                'periksa_radiologi.kd_jenis_prw',
                'dokter.nm_dokter',
                'permintaan_radiologi.dokter_perujuk',
                'permintaan_radiologi.noorder',
                'permintaan_radiologi.tgl_permintaan',
                'permintaan_radiologi.jam_permintaan',
                'periksa_radiologi.tgl_periksa as tgl_hasil',
                'periksa_radiologi.jam as jam_hasil',
                // 'permintaan_radiologi.tgl_hasil',
                // 'permintaan_radiologi.jam_hasil',
                'permintaan_radiologi.status',
                'jns_perawatan_radiologi.nm_perawatan'
                // 'hasil_radiologi.tgl_periksa',
                // 'hasil_radiologi.jam',
                // 'hasil_radiologi.hasil'
            )
            ->where('reg_periksa.no_rawat', '=', $id)
            ->where('permintaan_radiologi.status', '=', 'Ranap')
            // ->groupBy('periksa_radiologi.kd_jenis_prw')
            // ->groupBy('permintaan_radiologi.noorder')
            ->get();

        // dd($data);


        if (!empty($data)) {
            $dokterRad = $hasilRad = [];
            foreach ($data as $listData) {
                // dd($listData);
                $cek =  DB::connection('mysqlkhanza')->table('periksa_radiologi')
                    // ->join('hasil_radiologi', 'hasil_radiologi.no_rawat', '=',  'periksa_radiologi.no_rawat')
                    ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'periksa_radiologi.kd_dokter')
                    ->select(
                        'periksa_radiologi.no_rawat',
                        'periksa_radiologi.tgl_periksa',
                        'periksa_radiologi.jam',
                        'periksa_radiologi.status',
                        'periksa_radiologi.kd_dokter',
                        // 'hasil_radiologi.tgl_periksa',
                        // 'hasil_radiologi.jam',
                        // 'hasil_radiologi.hasil',
                        'dokter.nm_dokter'
                    )
                    ->where('periksa_radiologi.no_rawat', '=', $listData->no_rawat)
                    // ->orWhere('periksa_radiologi.jam', '=', $listData->jam_hasil)
                    ->get();

                foreach ($cek as $dataDokter) {
                    array_push($dokterRad, $dataDokter);
                }

                if ($listData && $listData->jam_hasil) {
                    $hasil = DB::connection('mysqlkhanza')->table('hasil_radiologi')
                        // ->where('hasil_radiologi.tgl_periksa', '=', $listData->tgl_hasil)
                        ->where('hasil_radiologi.no_rawat', '=', $listData->no_rawat)
                        // ->orWhere('hasil_radiologi.jam', '=', $listData->jam_hasil)
                        ->get();
                } else {
                    $hasil = DB::connection('mysqlkhanza')->table('hasil_radiologi')
                        ->join('periksa_radiologi', function ($join) {
                            $join->on('periksa_radiologi.no_rawat', '=', 'hasil_radiologi.no_rawat')
                                ->on('periksa_radiologi.tgl_periksa', '=', 'hasil_radiologi.tgl_periksa')
                                ->on('periksa_radiologi.jam', '=', 'hasil_radiologi.jam');
                        })
                        ->select('hasil_radiologi.*')
                        ->where('hasil_radiologi.no_rawat', '=', $listData->no_rawat)
                        ->get();
                }
            }
            if (!empty($hasil)) {
                foreach ($hasil as $dataHasil) {
                    array_push($hasilRad, $dataHasil);
                }
            }
        } else {
            $dokterRad = null;
        }
        // dd($data, $dokterRad, $hasilRad);
        return array($data, $dokterRad, $hasilRad);

        // return view('vedika.radiologi', compact(
        //     'data',
        //     'dokterRad'
        // ));
    }

    public function radioTambahan($id)
    {
        $data = DB::connection('mysqlkhanza')->table('permintaan_radiologi')
            ->join('dokter', 'dokter.kd_dokter', '=', 'permintaan_radiologi.dokter_perujuk')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_radiologi.no_rawat')
            ->join('permintaan_pemeriksaan_radiologi', 'permintaan_pemeriksaan_radiologi.noorder', '=', 'permintaan_radiologi.noorder')
            ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'reg_periksa.almt_pj',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'reg_periksa.no_rawat',
                'reg_periksa.kd_poli',
                'reg_periksa.tgl_registrasi',
                'poliklinik.nm_poli',
                'dokter.nm_dokter',
                'permintaan_radiologi.dokter_perujuk',
                'permintaan_radiologi.noorder',
                'permintaan_radiologi.tgl_permintaan',
                'permintaan_radiologi.jam_permintaan',
                'permintaan_radiologi.tgl_hasil',
                'permintaan_radiologi.jam_hasil',
                'permintaan_radiologi.status'

            )
            ->where('permintaan_radiologi.status', '=', 'Ralan')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->where('permintaan_radiologi.tgl_hasil', '!=', '0000-00-00')
            ->get();

        // dd($data, 'test');

        if (!empty($data)) {
            $dokterRad = $hasilRad = [];
            $cekDokterRad = $cekHasilRad = [];
            foreach ($data as $listData) {

                $cek =  DB::connection('mysqlkhanza')->table('periksa_radiologi')
                    ->join('jns_perawatan_radiologi', 'jns_perawatan_radiologi.kd_jenis_prw', '=', 'periksa_radiologi.kd_jenis_prw')
                    ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'periksa_radiologi.kd_dokter')
                    ->select(
                        'periksa_radiologi.no_rawat',
                        'periksa_radiologi.tgl_periksa',
                        'periksa_radiologi.jam',
                        'periksa_radiologi.status',
                        'periksa_radiologi.kd_dokter',
                        'jns_perawatan_radiologi.kd_jenis_prw',
                        'jns_perawatan_radiologi.nm_perawatan',
                        'dokter.nm_dokter'
                    )
                    ->where('periksa_radiologi.no_rawat', '=', $listData->no_rawat)
                    ->groupBy('periksa_radiologi.jam')
                    ->get();

                foreach ($cek as $dataDokter) {
                    if (!in_array($dataDokter->jam, $cekDokterRad)) {
                        array_push($cekDokterRad, $dataDokter->jam);
                        array_push($dokterRad, $dataDokter);
                    }
                }

                $hasil = DB::connection('mysqlkhanza')->table('hasil_radiologi')
                    ->where('hasil_radiologi.tgl_periksa', '=', $listData->tgl_hasil)
                    ->where('hasil_radiologi.jam', '=', $listData->jam_hasil)
                    ->get();

                foreach ($hasil as $dataHasil) {
                    if (!in_array($dataHasil->jam, $cekHasilRad)) {
                        array_push($cekHasilRad, $dataHasil->jam);
                        array_push($hasilRad, $dataHasil);
                    }
                }

                // array_push($dokterRad, $cek);
                // array_push($hasilRad, $hasil);
            }
        } else {
            $dokterRad = $hasilRad = null;
        }

        // dd($data, $dokterRad, $hasilRad, $cekHasilRad);
        return array($data, $dokterRad, $hasilRad);
    }

    public function triase($id)
    {
        // session()->put('ibu', 'Vedika');
        // session()->put('anak', 'Pasien Rajal');
        // session()->put('cucu', 'Triase');

        // $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('data_triase_igd')
            ->join('master_triase_macam_kasus', 'master_triase_macam_kasus.kode_kasus', '=', 'data_triase_igd.kode_kasus')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'data_triase_igd.no_rawat')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.no_ktp',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'data_triase_igd.tgl_kunjungan',
                'data_triase_igd.cara_masuk',
                'master_triase_macam_kasus.macam_kasus',
                'data_triase_igd.tekanan_darah',
                'data_triase_igd.nadi',
                'data_triase_igd.pernapasan',
                'data_triase_igd.suhu',
                'data_triase_igd.saturasi_o2',
                'data_triase_igd.nyeri'
            )
            ->where('data_triase_igd.no_rawat', '=', $id)
            ->first();

        // dd($data);
        $primer = DB::connection('mysqlkhanza')->table('data_triase_igdprimer')
            ->join('petugas', 'petugas.nip', '=', 'data_triase_igdprimer.nik')
            ->select(
                'data_triase_igdprimer.no_rawat',
                'data_triase_igdprimer.keluhan_utama',
                'data_triase_igdprimer.kebutuhan_khusus',
                'data_triase_igdprimer.catatan',
                'data_triase_igdprimer.plan',
                'data_triase_igdprimer.tanggaltriase',
                'petugas.nama',
                'petugas.nip'
            )
            ->where('data_triase_igdprimer.no_rawat', '=', $id)
            ->first();

        $sekunder = DB::connection('mysqlkhanza')->table('data_triase_igdsekunder')
            ->join('petugas', 'petugas.nip', '=', 'data_triase_igdsekunder.nik')
            ->select(
                'data_triase_igdsekunder.no_rawat',
                'data_triase_igdsekunder.anamnesa_singkat',
                'data_triase_igdsekunder.catatan',
                'data_triase_igdsekunder.plan',
                'data_triase_igdsekunder.tanggaltriase',
                'petugas.nama',
                'petugas.nip'
            )
            ->where('data_triase_igdsekunder.no_rawat', '=', $id)
            ->first();

        $skala[1] =  DB::connection('mysqlkhanza')->table('master_triase_skala1')
            ->join('data_triase_igddetail_skala1', 'data_triase_igddetail_skala1.kode_skala1', '=', 'master_triase_skala1.kode_skala1')
            ->join('master_triase_pemeriksaan', 'master_triase_pemeriksaan.kode_pemeriksaan', '=', 'master_triase_skala1.kode_pemeriksaan')
            ->select(
                'data_triase_igddetail_skala1.no_rawat',
                'master_triase_pemeriksaan.nama_pemeriksaan',
                'master_triase_skala1.pengkajian_skala1 as pengkajian_skala'
            )
            ->where('data_triase_igddetail_skala1.no_rawat', '=', $id)
            ->get();

        $skala[2] =  DB::connection('mysqlkhanza')->table('master_triase_skala2')
            ->join('data_triase_igddetail_skala2', 'data_triase_igddetail_skala2.kode_skala2', '=', 'master_triase_skala2.kode_skala2')
            ->join('master_triase_pemeriksaan', 'master_triase_pemeriksaan.kode_pemeriksaan', '=', 'master_triase_skala2.kode_pemeriksaan')
            ->select(
                'data_triase_igddetail_skala2.no_rawat',
                'master_triase_pemeriksaan.nama_pemeriksaan',
                'master_triase_skala2.pengkajian_skala2 as pengkajian_skala'
            )
            ->where('data_triase_igddetail_skala2.no_rawat', '=', $id)
            ->get();

        $skala[3] =  DB::connection('mysqlkhanza')->table('master_triase_skala3')
            ->join('data_triase_igddetail_skala3', 'data_triase_igddetail_skala3.kode_skala3', '=', 'master_triase_skala3.kode_skala3')
            ->join('master_triase_pemeriksaan', 'master_triase_pemeriksaan.kode_pemeriksaan', '=', 'master_triase_skala3.kode_pemeriksaan')
            ->select(
                'data_triase_igddetail_skala3.no_rawat',
                'master_triase_pemeriksaan.nama_pemeriksaan',
                'master_triase_skala3.pengkajian_skala3 as pengkajian_skala'
            )
            ->where('data_triase_igddetail_skala3.no_rawat', '=', $id)
            ->get();

        $skala[4] =  DB::connection('mysqlkhanza')->table('master_triase_skala4')
            ->join('data_triase_igddetail_skala4', 'data_triase_igddetail_skala4.kode_skala4', '=', 'master_triase_skala4.kode_skala4')
            ->join('master_triase_pemeriksaan', 'master_triase_pemeriksaan.kode_pemeriksaan', '=', 'master_triase_skala4.kode_pemeriksaan')
            ->select(
                'data_triase_igddetail_skala4.no_rawat',
                'master_triase_pemeriksaan.nama_pemeriksaan',
                'master_triase_skala4.pengkajian_skala4 as pengkajian_skala'
            )
            ->where('data_triase_igddetail_skala4.no_rawat', '=', $id)
            ->get();

        $skala[5] =  DB::connection('mysqlkhanza')->table('master_triase_skala5')
            ->join('data_triase_igddetail_skala5', 'data_triase_igddetail_skala5.kode_skala5', '=', 'master_triase_skala5.kode_skala5')
            ->join('master_triase_pemeriksaan', 'master_triase_pemeriksaan.kode_pemeriksaan', '=', 'master_triase_skala5.kode_pemeriksaan')
            ->select(
                'data_triase_igddetail_skala5.no_rawat',
                'master_triase_pemeriksaan.nama_pemeriksaan',
                'master_triase_skala5.pengkajian_skala5 as pengkajian_skala'
            )
            ->where('data_triase_igddetail_skala5.no_rawat', '=', $id)
            ->get();

        // dd($data, $primer, $sekunder, $skala[1], $skala[2], $skala[3], $skala[4], $skala[5]);
        return array($data, $primer, $sekunder, $skala);

        // return view('vedika.triase', compact(
        //     'data',
        //     'primer',
        //     'sekunder',
        //     'skala'
        // ));
    }

    public static function buktiPelayanan($id)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'reg_periksa.almt_pj',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.stts',
                'reg_periksa.no_rawat',
                'reg_periksa.kd_poli',
                'poliklinik.nm_poli',
                'dokter.nm_dokter',
                'dokter.kd_dokter',
                'penjab.png_jawab'
            )
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        $diagnosa = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
            ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            ->select(
                'diagnosa_pasien.no_rawat',
                'diagnosa_pasien.kd_penyakit',
                'diagnosa_pasien.prioritas',
                'diagnosa_pasien.status',
                'penyakit.nm_penyakit'
            )
            ->where('diagnosa_pasien.status', '=', 'Ralan')
            ->where('diagnosa_pasien.no_rawat', '=', $id)
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
            ->where('prosedur_pasien.status', '=', 'Ralan')
            ->where('prosedur_pasien.no_rawat', '=', $id)
            ->get();

        return array($diagnosa, $prosedur, $data);
    }

    public function ringkasanIgd($id)
    {
        // session()->put('ibu', 'Vedika');
        // session()->put('anak', 'Pasien Rajal');
        // session()->put('cucu', 'Ringkasan Pasien Gawat Darurat');

        // $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('penilaian_medis_igd', 'penilaian_medis_igd.no_rawat', '=', 'reg_periksa.no_rawat')
            // ->join('resume_pasien', 'resume_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'pasien.no_ktp',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.stts',
                'reg_periksa.no_rawat',
                'reg_periksa.kd_poli',
                'dokter.nm_dokter',
                'dokter.kd_dokter',
                'penilaian_medis_igd.*'
            )
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        $resume = DB::connection('mysqlkhanza')->table('ringkasan_pasien_igd')
            ->select(
                'ringkasan_pasien_igd.no_rawat',
                'ringkasan_pasien_igd.kondisi_pulang',
                'ringkasan_pasien_igd.obat_pulang',
                'ringkasan_pasien_igd.tindak_lanjut',
                'ringkasan_pasien_igd.edukasi',
                'ringkasan_pasien_igd.kebutuhan',
                'ringkasan_pasien_igd.tgl_selesai'
            )
            ->where('ringkasan_pasien_igd.no_rawat', '=', $id)
            ->first();

        // dd($id, $data, $resume);
        return array($data, $resume);

        // return view('vedika.resume_igd', compact(
        //     'data',
        //     'resume'
        // ));
    }

    public function awalMedisIgd($no_rawat)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('penilaian_medis_igd', 'penilaian_medis_igd.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'pasien.no_ktp',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.stts',
                'reg_periksa.no_rawat',
                'reg_periksa.kd_poli',
                'dokter.nm_dokter',
                'dokter.kd_dokter',
                'penilaian_medis_igd.diagnosis'
            )
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        return array($data);
    }

    public function pemeriksaanRalan($id, $idDokter)
    {
        $data = DB::connection('mysqlkhanza')->table('pemeriksaan_ralan')
            ->join('pegawai', 'pegawai.nik', '=', 'pemeriksaan_ralan.nip')
            ->select(
                'pemeriksaan_ralan.no_rawat',
                'pemeriksaan_ralan.tgl_perawatan',
                'pemeriksaan_ralan.jam_rawat',
                'pemeriksaan_ralan.suhu_tubuh',
                'pemeriksaan_ralan.tensi',
                'pemeriksaan_ralan.nadi',
                'pemeriksaan_ralan.respirasi',
                'pemeriksaan_ralan.tinggi',
                'pemeriksaan_ralan.berat',
                'pemeriksaan_ralan.spo2',
                'pemeriksaan_ralan.gcs',
                'pemeriksaan_ralan.kesadaran',
                'pemeriksaan_ralan.keluhan',
                'pemeriksaan_ralan.pemeriksaan',
                'pemeriksaan_ralan.rtl',
                'pemeriksaan_ralan.penilaian',
                'pemeriksaan_ralan.instruksi',
                'pemeriksaan_ralan.evaluasi',
                'pemeriksaan_ralan.nip',
                'pegawai.nama',
                'pegawai.bidang'
            )
            ->where('pemeriksaan_ralan.no_rawat', '=', $id)
            ->where('pemeriksaan_ralan.nip', '=', $idDokter)
            ->first();

        return $data;
    }

    public function dataSoap($id)
    {
        $data = DB::connection('mysqlkhanza')->table('pemeriksaan_ralan')
            ->join('pegawai', 'pegawai.nik', '=', 'pemeriksaan_ralan.nip')
            ->select(
                'pemeriksaan_ralan.no_rawat',
                'pemeriksaan_ralan.tgl_perawatan',
                'pemeriksaan_ralan.jam_rawat',
                'pemeriksaan_ralan.suhu_tubuh',
                'pemeriksaan_ralan.tensi',
                'pemeriksaan_ralan.nadi',
                'pemeriksaan_ralan.respirasi',
                'pemeriksaan_ralan.tinggi',
                'pemeriksaan_ralan.berat',
                'pemeriksaan_ralan.spo2',
                'pemeriksaan_ralan.gcs',
                'pemeriksaan_ralan.keluhan',
                'pemeriksaan_ralan.pemeriksaan',
                'pemeriksaan_ralan.alergi',
                'pemeriksaan_ralan.rtl',
                'pemeriksaan_ralan.penilaian',
                'pemeriksaan_ralan.instruksi',
                'pemeriksaan_ralan.evaluasi',
                'pegawai.nama as petugas',
                'pegawai.jbtn as jabatan_petugas'
            )
            ->where('pemeriksaan_ralan.no_rawat', '=', $id)
            ->first();

        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    public function getPsi($no_rawat)
    {
        $data =  DB::connection('mysqlkhanza')->table('penilaian_pneumonia_severity')
            ->select('*')
            ->where('penilaian_pneumonia_severity.no_rawat', '=', $no_rawat)
            ->orderBy('penilaian_pneumonia_severity.tanggal', 'DESC')
            ->first();

        // dd($data);

        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    public function getCurb($no_rawat)
    {
        $data =  DB::connection('mysqlkhanza')->table('penilaian_lain_skoringcurb')
            ->join('pegawai', 'pegawai.nik', '=', 'penilaian_lain_skoringcurb.kd_dokter')
            ->select(
                'penilaian_lain_skoringcurb.*', // Semua kolom dari penilaian_lain_skoringcurb
                'pegawai.nama'
            )
            ->where('penilaian_lain_skoringcurb.no_rawat', '=', $no_rawat)
            ->orderBy('penilaian_lain_skoringcurb.tanggal', 'DESC')
            ->first();

        // dd($data);

        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    public function getUsg($no_rawat)
    {
        $data =  DB::connection('mysqlkhanza')->table('hasil_pemeriksaan_usg')
            ->join('pegawai', 'pegawai.nik', '=', 'hasil_pemeriksaan_usg.kd_dokter')
            ->select(
                'hasil_pemeriksaan_usg.*', // Semua kolom dari hasil_pemeriksaan_usg
                'pegawai.nama'
            )
            ->where('hasil_pemeriksaan_usg.no_rawat', '=', $no_rawat)
            ->orderBy('hasil_pemeriksaan_usg.tanggal', 'DESC')
            ->first();

        // dd($data);

        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    public function getUsgRanap($no_rawat)
    {
        $data =  DB::connection('mysqlkhanza')->table('hasil_pemeriksaan_usg_ranap')
            ->join('pegawai', 'pegawai.nik', '=', 'hasil_pemeriksaan_usg_ranap.kd_dokter')
            ->select(
                'hasil_pemeriksaan_usg_ranap.*', // Semua kolom dari hasil_pemeriksaan_usg
                'pegawai.nama'
            )
            ->where('hasil_pemeriksaan_usg_ranap.no_rawat', '=', $no_rawat)
            ->orderBy('hasil_pemeriksaan_usg_ranap.tanggal', 'DESC')
            ->first();

        // dd($data);

        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    public function getUsgGynecologi($no_rawat)
    {
        $data =  DB::connection('mysqlkhanza')->table('hasil_pemeriksaan_usg_gynecologi')
            ->join('pegawai', 'pegawai.nik', '=', 'hasil_pemeriksaan_usg_gynecologi.kd_dokter')
            ->select(
                'hasil_pemeriksaan_usg_gynecologi.*', // Semua kolom dari hasil_pemeriksaan_usg
                'pegawai.nama'
            )
            ->where('hasil_pemeriksaan_usg_gynecologi.no_rawat', '=', $no_rawat)
            ->orderBy('hasil_pemeriksaan_usg_gynecologi.tanggal', 'DESC')
            ->first();

        // dd($data);

        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    public function getSpiro($no_rawat)
    {
        $data =  DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('penilaian_spiro', 'reg_periksa.no_rawat', '=', 'penilaian_spiro.no_rawat')
            ->leftJoin('petugas', 'petugas.nip', '=', 'penilaian_spiro.kd_petugas')
            ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'penilaian_spiro.kd_dokter')
            ->select(
                'penilaian_spiro.*', // Semua kolom dari hasil_pemeriksaan_usg
                'reg_periksa.no_rawat',
                'petugas.nama',
                'dokter.nm_dokter'
            )
            ->where('reg_periksa.no_rawat', '=', $no_rawat)
            ->orderBy('penilaian_spiro.tanggal', 'DESC')
            ->first();

        // dd($data);
        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    public function getTransfusi($no_rawat)
    {
        $data =  DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penilaian_observasi_transfusi_darah', 'reg_periksa.no_rawat', '=', 'penilaian_observasi_transfusi_darah.no_rawat')
            ->leftJoin('petugas', 'petugas.nip', '=', 'penilaian_observasi_transfusi_darah.kd_petugas')
            ->leftJoin('petugas as petugas2', 'petugas2.nip', '=', 'penilaian_observasi_transfusi_darah.kd_petugas_2')
            ->leftJoin('petugas as petugas3', 'petugas3.nip', '=', 'penilaian_observasi_transfusi_darah.kd_petugas_3')
            ->select(
                'penilaian_observasi_transfusi_darah.*',
                'reg_periksa.no_rawat',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'petugas.nama as petugas1',
                'petugas2.nama as petugas2',
                'petugas3.nama as petugas3'
            )
            ->where('reg_periksa.no_rawat', '=', $no_rawat)
            ->orderBy('penilaian_observasi_transfusi_darah.tanggal', 'asc')
            ->get();

        // dd($data);
        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    public function getObservasiFibrinolitic($no_rawat)
    {
        $data =  DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penilaian_observasi_fibrinolitik', 'reg_periksa.no_rawat', '=', 'penilaian_observasi_fibrinolitik.no_rawat')
            ->leftJoin('petugas', 'petugas.nip', '=', 'penilaian_observasi_fibrinolitik.kd_petugas')
            ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'penilaian_observasi_fibrinolitik.kd_dokter')
            ->select(
                'penilaian_observasi_fibrinolitik.*',
                'reg_periksa.no_rawat',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.alamat',
                'pasien.jk',
                'petugas.nama as nm_petugas',
                'dokter.nm_dokter'
            )
            ->where('reg_periksa.no_rawat', '=', $no_rawat)
            ->orderBy('penilaian_observasi_fibrinolitik.tanggal', 'asc')
            ->orderBy('penilaian_observasi_fibrinolitik.jam', 'asc')
            ->get();

        // dd($data);
        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    public function getCheckFibrinolitic($no_rawat)
    {
        $data =  DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penilaian_awal_checklist_fibrinolitik', 'reg_periksa.no_rawat', '=', 'penilaian_awal_checklist_fibrinolitik.no_rawat')
            ->leftJoin('petugas', 'petugas.nip', '=', 'penilaian_awal_checklist_fibrinolitik.kd_petugas')
            ->select(
                'penilaian_awal_checklist_fibrinolitik.*',
                'reg_periksa.no_rawat',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.alamat',
                'pasien.jk',
                'petugas.nama as nm_petugas'
            )
            ->where('reg_periksa.no_rawat', '=', $no_rawat)
            ->first();

        // dd($data);
        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    public function getLaporanTindakanMata($no_rawat)
    {
        $data =  DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penilaian_laporan_tindakan_ralan', 'reg_periksa.no_rawat', '=', 'penilaian_laporan_tindakan_ralan.no_rawat')
            ->join('petugas', 'petugas.nip', '=', 'penilaian_laporan_tindakan_ralan.kd_petugas')
            ->join('dokter', 'dokter.kd_dokter', '=', 'penilaian_laporan_tindakan_ralan.kd_dokter')
            ->select(
                'penilaian_laporan_tindakan_ralan.*',
                'reg_periksa.no_rawat',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.alamat',
                'pasien.jk',
                'pasien.tgl_lahir',
                'petugas.nama as nm_petugas',
                'dokter.nm_dokter'
            )
            ->where('reg_periksa.no_rawat', '=', $no_rawat)
            ->first();

        // dd($data);
        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    public static function berkas($id)
    {
        // session()->put('ibu', 'Vedika');
        // session()->put('anak', 'Pasien Rajal');
        // session()->put('cucu', 'Berkas');

        // $id = Crypt::decrypt($id);
        // dd($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
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
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.no_peserta',
                'pasien.tgl_lahir',
                'pasien.jk',
                'dokter.nm_dokter'
            )
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        // $berkas = BerkasVedika::where('no_rawat', $id)
        //     ->get();
        // $master = MasterBerkasVedika::all();

        // dd($data);

        if (!empty($data)) {
            if ($data->nm_poli == 'REHABILITASI MEDIK') {
                $getRm = DB::connection('mysqlkhanza')->table('reg_periksa')
                    ->select(
                        'reg_periksa.no_rawat',
                        'reg_periksa.no_rkm_medis',
                        'reg_periksa.tgl_registrasi'
                    )
                    ->where('no_rkm_medis', $data->no_rkm_medis)
                    ->where('tgl_registrasi', '<=', $data->tgl_registrasi)
                    ->orderBy('tgl_registrasi', 'DESC')
                    ->skip(0)->take(3)
                    ->get();

                // dd($getRm);
                $berkas = [];

                foreach ($getRm as $listRm) {
                    $listBerkas = DB::connection('mysqlkhanza')->table('berkas_digital_perawatan')
                        ->join('master_berkas_digital', 'master_berkas_digital.kode', '=', 'berkas_digital_perawatan.kode')
                        ->select(
                            'master_berkas_digital.nama',
                            'berkas_digital_perawatan.kode',
                            'berkas_digital_perawatan.lokasi_file',
                            'berkas_digital_perawatan.no_rawat'
                        )
                        ->where('berkas_digital_perawatan.no_rawat', '=', $listRm->no_rawat)
                        ->get();

                    foreach ($listBerkas as $listArray) {
                        if ($listArray->kode == '015') {
                            if ($listArray->no_rawat == $id) {
                                array_push($berkas, $listArray);
                            }
                        } else {
                            array_push($berkas, $listArray);
                        }
                    }
                }
                $berkas = (object)$berkas;
            } else {
                goto berkasTunggal;
            }
        } else {
            berkasTunggal:
            // $berkas = DB::connection('mysqlkhanza')->table('berkas_digital_perawatan')
            //     ->join('master_berkas_digital', 'master_berkas_digital.kode', '=', 'berkas_digital_perawatan.kode')
            //     ->select(
            //         'master_berkas_digital.nama',
            //         'berkas_digital_perawatan.lokasi_file',
            //         'berkas_digital_perawatan.no_rawat'
            //     )
            //     ->where('berkas_digital_perawatan.no_rawat', '=', $id)
            //     ->get();

            //Tambah berkas Echo/013 dan Spiro/018
            $arrayBerkas = [];
            // foreach ($berkas as $listBerkas) {
            //     array_push($arrayBerkas, $listBerkas);
            // }

            // dd('berkastunggal');

            $getRm = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->select(
                    'reg_periksa.no_rawat',
                    'reg_periksa.no_rkm_medis',
                    'reg_periksa.stts',
                    'reg_periksa.tgl_registrasi'
                )
                ->where('no_rkm_medis', $data->no_rkm_medis)
                ->where('tgl_registrasi', '<=', $data->tgl_registrasi)
                ->where('stts', '!=', 'Batal')
                ->orderBy('tgl_registrasi', 'DESC')
                ->skip(0)->take(3)
                ->get();

            // dd($getRm);

            foreach ($getRm as $listRm) {
                $listBerkas = DB::connection('mysqlkhanza')->table('berkas_digital_perawatan')
                    ->join('master_berkas_digital', 'master_berkas_digital.kode', '=', 'berkas_digital_perawatan.kode')
                    ->select(
                        'master_berkas_digital.nama',
                        'berkas_digital_perawatan.kode',
                        'berkas_digital_perawatan.lokasi_file',
                        'berkas_digital_perawatan.no_rawat'
                    )
                    ->where('berkas_digital_perawatan.no_rawat', '=', $listRm->no_rawat)
                    ->get();

                foreach ($listBerkas as $listArray) {
                    if ($listArray->no_rawat == $id) {
                        array_push($arrayBerkas, $listArray);
                    }
                    // else {
                    //     if ($listArray->kode == '013' || $listArray->kode == '018' || $listArray->kode == '037') {
                    //         $cariBerkas = VedikaController::berkasPilihan($data->no_rkm_medis, '018');
                    //         dd('masuk');

                    //         array_push($arrayBerkas, $listArray);
                    //     };
                    // }
                }
            }
            // dd($arrayBerkas);


            //Mencari berkas 013,018,037 terakhir
            $pencarian = ['013', '018', '037'];
            foreach ($pencarian as $listCari) {
                $cekBerkasExists = array_filter($arrayBerkas, function ($item) use ($listCari) {
                    return $item->kode === $listCari;
                });

                if (empty($cekBerkasExists)) {
                    $cariBerkas = VedikaController::berkasPilihan($data->no_rkm_medis, $listCari);
                    if ($cariBerkas) {
                        array_push($arrayBerkas, $cariBerkas);
                    }
                }
            }

            // dd($arrayBerkas);
            if (!empty($arrayBerkas)) {
                $berkas = (object) $arrayBerkas;
            } else {
                $berkas = null;
            }
        }

        // dd($berkas);

        $master =  DB::connection('mysqlkhanza')->table('master_berkas_digital')
            ->get();
        $path = Setting::where('nama', 'webappz_berkasrawat')->first();

        return array($berkas, $master, $path);
    }

    public function berkasRanap($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Ranap');
        session()->put('cucu', 'Berkas');

        $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
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
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.no_peserta',
                'pasien.tgl_lahir',
                'pasien.jk',
                'dokter.nm_dokter',
                'diagnosa_pasien.kd_penyakit',
                'diagnosa_pasien.prioritas',
                'penyakit.nm_penyakit'
            )
            ->where('reg_periksa.no_rawat', '=', $id)
            ->where('diagnosa_pasien.prioritas', '=', 1)
            ->first();

        // $berkas = BerkasVedika::where('no_rawat', $id)
        //     ->get();
        // $master = MasterBerkasVedika::all();

        $berkas = DB::connection('mysqlkhanza')->table('berkas_digital_perawatan')
            ->join('master_berkas_digital', 'master_berkas_digital.kode', '=', 'berkas_digital_perawatan.kode')
            ->select(
                'master_berkas_digital.nama',
                'berkas_digital_perawatan.lokasi_file',
                'berkas_digital_perawatan.no_rawat'
            )
            ->where('berkas_digital_perawatan.no_rawat', '=', $id)
            ->get();

        $master =  DB::connection('mysqlkhanza')->table('master_berkas_digital')
            ->get();

        $path = Setting::where('nama', 'webappz_berkasrawat')
            ->first();
        // dd($data, $berkas);

        return view('vedika.berkas', compact('data', 'berkas', 'master', 'path'));
    }

    public static function berkasPilihan($noRM, $idberkas)
    {
        $berkas = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('berkas_digital_perawatan', 'berkas_digital_perawatan.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('master_berkas_digital', 'master_berkas_digital.kode', '=', 'berkas_digital_perawatan.kode')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'master_berkas_digital.nama',
                'berkas_digital_perawatan.kode',
                'berkas_digital_perawatan.lokasi_file'
            )
            ->where('reg_periksa.no_rkm_medis', $noRM)
            ->where('berkas_digital_perawatan.kode', $idberkas)
            ->orderBy('reg_periksa.tgl_registrasi', 'DESC')
            ->first();

        if ($berkas) {
            return $berkas;
        } else {
            return null;
        }
    }

    public function berkasStore(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:pdf,jpg,jpeg|max:2048',
        ], [
            'file.mimes' => 'File yang diperbolehkan adalah file PDF, JPG/JPEG!',
            'file.max' => 'Ukuran file maksimal 2MB!'
        ]);


        $str = $request->master_berkas;
        $split = explode("-", $str);

        $data = new BerkasVedika();
        $data->no_rawat = $request->no_rawat;
        $data->master_berkas_vedika_id = $split[0];
        // $data->file = $request->file;

        //aksi file
        $file = $request->file('file');
        $tgl_registrasi = Carbon::parse($request->tgl_registrasi)->format('Ymd');
        $waktu_upload = Carbon::now()->format('YmdHis');
        $extension = $file->getClientOriginalExtension();
        $nama_file = substr($request->no_rawat, -6) . "_" . $split[1] . "_" . $waktu_upload . '.' . $extension;
        // isi dengan nama folder tempat kemana file diupload
        $tujuan_upload = "berkas_vedika/" . $tgl_registrasi;

        // dd($nama_file);
        $file->move($tujuan_upload, $nama_file);

        $data->file = $nama_file;
        $data->lokasi_berkas = $tujuan_upload;

        // dd($data);

        $data->save();

        $id = Crypt::encrypt($request->no_rawat);

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        return redirect("/vedika/rajal/$id/berkas");
    }

    public function berkasUpload(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:pdf,jpg,jpeg|max:2048',
        ], [
            'file.mimes' => 'File yang diperbolehkan adalah file PDF, JPG/JPEG!',
            'file.max' => 'Ukuran file maksimal 2MB!'
        ]);

        // dd($request);
        $str = $request->master_berkas;
        $split = explode("-", $str);
        $nama_master = str_replace(' ', '_', $split[1]);

        $data['no_rawat'] = $request->no_rawat;
        $data['kode'] = $split[0];
        // $data->file = $request->file;

        //aksi file
        $file = $request->file('file');
        $tgl_registrasi = Carbon::parse($request->tgl_registrasi)->format('Ymd');
        $waktu_upload = Carbon::now()->format('His');
        $extension = $file->getClientOriginalExtension();
        $nama_file = $tgl_registrasi . '_' . $waktu_upload . '_' . substr($request->no_rawat, -6) . "_" . $nama_master . '.' . $extension;

        // isi dengan nama folder tempat kemana file diupload
        $path = Setting::where('nama', 'webappz_berkasrawat')
            ->first();
        $tujuan_upload = $path->base_url . "pages/upload";

        // dd($nama_file);
        // $file->move($tujuan_upload, $nama_file);

        // Storage::disk('sftp')->putFileAs($file, new File('pages/upload/'), $nama_file);
        // Storage::put($nama_file, $file);
        $pindah = $request->file('file')->storeAs(
            'pages/upload',
            $nama_file,
            'sftp'
        );

        // Storage::disk('sftp')->setVisibility('pages/upload' . $nama_file, 'public');


        $data['lokasi_file'] = 'pages/upload/' . $nama_file;
        // dd($data, $pindah);

        DB::connection('mysqlkhanza')->table('berkas_digital_perawatan')->insert($data);

        $id = Crypt::encrypt($request->no_rawat);

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        // dd(Session(''))

        if (Session('anak') == 'Pasien Rajal')
            return redirect("/vedika/rajal/$id/detail");
        else
            return redirect("/vedika/ranap/$id/detail");
    }

    public function berkasShow($id)
    {
        $id = Crypt::decrypt($id);

        // dd($id);

        $cek = Storage::disk('sftp')->exists($id);
        if ($cek == true) {

            $realFile = explode('/', $id);
            //Cek file di lokal ada tidak
            $cek2 = Storage::disk()->exists($realFile[2]);
            if ($cek2 == false) {
                Storage::disk('local')
                    ->put("$realFile[2]", Storage::disk('sftp')
                        ->get($id));
                $contents = Storage::disk('sftp')->get($id);
                // Storage::disk('local')->put("berkas_vedika/$realFile[2]", $contents);
                file_put_contents("berkas_vedika/$realFile[2]", $contents);
            }


            // $path = Storage::path('berkas_vedika/' . $realFile[2]);
            // return response()->file($path);

            $this->file_to_download = 'berkas_vedika/' . $realFile[2];
            //return response()->streamDownload(function () {
            //    echo file_get_contents($this->file_to_download);
            //}, $file.'.pdf');
            return response()->file($this->file_to_download);
        } else {
            abort(404);
        }
    }

    public function berkasDelete($id)
    {
        $id = Crypt::decrypt($id);
        // $delete = BerkasVedika::find($id);

        // $file = public_path($delete->lokasi_berkas . '/' . $delete->file);

        // if (File::exists($file)) {
        //     File::delete($file);
        // } else {
        //     dd('file tidak eksis', $file);
        // }
        $berkas = DB::connection('mysqlkhanza')->table('berkas_digital_perawatan')
            ->select(
                'berkas_digital_perawatan.lokasi_file',
                'berkas_digital_perawatan.no_rawat'
            )
            ->where('berkas_digital_perawatan.lokasi_file', '=', $id)
            ->first();
        if (Storage::disk('sftp')->exists($berkas->lokasi_file)) {
            // dd('eksis');
            Storage::disk('sftp')->delete($berkas->lokasi_file);
            $berkas = DB::connection('mysqlkhanza')->table('berkas_digital_perawatan')
                ->select(
                    'berkas_digital_perawatan.lokasi_file',
                    'berkas_digital_perawatan.no_rawat'
                )
                ->where('berkas_digital_perawatan.lokasi_file', '=', $id)
                ->delete();

            Session::flash('sukses', 'Data Berhasil dihapus!');
        } else {
            // dd('tidak eksis');
            Session::flash('error', 'Data tidak ditemukan');
        }

        // dd($berkas);

        return redirect()->back();
    }

    public function getSep($id, $pelayanan)
    {
        $data = DB::connection('mysqlkhanza')->table('bridging_sep')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'bridging_sep.nomr')
            ->leftJoin('bpjs_prb', 'bpjs_prb.no_sep', '=', 'bridging_sep.no_sep')
            ->select(
                'bridging_sep.no_sep',
                'bridging_sep.no_rawat',
                'bridging_sep.tglsep',
                'bridging_sep.tglrujukan',
                'bridging_sep.no_rujukan',
                'bridging_sep.kdppkrujukan',
                'bridging_sep.nmppkrujukan',
                'bridging_sep.kdppkpelayanan',
                'bridging_sep.jnspelayanan',
                'bridging_sep.catatan',
                'bridging_sep.diagawal',
                'bridging_sep.nmdiagnosaawal',
                'bridging_sep.kdpolitujuan',
                'bridging_sep.nmpolitujuan',
                'bridging_sep.klsrawat',
                'bridging_sep.klsnaik',
                'bridging_sep.pembiayaan',
                'bridging_sep.pjnaikkelas',
                'bridging_sep.lakalantas',
                'bridging_sep.user',
                'bridging_sep.nomr',
                'bridging_sep.nama_pasien',
                'bridging_sep.tanggal_lahir',
                'bridging_sep.peserta',
                'bridging_sep.jkel',
                'bridging_sep.no_kartu',
                'bridging_sep.tglpulang',
                'bridging_sep.asal_rujukan',
                'bridging_sep.eksekutif',
                'bridging_sep.cob',
                'bridging_sep.notelep',
                'bridging_sep.katarak',
                'bridging_sep.tglkkl',
                'bridging_sep.keterangankkl',
                'bridging_sep.suplesi',
                'bridging_sep.no_sep_suplesi',
                'bridging_sep.noskdp',
                'bridging_sep.kddpjp',
                'bridging_sep.nmdpdjp',
                'bridging_sep.tujuankunjungan',
                'bridging_sep.flagprosedur',
                'bridging_sep.penunjang',
                'bridging_sep.asesmenpelayanan',
                'bridging_sep.kddpjplayanan',
                'bridging_sep.nmdpjplayanan',
                'pasien.tgl_lahir',
                'bpjs_prb.prb'
            )
            ->where('bridging_sep.no_rawat', '=', $id)
            ->where('bridging_sep.jnspelayanan', '=', $pelayanan)
            ->orderBy('bridging_sep.tglpulang', 'DESC')
            ->first();

        if (!empty($data) && $data->no_sep != '') {
            return $data;
        } else {
            $noSep = Vedika::getSep($id, $pelayanan);
            if (!empty($noSep)) {
                $data = SepController::getSep($noSep->no_sep);
                return $data;
            } else {
                return null;
            }
        }
    }

    public function getBillFarmasi($id)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            // ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->leftJoin('detail_pemberian_obat', 'detail_pemberian_obat.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('databarang', 'databarang.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.alamat',
                'detail_pemberian_obat.no_rawat',
                'detail_pemberian_obat.kode_brng',
                'databarang.nama_brng',
                'detail_pemberian_obat.biaya_obat',
                'detail_pemberian_obat.embalase',
                'detail_pemberian_obat.tuslah',
                'detail_pemberian_obat.jml',
                'detail_pemberian_obat.total'
            )
            ->where('detail_pemberian_obat.no_rawat', $id)
            ->get();

        // dd($data);

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }

    public function getPerintahRanap($id)
    {
        $data = DB::connection('mysqlkhanza')->table('permintaan_ranap')
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
            ->where('permintaan_ranap.no_rawat', $id)
            ->first();

        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    public static function getResumeRanap($id)
    {
        $data = DB::connection('mysqlkhanza')->table('resume_pasien_ranap')
            ->join('dokter', 'dokter.kd_dokter', '=', 'resume_pasien_ranap.kd_dokter')
            ->select(
                'resume_pasien_ranap.no_rawat',
                'resume_pasien_ranap.diagnosa_awal',
                'resume_pasien_ranap.alasan',
                'resume_pasien_ranap.keluhan_utama',
                'resume_pasien_ranap.pemeriksaan_fisik',
                'resume_pasien_ranap.jalannya_penyakit',
                'resume_pasien_ranap.pemeriksaan_penunjang',
                'resume_pasien_ranap.hasil_laborat',
                'resume_pasien_ranap.tindakan_dan_operasi',
                'resume_pasien_ranap.obat_di_rs',
                'resume_pasien_ranap.diagnosa_utama',
                'resume_pasien_ranap.kd_diagnosa_utama',
                'resume_pasien_ranap.diagnosa_sekunder',
                'resume_pasien_ranap.kd_diagnosa_sekunder',
                'resume_pasien_ranap.diagnosa_sekunder2',
                'resume_pasien_ranap.kd_diagnosa_sekunder2',
                'resume_pasien_ranap.diagnosa_sekunder3',
                'resume_pasien_ranap.kd_diagnosa_sekunder3',
                'resume_pasien_ranap.diagnosa_sekunder4',
                'resume_pasien_ranap.kd_diagnosa_sekunder4',
                'resume_pasien_ranap.prosedur_utama',
                'resume_pasien_ranap.kd_prosedur_utama',
                'resume_pasien_ranap.prosedur_sekunder',
                'resume_pasien_ranap.kd_prosedur_sekunder',
                'resume_pasien_ranap.prosedur_sekunder2',
                'resume_pasien_ranap.kd_prosedur_sekunder2',
                'resume_pasien_ranap.prosedur_sekunder3',
                'resume_pasien_ranap.kd_prosedur_sekunder3',
                'resume_pasien_ranap.alergi',
                'resume_pasien_ranap.diet',
                'resume_pasien_ranap.lab_belum',
                'resume_pasien_ranap.edukasi',
                'resume_pasien_ranap.cara_keluar',
                'resume_pasien_ranap.ket_keluar',
                'resume_pasien_ranap.keadaan',
                'resume_pasien_ranap.ket_keadaan',
                'resume_pasien_ranap.dilanjutkan',
                'resume_pasien_ranap.ket_dilanjutkan',
                'resume_pasien_ranap.kontrol',
                'resume_pasien_ranap.obat_pulang',
                'resume_pasien_ranap.td',
                'resume_pasien_ranap.hr',
                'resume_pasien_ranap.rr',
                'resume_pasien_ranap.suhu',
                'resume_pasien_ranap.kd_dokter',
                'dokter.nm_dokter'
            )
            ->where('resume_pasien_ranap.no_rawat', $id)
            ->first();

        $dataKamar = DB::connection('mysqlkhanza')->table('kamar_inap')
            ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->leftJoin('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->select(
                'kamar_inap.no_rawat',
                'kamar_inap.kd_kamar',
                DB::raw("CONCAT(kamar_inap.tgl_masuk,' ',kamar_inap.jam_masuk) AS waktu_masuk_ranap"),
                DB::raw("CONCAT(kamar_inap.tgl_keluar,' ',kamar_inap.jam_keluar) AS waktu_keluar_ranap"),
                'bangsal.nm_bangsal'
            )
            ->where('kamar_inap.no_rawat', $id)
            // ->where('kamar_inap.tgl_keluar', '!=', '0000-00-00')
            ->orderBy('waktu_masuk_ranap', 'ASC')
            ->get();

        $dataDiagnosa = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
            ->where('no_rawat', $id)
            ->where('status', 'Ranap')
            ->orderBy('prioritas', 'ASC')
            ->get();

        $dataProsedur = DB::connection('mysqlkhanza')->table('prosedur_pasien')
            ->where('no_rawat', $id)
            ->where('status', 'Ranap')
            ->orderBy('prioritas', 'ASC')
            ->get();

        // dd($data, $dataDiagnosa, $dataProsedur, $dataKamar);

        if ($data && $dataKamar) {
            return array($data, $dataKamar, $dataDiagnosa, $dataProsedur);
        } else {
            return null;
        }
    }

    public function getPrasedasi($no_rawat)
    {
        $data = DB::connection('mysqlkhanza')->table('penilaian_awal_anestesi_prasedasi')
            ->join('dokter', 'dokter.kd_dokter', '=', 'penilaian_awal_anestesi_prasedasi.kd_dokter')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'penilaian_awal_anestesi_prasedasi.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('kelurahan', 'kelurahan.kd_kel', '=', 'pasien.kd_kel')
            ->leftJoin('kecamatan', 'kecamatan.kd_kec', '=', 'pasien.kd_kec')
            ->leftJoin('kabupaten', 'kabupaten.kd_kab', '=', 'pasien.kd_kab')
            ->leftJoin('kamar_inap', 'kamar_inap.no_rawat', '=', 'penilaian_awal_anestesi_prasedasi.no_rawat')
            ->leftJoin('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->leftJoin('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->select(
                'penilaian_awal_anestesi_prasedasi.*',
                'dokter.nm_dokter',
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.alamat',
                'kelurahan.nm_kel as kelurahan',
                'kecamatan.nm_kec as kecamatan',
                'kabupaten.nm_kab as kabupaten',
                'pasien.jk',
                'pasien.tgl_lahir',
                'bangsal.nm_bangsal'
            )
            ->where('reg_periksa.no_rawat', $no_rawat)
            ->first();

        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    public function getPraInduksi($no_rawat)
    {
        $data = DB::connection('mysqlkhanza')->table('penilaian_awal_anestesi_prainduksi')
            ->join('dokter', 'dokter.kd_dokter', '=', 'penilaian_awal_anestesi_prainduksi.kd_dokter')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'penilaian_awal_anestesi_prainduksi.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('kelurahan', 'kelurahan.kd_kel', '=', 'pasien.kd_kel')
            ->leftJoin('kecamatan', 'kecamatan.kd_kec', '=', 'pasien.kd_kec')
            ->leftJoin('kabupaten', 'kabupaten.kd_kab', '=', 'pasien.kd_kab')
            ->leftJoin('kamar_inap', 'kamar_inap.no_rawat', '=', 'penilaian_awal_anestesi_prainduksi.no_rawat')
            ->leftJoin('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->leftJoin('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->select(
                'penilaian_awal_anestesi_prainduksi.*',
                'dokter.nm_dokter',
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.alamat',
                'kelurahan.nm_kel as kelurahan',
                'kecamatan.nm_kec as kecamatan',
                'kabupaten.nm_kab as kabupaten',
                'pasien.jk',
                'pasien.tgl_lahir',
                'bangsal.nm_bangsal'
            )
            ->where('reg_periksa.no_rawat', $no_rawat)
            ->first();

        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    function mergePdfs($pdfFiles, $outputFilePath)
    {
        $pdf = new \setasign\Fpdi\Fpdi();

        $result = [
            'success' => false,
            'message' => ''
        ];

        foreach ($pdfFiles as $filePath) {
            if (!file_exists($filePath)) {
                Log::warning("File not found: $filePath");
                continue;
            }

            $error = false;

            try {
                $pageCount = $pdf->setSourceFile($filePath);

                // Tambahkan setiap halaman dari file PDF
                for ($i = 1; $i <= $pageCount; $i++) {
                    $pdf->AddPage();
                    $tplIdx = $pdf->importPage($i);

                    // Periksa dimensi template
                    $size = $pdf->getTemplateSize($tplIdx);
                    if ($size['width'] == 0 || $size['height'] == 0) {
                        Log::error("Invalid template size in file: $filePath, page: $i");
                        continue;
                    }

                    // Gunakan template dengan ukuran yang valid
                    $pdf->useTemplate($tplIdx, 10, 10, 200);
                }
            } catch (\Exception $e) {
                // Log::error("Error processing file: $filePath - " . $e->getMessage());
                Session::flash('error', "Error processing file: $filePath - " . $e->getMessage());
                $error = true;
            }
        }



        // if ($error) {
        //     return array($error, $e->getMessage());
        // }

        if ($error) {
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        } else {
            // Simpan PDF yang telah digabungkan
            $pdf->Output($outputFilePath, 'F');

            $result['success'] = true;
            $result['message'] = 'PDFs merged successfully!';
        }

        return $result;
    }

    //Master vedika ndak dipake karena langsung ambil dikhanza aja sinkron dengan data simrs berkas digital
    public function index()
    {
        session()->put('ibu', 'Master Data');
        session()->put('anak', 'Berkas Vedika');
        session()->forget('cucu');

        $data = MasterBerkasVedika::all();

        return view('masters.vedika_berkas', compact('data'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nama' => 'required|unique:master_berkas_vedikas,nama',
            // 'keterangan' => 'required',
        ], [
            'nama.unique' => 'Nama Berkas sudah terdaftar!',
        ]);

        $data = new MasterBerkasVedika();
        $data->nama = $request->nama;
        $data->keterangan = $request->keterangan;
        $data->save();

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        return redirect('/master/vedika');
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);

        $data = MasterBerkasVedika::find($id);

        return view('masters.vedika_berkas_edit', compact('data'));
    }

    public function update($id, Request $request)
    {
        $data = MasterBerkasVedika::find($id);
        $data->nama = $request->nama;
        $data->keterangan = $request->keterangan;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diupdate!');

        return redirect('/master/vedika');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = MasterBerkasVedika::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }
}
