<?php

namespace App\Http\Controllers;

use App\LogErrorSatuSehat;
use App\MasterAnswerLoinc;
use App\MasterLoinc;
use App\PasienSehat;
use App\PraktisiSehat;
use App\ResponseLabSatuSehat;
use App\ResponseMedicationSatuSehat;
use App\ResponseObservationLab;
use App\ResponseSatuSehat;
use App\Setting;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Guzzle\Http\Exception\ClientErrorResponseException;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\Empty_;

class SatuSehatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $uuid = Str::uuid();

        dd($uuid);
    }

    public function summary(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Rajal Satu Sehat');
        session()->put('cucu', 'Summary');

        if (empty($request->get('tanggal'))) {
            $tanggal = Carbon::now();
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
        }


        $dataLog = ResponseSatuSehat::whereDate('tgl_registrasi', $tanggal)
            ->get();

        $errorLog = LogErrorSatuSehat::whereDate('created_at', $tanggal)->get();

        // dd($errorLog);

        return view('satu_sehat.summary', compact('dataLog', 'errorLog'));
    }

    public function checkRajal(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Rajal Satu Sehat');
        session()->put('cucu', 'Summary Check');


        if (empty($request->get('tanggal_awal'))) {
            $tanggal_awal = Carbon::now();
            $tanggal_akhir = Carbon::now();
        } else {
            $tanggal_awal = new Carbon($request->get('tanggal_awal'));
            $tanggal_akhir = new Carbon($request->get('tanggal_akhir'));
        }

        $dataLog = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'penjab.png_jawab',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli',
                'poliklinik.kd_poli'
            )
            ->where('reg_periksa.status_lanjut', 'Ralan')
            // ->where('poliklinik.nm_poli', 'not like', '%IGD%')
            ->whereNotIn('poliklinik.nm_poli', ['IGD', 'Farmasi', 'Farmasi 2', 'Radiologi', 'LABORATORIUM', 'POLI HOME CARE'])
            ->whereBetween('reg_periksa.tgl_registrasi', [$tanggal_awal, $tanggal_akhir])
            ->orderBy('reg_periksa.no_rkm_medis', 'ASC')
            ->get();

        return view('satu_sehat.summary_rajal', compact('dataLog'));
    }

    public function checkRajalDetail($id)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Rajal Satu Sehat');
        session()->put('cucu', 'Summary Check');

        $id = Crypt::decrypt($id);
        // $id = '2023/06/14/000055';

        $dataPasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'penjab.nama_perusahaan',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.no_peserta',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli',
                'poliklinik.kd_poli'
            )
            ->where('reg_periksa.no_rawat', $id)
            ->orderBy('reg_periksa.no_rkm_medis', 'ASC')
            ->first();

        if (strlen($dataPasien->ktp_pasien) > 3) {
            $idSatu = PasienSehat::where('nik', $dataPasien->ktp_pasien)->first();
        } else {
            $idSatu = null;
        }

        if (strlen($dataPasien->ktp_dokter) > 3) {
            $idSatuPraktisi = PraktisiSehat::where('nik', $dataPasien->ktp_dokter)->first();
        } else {
            $idSatuPraktisi = null;
        }

        $cekDiagnosa = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
            ->leftJoin('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            ->select(
                'diagnosa_pasien.no_rawat',
                'diagnosa_pasien.kd_penyakit',
                'diagnosa_pasien.status',
                'diagnosa_pasien.prioritas',
                'penyakit.nm_penyakit'
            )
            ->where('diagnosa_pasien.status', 'Ralan')
            ->where('diagnosa_pasien.no_rawat', $id)
            ->get();

        $cekPoliklinik = DB::connection('mysqlkhanza')->table('fhir_poliklinik')
            ->where('kd_poli', $dataPasien->kd_poli)
            ->first();

        $logUser = LogErrorSatuSehat::where('subject', 'Pasien')
            ->where(function ($query)  use ($id, $dataPasien) {
                $query->where('keterangan', 'like', "%$id%")
                    ->orWhere('keterangan', 'like', "%$dataPasien->no_rkm_medis%")
                    ->orWhere('keterangan', 'like', "%$dataPasien->ktp_pasien%");
            })
            ->orderBy('created_at', 'DESC')
            ->get();

        $logPraktisi = LogErrorSatuSehat::where('subject', 'Praktitioner')
            ->where(function ($query)  use ($id, $dataPasien) {
                $query->where('keterangan', 'like', "%$id%")
                    ->orWhere('keterangan', 'like', "%$dataPasien->nama_dokter%")
                    ->orWhere('keterangan', 'like', "%$dataPasien->ktp_dokter%");
            })
            ->orderBy('created_at', 'DESC')
            ->get();

        $logDiagnosa = LogErrorSatuSehat::where('subject', 'like', '%Diagnosa%')
            ->where(function ($query)  use ($id, $dataPasien) {
                $query->where('keterangan', 'like', "%$id%")
                    ->orWhere('keterangan', 'like', "%$dataPasien->no_rkm_medis%")
                    ->orWhere('keterangan', 'like', "%$dataPasien->ktp_pasien%");
            })
            ->orderBy('created_at', 'DESC')
            ->get();

        $logPoliklinik = LogErrorSatuSehat::where('subject', 'Lokasi')
            ->where('keterangan', 'like', "%$dataPasien->kd_poli%")
            ->orderBy('created_at', 'DESC')
            ->get();

        $logOther = LogErrorSatuSehat::where('keterangan', 'like', "%$dataPasien->no_rkm_medis%")
            ->orWhere('keterangan', 'like', "%$dataPasien->no_rawat%")
            ->orderBy('created_at', 'DESC')
            ->get();

        // dd($cekDiagnosa);


        return view('satu_sehat.check_error', compact(
            'dataPasien',
            'idSatu',
            'idSatuPraktisi',
            'logPraktisi',
            'logDiagnosa',
            'cekDiagnosa',
            'cekPoliklinik',
            'logPoliklinik',
            'logOther',
            'logUser'
        ));
    }

    public function bundleData(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Rajal Satu Sehat');
        session()->put('cucu', 'API Bundle');
        set_time_limit(0);

        // SatuSehatController::getTokenSehat();

        // dd(Session('tokenSatuSehat'));
        if (empty($request->tanggal)) {
            $pasien_tanggal = Carbon::now()->format('Y-m-d');
            $kemarin = Carbon::yesterday();
            // $pasien_tanggal = '2022-11-25';
            $data = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
                ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
                ->select(
                    'reg_periksa.no_rkm_medis',
                    'reg_periksa.no_rawat',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.jam_reg',
                    'reg_periksa.kd_dokter',
                    'reg_periksa.status_lanjut',
                    'reg_periksa.stts',
                    'reg_periksa.kd_poli',
                    'reg_periksa.kd_pj',
                    'pasien.nm_pasien',
                    'pasien.no_ktp as ktp_pasien',
                    'pasien.tgl_lahir',
                    'pasien.jk',
                    'pegawai.no_ktp as ktp_dokter',
                    'pegawai.nama as nama_dokter',
                    'poliklinik.nm_poli',
                    'poliklinik.kd_poli'

                )
                ->selectRaw("(CASE WHEN (poliklinik.kd_poli = 'u0041') THEN 'IGD' ELSE poliklinik.nm_poli END) as alias_nm_poli")
                ->where('reg_periksa.status_lanjut', 'Ralan')
                // ->where('reg_periksa.stts', 'Sudah')
                // // ->where('poliklinik.kd_poli', '!=', 'u0041')
                // // ->orWhere('poliklinik.kd_poli', '!=', 'IGDK')
                // ->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
                // ->orWhere('reg_periksa.tgl_registrasi', $kemarin)
                ->whereIn('reg_periksa.stts', ['Sudah', 'Dirujuk', 'Berkas Lengkap']) // Menggunakan whereIn untuk beberapa kondisi
                ->where(function ($query) use ($pasien_tanggal, $kemarin) {
                    $query->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
                        ->orWhere('reg_periksa.tgl_registrasi', $kemarin);
                })
                ->orderBy('reg_periksa.tgl_registrasi', 'ASC')
                ->get();
            // dd($data, $kemarin);
        } else {
            // dd($request);
            // $pasien_tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
            $pasien_tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
            $data = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
                ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
                ->select(
                    'reg_periksa.no_rkm_medis',
                    'reg_periksa.no_rawat',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.jam_reg',
                    'reg_periksa.kd_dokter',
                    'reg_periksa.status_lanjut',
                    'reg_periksa.stts',
                    'reg_periksa.kd_poli',
                    'reg_periksa.kd_pj',
                    'pasien.nm_pasien',
                    'pasien.no_ktp as ktp_pasien',
                    'pasien.tgl_lahir',
                    'pasien.jk',
                    'pegawai.no_ktp as ktp_dokter',
                    'pegawai.nama as nama_dokter',
                    'poliklinik.nm_poli',
                    'poliklinik.kd_poli'

                )
                ->selectRaw("(CASE WHEN (poliklinik.kd_poli = 'u0041') THEN 'IGD' ELSE poliklinik.nm_poli END) as alias_nm_poli")
                ->where('reg_periksa.status_lanjut', 'Ralan')
                // ->where('reg_periksa.stts', 'Sudah')
                ->whereIn('reg_periksa.stts', ['Sudah', 'Dirujuk', 'Berkas Lengkap'])
                // ->where('reg_periksa.no_rawat', '=', '2023/03/09/000107')
                ->whereDate('reg_periksa.tgl_registrasi', $pasien_tanggal)
                ->orderBy('reg_periksa.tgl_registrasi', 'ASC')
                ->get();

            // dd($data);
        }

        $loop = 0;

        foreach ($data as $key => $dataPengunjung) {
            $cekLog = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->count();
            // dd($cekLog);
            // if ($dataPengunjung->no_rawat == '2023/03/09/000107') {
            if (($cekLog == 0) && ($dataPengunjung->nm_poli != 'IGD')) {
                $idRS = env('IDRS');
                //Karena masih masalah diminta kirim pakai dummy dulu
                $idPasien = SatuSehatController::patientSehat($dataPengunjung->ktp_pasien);
                if ($idPasien == null) {
                    $cek = LogErrorSatuSehat::where('subject', 'Pasien')
                        ->where('keterangan', 'like', '%' . $dataPengunjung->no_rkm_medis . '%')
                        ->whereDate('created_at', Carbon::now())
                        ->get();

                    if ($cek->count() == 0) {
                        $error = new LogErrorSatuSehat();
                        $error->subject = 'Pasien';
                        $error->keterangan =  ' cek NIK Pasien no RM ' . $dataPengunjung->no_rkm_medis;
                        $error->save();
                    }
                }
                $idDokter = SatuSehatController::practitioner($dataPengunjung->ktp_dokter);
                if ($idDokter == null) {
                    $cek = LogErrorSatuSehat::where('subject', 'Praktitioner')
                        ->where('keterangan', 'like', '%' . $dataPengunjung->nama_dokter . '%')
                        ->whereDate('created_at', Carbon::now())
                        ->get();

                    if ($cek->count() == 0) {
                        $error = new LogErrorSatuSehat();
                        $error->subject = 'Praktitioner';
                        $error->keterangan = $dataPengunjung->nama_dokter . ' tidak ditemukan';
                        $error->save();
                    }
                }
                // $idPasien = "P02478375538";
                // $idDokter = "10009880728";
                $idLokasi = SatuSehatController::getIdPoli($dataPengunjung->kd_poli);
                $diagnosaPrimer = SatuSehatController::getDiagnosisPrimerRalan($dataPengunjung->no_rawat);

                // dd($dataPengunjung->no_rawat, $idPasien, $idDokter, $idLokasi, $diagnosaPrimer);

                if ((!empty($idPasien)) && (!empty($idDokter)) && (!empty($diagnosaPrimer)) && (!empty($idLokasi))) {

                    $diagnosaSekunder = SatuSehatController::getDiagnosisSekunderRalan($dataPengunjung->no_rawat);
                    $procedurePasien = SatuSehatController::getProcedureRalan($dataPengunjung->no_rawat);
                    $cekDiet = SatuSehatController::getDiet($dataPengunjung->no_rawat, $dataPengunjung->tgl_registrasi); //nyoba bundle composition
                    $waktuKeperawatan = SatuSehatController::getWaktuKeperawatan($dataPengunjung->no_rawat);

                    //Definisi Vital
                    $vital = SatuSehatController::getVital($dataPengunjung->no_rawat);
                    if (!empty($vital)) {
                        if ($vital->nadi != '-') {
                            $heartRate = floatval($vital->nadi);
                        } else {
                            $heartRate = floatval(80);
                        }

                        if ($vital->respirasi != '-') {
                            $respiratory = floatval($vital->respirasi);
                        } else {
                            $respiratory = floatval(20);
                        }
                        if ($vital->tensi != '-') {
                            $darah = explode('/', $vital->tensi);
                            $sistole = floatval($darah[0]);
                            if (!empty($darah[1])) {
                                $diastole = floatval($darah[1]);
                            } else {
                                $diastole = floatval(80);
                            }
                        } else {
                            $sistole = floatval(120);
                            $diastole = floatval(80);
                        }

                        // $waktu_mulai = new Carbon($waktuAwal);
                        if ($vital->suhu_tubuh != '-') {
                            $temperature = floatval($vital->suhu_tubuh);
                        } else {
                            $temperature = floatval(37);
                        }
                    } else {
                        $heartRate = floatval(80);
                        $sistole = floatval(120);
                        $diastole = floatval(80);
                        $respiratory = floatval(20);
                        $temperature = floatval(37);
                    }

                    //Waktu
                    $waktuAwal = $dataPengunjung->tgl_registrasi . ' ' . $dataPengunjung->jam_reg;
                    $waktu_mulai = new Carbon($waktuAwal);
                    // $formatWaktuMulai = Carbon::parse($waktuAwal)->format('Y-m-d') . 'T' . Carbon::parse($waktuAwal)->format('H:i:s+07:00');
                    $formatWaktuMulai = $waktu_mulai->setTimezone('UTC')->toW3cString();
                    if ((!empty($waktuKeperawatan->tanggal))) {
                        $waktuInprogress = Carbon::parse($waktuKeperawatan->tanggal);
                        if ($waktu_mulai > $waktuInprogress) {
                            goto WaktuProses2;
                        }
                    } else {
                        WaktuProses2:
                        $waktuInprogress = Carbon::parse($waktuAwal)->addMinute(10);
                        // dd($dataPengunjung->no_rawat, $waktu_mulai, $waktuInprogress);
                    }
                    // $formatWaktuProgress = Carbon::parse($waktuInprogress)->format('Y-m-d') . 'T' . Carbon::parse($waktuInprogress)->format('H:i:s+07:00');
                    $formatWaktuProgress = $waktuInprogress->setTimezone('UTC')->toW3cString();
                    if ((!empty($vital->tgl_perawatan))) {
                        $waktuSelesai = Carbon::parse($vital->tgl_perawatan . ' ' . $vital->jam_rawat);
                        if ($waktuInprogress > $waktuSelesai) {
                            // goto WaktuSelesai2;
                            $selisih = $waktuInprogress->diffInMinutes($waktu_mulai, true);
                            $waktuSelesai = Carbon::parse($waktuAwal)->addMinute($selisih + 5);
                        }
                    } else {
                        WaktuSelesai2:
                        $selisih = $waktuInprogress->diffInMinutes($waktu_mulai, true);
                        $waktuSelesai = Carbon::parse($waktuAwal)->addMinute($selisih + 5);
                        // $waktuSelesai = Carbon::parse($waktuAwal)->addMinute(30);
                        // dd($dataPengunjung->no_rawat, $waktu_mulai, $waktuInprogress, $waktuSelesai);
                    }
                    // $formatWaktuSelesai = Carbon::parse($waktuSelesai)->format('Y-m-d') . 'T' . Carbon::parse($waktuSelesai)->format('H:i:s+07:00');
                    $formatWaktuSelesai = $waktuSelesai->setTimezone('UTC')->toW3cString();

                    $day = Carbon::parse($waktuAwal)->dayName;
                    $day2 = Carbon::parse($waktuAwal)->format('d F Y');
                    $formatDay = $day . ', ' . $day2;
                    // dd($formatWaktuMulai, $formatWaktuProgress, $formatWaktuSelesai);

                    //UUID
                    $uuidEncounter = Str::uuid();
                    $uuidDiagnosaPrimer = Str::uuid();
                    // $uuidCondition1 = Str::uuid();
                    // $uuidCondition2 = Str::uuid();

                    $uuidHeart = Str::uuid();
                    $uuidRespiratory = Str::uuid();
                    $uuidSistol = Str::uuid();
                    $uuidDiastol = Str::uuid();
                    $uuidTemperature = Str::uuid();
                    if ($diagnosaSekunder != null) {
                        $uuidDiagnosaSekunder = Str::uuid();
                        //encounter 2 diagnosa
                        $Encounter1 = [
                            "fullUrl" => "urn:uuid:$uuidEncounter",
                            "resource" => [
                                "resourceType" => "Encounter",
                                "status" => "finished", //awal finished diganti in-progress
                                "class" => [
                                    "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                                    "code" => "AMB",
                                    "display" => "ambulatory"
                                ],
                                "subject" => [
                                    "reference" => "Patient/$idPasien",
                                    "display" => "$dataPengunjung->nm_pasien"
                                ],
                                "participant" => [
                                    [
                                        "type" => [
                                            [
                                                "coding" => [
                                                    [
                                                        "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                                        "code" => "ATND",
                                                        "display" => "attender"
                                                    ]
                                                ]
                                            ]
                                        ],
                                        "individual" => [
                                            "reference" => "Practitioner/$idDokter",
                                            "display" => "$dataPengunjung->nama_dokter"
                                        ]
                                    ]
                                ],
                                "period" => [
                                    "start" => "$formatWaktuMulai",
                                    "end" => "$formatWaktuSelesai"
                                ],
                                "location" => [
                                    [
                                        "location" => [
                                            "reference" => "Location/$idLokasi",
                                            "display" => "$dataPengunjung->alias_nm_poli"
                                        ]
                                    ]
                                ],
                                "diagnosis" => [
                                    [
                                        "condition" => [
                                            "reference" => "urn:uuid:$uuidDiagnosaPrimer",
                                            "display" => "$diagnosaPrimer->nm_penyakit"
                                        ],
                                        "use" => [
                                            "coding" => [
                                                [
                                                    "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
                                                    "code" => "DD",
                                                    "display" => "Discharge diagnosis"
                                                ]
                                            ]
                                        ],
                                        "rank" => 1
                                    ],
                                    [
                                        "condition" => [
                                            "reference" => "urn:uuid:$uuidDiagnosaSekunder",
                                            "display" => "$diagnosaSekunder->nm_penyakit"
                                        ],
                                        "use" => [
                                            "coding" => [
                                                [
                                                    "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
                                                    "code" => "DD",
                                                    "display" => "Discharge diagnosis"
                                                ]
                                            ]
                                        ],
                                        "rank" => 2
                                    ]

                                ],
                                "statusHistory" => [
                                    [
                                        "status" => "arrived",
                                        "period" => [
                                            "start" => "$formatWaktuMulai",
                                            "end" => "$formatWaktuProgress"
                                        ]
                                    ],
                                    [
                                        "status" => "in-progress",
                                        "period" => [
                                            "start" => "$formatWaktuProgress",
                                            "end" => "$formatWaktuSelesai"
                                        ]
                                    ],
                                    [
                                        "status" => "finished",
                                        "period" => [
                                            "start" => "$formatWaktuSelesai",
                                            "end" => "$formatWaktuSelesai"
                                        ]
                                    ]
                                ],
                                "serviceProvider" => [
                                    "reference" => "Organization/$idRS"
                                ],
                                "identifier" => [
                                    [
                                        "system" => "http://sys-ids.kemkes.go.id/encounter/$idRS",
                                        "value" => "$dataPengunjung->no_rawat"
                                    ]
                                ]
                            ],
                            "request" => [
                                "method" => "POST",
                                "url" => "Encounter"
                            ]
                        ];
                        //diagnosa 2
                        $diagnosis2 = [
                            "fullUrl" => "urn:uuid:$uuidDiagnosaSekunder",
                            "resource" => [
                                "resourceType" => "Condition",
                                "clinicalStatus" => [
                                    "coding" => [
                                        [
                                            "system" => "http://terminology.hl7.org/CodeSystem/condition-clinical",
                                            "code" => "active",
                                            "display" => "Active"
                                        ]
                                    ]
                                ],
                                "category" => [
                                    [
                                        "coding" => [
                                            [
                                                "system" => "http://terminology.hl7.org/CodeSystem/condition-category",
                                                "code" => "encounter-diagnosis",
                                                "display" => "Encounter Diagnosis"
                                            ]
                                        ]
                                    ]
                                ],
                                "code" => [
                                    "coding" => [
                                        [
                                            "system" => "http://hl7.org/fhir/sid/icd-10",
                                            "code" => "$diagnosaSekunder->kd_penyakit",
                                            "display" => "$diagnosaSekunder->nm_penyakit"
                                        ]
                                    ]
                                ],
                                "subject" => [
                                    "reference" => "Patient/$idPasien",
                                    "display" => "$dataPengunjung->nm_pasien"
                                ],
                                "encounter" => [
                                    "reference" => "urn:uuid:$uuidEncounter",
                                    "display" => "Kunjungan $dataPengunjung->nm_pasien di hari $formatDay"
                                ]
                            ],
                            "request" => [
                                "method" => "POST",
                                "url" => "Condition"
                            ]
                        ];
                    } else {
                        //Ecounter 1 diagnosa
                        $Encounter2 = [
                            "fullUrl" => "urn:uuid:$uuidEncounter",
                            "resource" => [
                                "resourceType" => "Encounter",
                                "status" => "finished", //coba diganti in-progress dari finished
                                "class" => [
                                    "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                                    "code" => "AMB",
                                    "display" => "ambulatory"
                                ],
                                "subject" => [
                                    "reference" => "Patient/$idPasien",
                                    "display" => "$dataPengunjung->nm_pasien"
                                ],
                                "participant" => [
                                    [
                                        "type" => [
                                            [
                                                "coding" => [
                                                    [
                                                        "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                                        "code" => "ATND",
                                                        "display" => "attender"
                                                    ]
                                                ]
                                            ]
                                        ],
                                        "individual" => [
                                            "reference" => "Practitioner/$idDokter",
                                            "display" => "$dataPengunjung->nama_dokter"
                                        ]
                                    ]
                                ],
                                "period" => [
                                    "start" => "$formatWaktuMulai",
                                    "end" => "$formatWaktuSelesai"
                                ],
                                "location" => [
                                    [
                                        "location" => [
                                            "reference" => "Location/$idLokasi",
                                            "display" => "$dataPengunjung->alias_nm_poli"
                                        ]
                                    ]
                                ],
                                "diagnosis" => [
                                    [
                                        "condition" => [
                                            "reference" => "urn:uuid:$uuidDiagnosaPrimer",
                                            "display" => "$diagnosaPrimer->nm_penyakit"
                                        ],
                                        "use" => [
                                            "coding" => [
                                                [
                                                    "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
                                                    "code" => "DD",
                                                    "display" => "Discharge diagnosis"
                                                ]
                                            ]
                                        ],
                                        "rank" => 1
                                    ]
                                ],
                                "statusHistory" => [
                                    [
                                        "status" => "arrived",
                                        "period" => [
                                            "start" => "$formatWaktuMulai",
                                            "end" => "$formatWaktuProgress"
                                        ]
                                    ],
                                    [
                                        "status" => "in-progress",
                                        "period" => [
                                            "start" => "$formatWaktuProgress",
                                            "end" => "$formatWaktuSelesai"
                                        ]
                                    ],
                                    [
                                        "status" => "finished",
                                        "period" => [
                                            "start" => "$formatWaktuSelesai",
                                            "end" => "$formatWaktuSelesai"
                                        ]
                                    ]
                                ],
                                "serviceProvider" => [
                                    "reference" => "Organization/$idRS"
                                ],
                                "identifier" => [
                                    [
                                        "system" => "http://sys-ids.kemkes.go.id/encounter/$idRS",
                                        "value" => "$dataPengunjung->no_rawat"
                                    ]
                                ]
                            ],
                            "request" => [
                                "method" => "POST",
                                "url" => "Encounter"
                            ]
                        ];
                    }

                    // dd($heartRate, $respiratory, $sistole, $diastole, $temperature, $formatWaktuMulai, $formatWaktuProgress, $formatWaktuSelesai);
                    // dd($idDokter, $idPasien, $idLokasi, $diagnosaPrimer, $diagnosaSekunder, $waktu_mulai);

                    //diagnosa 1
                    $diagnosis1 = [
                        "fullUrl" => "urn:uuid:$uuidDiagnosaPrimer",
                        "resource" => [
                            "resourceType" => "Condition",
                            "clinicalStatus" => [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/condition-clinical",
                                        "code" => "active",
                                        "display" => "Active"
                                    ]
                                ]
                            ],
                            "category" => [
                                [
                                    "coding" => [
                                        [
                                            "system" => "http://terminology.hl7.org/CodeSystem/condition-category",
                                            "code" => "encounter-diagnosis",
                                            "display" => "Encounter Diagnosis"
                                        ]
                                    ]
                                ]
                            ],
                            "code" => [
                                "coding" => [
                                    [
                                        "system" => "http://hl7.org/fhir/sid/icd-10",
                                        "code" => "$diagnosaPrimer->kd_penyakit",
                                        "display" => "$diagnosaPrimer->nm_penyakit"
                                    ]
                                ]
                            ],
                            "subject" => [
                                "reference" => "Patient/$idPasien",
                                "display" => "$dataPengunjung->nm_pasien"
                            ],
                            "encounter" => [
                                "reference" => "urn:uuid:$uuidEncounter",
                                "display" => "Kunjungan $dataPengunjung->nm_pasien di hari $formatDay"
                            ]
                        ],
                        "request" => [
                            "method" => "POST",
                            "url" => "Condition"
                        ]
                    ];

                    $vital1 = [
                        "fullUrl" => "urn:uuid:$uuidHeart",
                        "resource" => [
                            "resourceType" => "Observation",
                            "status" => "final",
                            "category" => [
                                [
                                    "coding" => [
                                        [
                                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                            "code" => "vital-signs",
                                            "display" => "Vital Signs"
                                        ]
                                    ]
                                ]
                            ],
                            "code" => [
                                "coding" => [
                                    [
                                        "system" => "http://loinc.org",
                                        "code" => "8867-4",
                                        "display" => "Heart rate"
                                    ]
                                ]
                            ],
                            "subject" => [
                                "reference" => "Patient/$idPasien"
                            ],
                            "performer" => [
                                [
                                    "reference" => "Practitioner/10004181193"
                                ]
                            ],
                            "encounter" => [
                                "reference" => "urn:uuid:$uuidEncounter",
                                "display" => "Pemeriksaan Fisik Nadi $dataPengunjung->nm_pasien di hari $formatDay"
                            ],
                            "effectiveDateTime" => "$formatWaktuProgress",
                            "valueQuantity" => [
                                "value" => $heartRate,
                                "unit" => "beats/minute",
                                "system" => "http://unitsofmeasure.org",
                                "code" => "/min"
                            ]
                        ],
                        "request" => [
                            "method" => "POST",
                            "url" => "Observation"
                        ]
                    ];
                    $vital2 = [
                        "fullUrl" => "urn:uuid:$uuidRespiratory",
                        "resource" => [
                            "resourceType" => "Observation",
                            "status" => "final",
                            "category" => [
                                [
                                    "coding" => [
                                        [
                                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                            "code" => "vital-signs",
                                            "display" => "Vital Signs"
                                        ]
                                    ]
                                ]
                            ],
                            "code" => [
                                "coding" => [
                                    [
                                        "system" => "http://loinc.org",
                                        "code" => "9279-1",
                                        "display" => "Respiratory rate"
                                    ]
                                ]
                            ],
                            "subject" => [
                                "reference" => "Patient/$idPasien"
                            ],
                            "performer" => [
                                [
                                    "reference" => "Practitioner/10004181193"
                                ]
                            ],
                            "encounter" => [
                                "reference" => "urn:uuid:$uuidEncounter",
                                "display" => "Pemeriksaan Fisik Pernafasan $dataPengunjung->nm_pasien di hari $formatDay"
                            ],
                            "effectiveDateTime" => "$formatWaktuProgress",
                            "valueQuantity" => [
                                "value" => $respiratory,
                                "unit" => "breaths/minute",
                                "system" => "http://unitsofmeasure.org",
                                "code" => "/min"
                            ]
                        ],
                        "request" => [
                            "method" => "POST",
                            "url" => "Observation"
                        ]
                    ];
                    $vital3 = [
                        "fullUrl" => "urn:uuid:$uuidSistol",
                        "resource" => [
                            "resourceType" => "Observation",
                            "status" => "final",
                            "category" => [
                                [
                                    "coding" => [
                                        [
                                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                            "code" => "vital-signs",
                                            "display" => "Vital Signs"
                                        ]
                                    ]
                                ]
                            ],
                            "code" => [
                                "coding" => [
                                    [
                                        "system" => "http://loinc.org",
                                        "code" => "8480-6",
                                        "display" => "Systolic blood pressure"
                                    ]
                                ]
                            ],
                            "subject" => [
                                "reference" => "Patient/$idPasien"
                            ],
                            "performer" => [
                                [
                                    "reference" => "Practitioner/10004181193"
                                ]
                            ],
                            "encounter" => [
                                "reference" => "urn:uuid:$uuidEncounter",
                                "display" => "Pemeriksaan Fisik Sistolik $dataPengunjung->nm_pasien di hari $formatDay"
                            ],
                            "effectiveDateTime" => "$formatWaktuProgress",
                            "bodySite" => [
                                "coding" => [
                                    [
                                        "system" => "http://snomed.info/sct",
                                        "code" => "368209003",
                                        "display" => "Right arm"
                                    ]
                                ]
                            ],
                            "valueQuantity" => [
                                "value" => $sistole,
                                "unit" => "mm[Hg]",
                                "system" => "http://unitsofmeasure.org",
                                "code" => "mm[Hg]"
                            ]
                            // ,
                            // "interpretation" => [
                            //     [
                            //         "coding" => [
                            //             [
                            //                 "system" => "http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation",
                            //                 "code" => "HU",
                            //                 "display" => "significantly high"
                            //             ]
                            //         ],
                            //         "text" => "Di atas nilai referensi"
                            //     ]
                            // ]
                        ],
                        "request" => [
                            "method" => "POST",
                            "url" => "Observation"
                        ]
                    ];
                    $vital4 = [
                        "fullUrl" => "urn:uuid:$uuidDiastol",
                        "resource" => [
                            "resourceType" => "Observation",
                            "status" => "final",
                            "category" => [
                                [
                                    "coding" => [
                                        [
                                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                            "code" => "vital-signs",
                                            "display" => "Vital Signs"
                                        ]
                                    ]
                                ]
                            ],
                            "code" => [
                                "coding" => [
                                    [
                                        "system" => "http://loinc.org",
                                        "code" => "8462-4",
                                        "display" => "Diastolic blood pressure"
                                    ]
                                ]
                            ],
                            "subject" => [
                                "reference" => "Patient/$idPasien",
                                "display" => "$dataPengunjung->nm_pasien"
                            ],
                            "performer" => [
                                [
                                    "reference" => "Practitioner/10004181193"
                                ]
                            ],
                            "encounter" => [
                                "reference" => "urn:uuid:$uuidEncounter",
                                "display" => "Pemeriksaan Fisik Diastolik $dataPengunjung->nm_pasien di hari $formatDay"
                            ],
                            "effectiveDateTime" => "$formatWaktuProgress",
                            "bodySite" => [
                                "coding" => [
                                    [
                                        "system" => "http://snomed.info/sct",
                                        "code" => "368209003",
                                        "display" => "Right arm"
                                    ]
                                ]
                            ],
                            "valueQuantity" => [
                                "value" => $diastole,
                                "unit" => "mm[Hg]",
                                "system" => "http://unitsofmeasure.org",
                                "code" => "mm[Hg]"
                            ]
                            // ,
                            // "interpretation" => [
                            //     [
                            //         "coding" => [
                            //             [
                            //                 "system" => "http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation",
                            //                 "code" => "L",
                            //                 "display" => "low"
                            //             ]
                            //         ],
                            //         "text" => "Di bawah nilai referensi"
                            //     ]
                            // ]
                        ],
                        "request" => [
                            "method" => "POST",
                            "url" => "Observation"
                        ]
                    ];
                    $vital5 = [
                        "fullUrl" => "urn:uuid:$uuidTemperature",
                        "resource" => [
                            "resourceType" => "Observation",
                            "status" => "final",
                            "category" => [
                                [
                                    "coding" => [
                                        [
                                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                            "code" => "vital-signs",
                                            "display" => "Vital Signs"
                                        ]
                                    ]
                                ]
                            ],
                            "code" => [
                                "coding" => [
                                    [
                                        "system" => "http://loinc.org",
                                        "code" => "8310-5",
                                        "display" => "Body temperature"
                                    ]
                                ]
                            ],
                            "subject" => [
                                "reference" => "Patient/$idPasien"
                            ],
                            "performer" => [
                                [
                                    "reference" => "Practitioner/10004181193"
                                ]
                            ],
                            "encounter" => [
                                "reference" => "urn:uuid:$uuidEncounter",
                                "display" => "Pemeriksaan Fisik Suhu $dataPengunjung->nm_pasien di hari $formatDay"
                            ],
                            "effectiveDateTime" => "$formatWaktuProgress",
                            "valueQuantity" => [
                                "value" => $temperature,
                                "unit" => "C",
                                "system" => "http://unitsofmeasure.org",
                                "code" => "Cel"
                            ]
                            // ,
                            // "interpretation" => [
                            //     [
                            //         "coding" => [
                            //             [
                            //                 "system" => "http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation",
                            //                 "code" => "H",
                            //                 "display" => "High"
                            //             ]
                            //         ],
                            //         "text" => "Di atas nilai referensi"
                            //     ]
                            // ]
                        ],
                        "request" => [
                            "method" => "POST",
                            "url" => "Observation"
                        ]
                    ];
                    if ($procedurePasien != null) {
                        $uuidProcedure = Str::uuid();

                        $procedure = [
                            "fullUrl" => "urn:uuid:$uuidProcedure",
                            "resource" => [
                                "resourceType" => "Procedure",
                                "status" => "completed",
                                "category" => [
                                    "coding" => [
                                        [
                                            "system" => "http://snomed.info/sct",
                                            "code" => "103693007",
                                            "display" => "Diagnostic procedure"
                                        ]
                                    ],
                                    "text" => "Diagnostic procedure"
                                ],
                                "code" => [
                                    "coding" => [
                                        [
                                            "system" => "http://hl7.org/fhir/sid/icd-9-cm",
                                            "code" => "$procedurePasien->kode",
                                            "display" => "$procedurePasien->deskripsi_panjang"
                                        ]
                                    ]
                                ],
                                "subject" => [
                                    "reference" => "Patient/$idPasien",
                                    "display" => "$dataPengunjung->nm_pasien"
                                ],
                                "encounter" => [
                                    "reference" => "urn:uuid:$uuidEncounter",
                                    "display" => "Tindakan yang dilakukan kepada pasien $dataPengunjung->nm_pasien pada $formatDay"
                                ],
                                "performedPeriod" => [
                                    "start" => "$formatWaktuProgress",
                                    "end" => "$formatWaktuSelesai"
                                ],
                                "performer" => [
                                    [
                                        "actor" => [
                                            "reference" => "Practitioner/$idDokter",
                                            "display" => "$dataPengunjung->nama_dokter"
                                        ]
                                    ]
                                ],
                                "reasonCode" => [
                                    [
                                        "coding" => [
                                            [
                                                "system" => "http://hl7.org/fhir/sid/icd-10",
                                                "code" => "$diagnosaPrimer->kd_penyakit",
                                                "display" => "$diagnosaPrimer->nm_penyakit"
                                            ]
                                        ]
                                    ]
                                ]
                                // ,
                                // "bodySite" => [
                                //     [
                                //         "coding" => [
                                //             [
                                //                 "system" => "http://snomed.info/sct",
                                //                 "code" => "302551006",
                                //                 "display" => "Entire Thorax"
                                //             ]
                                //         ]
                                //     ]
                                // ],
                                // "note" => [
                                //     [
                                //         "text" => "Rontgen thorax melihat perluasan infiltrat dan kavitas."
                                //     ]
                                // ]
                            ],
                            "request" => [
                                "method" => "POST",
                                "url" => "Procedure"
                            ]
                        ];
                    }
                    if ($cekDiet != null) {
                        $uuidComposition = Str::uuid();

                        $compositionData = [
                            "fullUrl" => "urn:uuid:$uuidComposition",
                            "resource" => [
                                "resourceType" => "Composition",
                                "identifier" => [
                                    "system" => "http://sys-ids.kemkes.go.id/composition/$idRS",
                                    "value" => "$dataPengunjung->no_rawat"
                                ],
                                "status" => "final",
                                "type" => [
                                    "coding" => [
                                        [
                                            "system" => "http://loinc.org",
                                            "code" => "18842-5",
                                            "display" => "Discharge summary"
                                        ]
                                    ]
                                ],
                                "category" => [
                                    [
                                        "coding" => [
                                            [
                                                "system" => "http://loinc.org",
                                                "code" => "LP173421-1",
                                                "display" => "Report"
                                            ]
                                        ]
                                    ]
                                ],
                                "subject" => [
                                    "reference" => "Patient/$idPasien",
                                    "display" => "$dataPengunjung->nm_pasien"
                                ],
                                "encounter" => [
                                    "reference" => "urn:uuid:$uuidEncounter",
                                    "display" => "Kunjungan $dataPengunjung->nm_pasien di hari $formatDay"
                                ],
                                "date" => "$dataPengunjung->tgl_registrasi",
                                "author" => [
                                    [
                                        "reference" => "Practitioner/$idDokter",
                                        "display" => "$dataPengunjung->nama_dokter"
                                    ]
                                ],
                                "title" => "Resume Medis Rawat Jalan",
                                "custodian" => [
                                    "reference" => "Organization/$idRS"
                                ],
                                "section" => [
                                    [
                                        "code" => [
                                            "coding" => [
                                                [
                                                    "system" => "http://loinc.org",
                                                    "code" => "42344-2",
                                                    "display" => "Discharge diet (narrative)"
                                                ]
                                            ]
                                        ],
                                        "text" => [
                                            "status" => "additional",
                                            "div" => "$cekDiet->monitoring_evaluasi"
                                        ]
                                    ]
                                ],
                            ],
                            "request" => [
                                "method" => "POST",
                                "url" => "Composition"
                            ]
                        ];
                    }
                    if ((!empty($diagnosaSekunder)) && (!empty($procedurePasien)) && (!empty($cekDiet))) {
                        $dataBundle = [$Encounter1, $diagnosis1, $diagnosis2, $vital1, $vital2, $vital3, $vital4, $vital5, $procedure, $compositionData];
                    } elseif ((!empty($diagnosaSekunder)) && (!empty($procedurePasien)) && (empty($cekDiet))) {
                        $dataBundle = [$Encounter1, $diagnosis1, $diagnosis2, $vital1, $vital2, $vital3, $vital4, $vital5, $procedure];
                    } elseif ((!empty($diagnosaSekunder)) && (empty($procedurePasien)) && (!empty($cekDiet))) {
                        $dataBundle = [$Encounter1, $diagnosis1, $diagnosis2, $vital1, $vital2, $vital3, $vital4, $vital5, $compositionData];
                    } elseif ((!empty($diagnosaSekunder)) && (empty($procedurePasien)) && (empty($cekDiet))) {
                        $dataBundle = [$Encounter1, $diagnosis1, $diagnosis2, $vital1, $vital2, $vital3, $vital4, $vital5];
                    } elseif ((empty($diagnosaSekunder)) && (!empty($procedurePasien)) && (!empty($cekDiet))) {
                        $dataBundle = [$Encounter2, $diagnosis1, $vital1, $vital2, $vital3, $vital4, $vital5, $procedure, $compositionData];
                    } elseif ((empty($diagnosaSekunder)) && (!empty($procedurePasien)) && (empty($cekDiet))) {
                        $dataBundle = [$Encounter2, $diagnosis1, $vital1, $vital2, $vital3, $vital4, $vital5, $procedure];
                    } elseif ((empty($diagnosaSekunder)) && (empty($procedurePasien)) && (!empty($cekDiet))) {
                        $dataBundle = [$Encounter2, $diagnosis1, $vital1, $vital2, $vital3, $vital4, $vital5, $compositionData];
                    } else {
                        $dataBundle = [$Encounter2, $diagnosis1, $vital1, $vital2, $vital3, $vital4, $vital5];
                    }

                    // if ((!empty($diagnosaSekunder))) {
                    //     $dataBundle = [$Encounter1, $diagnosis1, $diagnosis2, $vital1, $vital2, $vital3, $vital4, $vital5, $procedurePasien != null ? $procedure : '', $cekDiet != null ? $compositionData : ''];
                    // } else {
                    //     $dataBundle = [$Encounter2, $diagnosis1, $vital1, $vital2, $vital3, $vital4, $vital5, $procedurePasien != null ? $procedure : '', $cekDiet != null ? $compositionData : ''];
                    // }


                    // if ($dataPengunjung->no_rawat == '2022/09/16/000022') {
                    // dd($dataBundle);
                    // }

                    SatuSehatController::getTokenSehat();
                    $access_token = Session::get('tokenSatuSehat');
                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => [
                                "resourceType" => "Bundle",
                                "type" => "transaction",
                                "entry" => $dataBundle
                            ]
                        ]);
                    } catch (RequestException $e) {
                        // echo $e->getRequest();
                        // $status = $e->getStatusCode();
                        // dd($status);
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();
                            // $status = $e->getStatusCode();
                            // dd($response->getBody());
                            // if ($response->statusCode != '200' || $response->statusCode != '201') {
                            //     goto KirimPasienlain;
                            // }
                            $test = json_decode($response->getBody());
                            // dd($dataBundle, $test);
                            // dd($test->issue[0]->expression[0]);
                            // $pesan = $test->issue[0]->expression[0];
                            $errorCode = (array) $test;

                            if (!empty($errorCode)) {
                                if (!empty($errorCode['issue'][0])) {
                                    $pesan = $errorCode['issue'][0]->details->text;
                                    if (str_contains($pesan, 'duplicate')) {
                                        $simpan = new ResponseSatuSehat();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                                        $simpan->encounter_id = 'duplicate';
                                        $simpan->save();
                                    }
                                } else {

                                    $pesan = $errorCode['fault']->faultstring;
                                    if (str_contains($pesan, 'Rate limit quota violation')) {
                                        Session::flash('error', $pesan);
                                        goto Selesai;
                                    }
                                }

                                $cek = LogErrorSatuSehat::where('subject', 'Bundle Ralan')
                                    ->where('keterangan', 'like', '%' . $dataPengunjung->no_rawat . '%')
                                    ->whereDate('created_at', Carbon::now())
                                    ->get();
                                if ($cek->count() < 1) {
                                    $error = new LogErrorSatuSehat();
                                    $error->subject = 'Bundle Ralan';
                                    $error->keterangan = $dataPengunjung->no_rawat . ' error kirim "' . $pesan . '"';
                                    $error->save();
                                }

                                $message = "Error kirim bundle Pengunjung $dataPengunjung->no_rawat";

                                Session::flash('error', $message);
                            }

                            goto KirimPasienlain;
                        } else {
                            $message = "Error kirim bundle Pengunjung $dataPengunjung->no_rawat, no response!";

                            Session::flash('error', $message);

                            goto KirimPasienlain;
                        }
                    }

                    // dd($response->getStatusCode());

                    $data = json_decode($response->getBody());

                    // if ($dataPengunjung->no_rawat == '2022/09/16/000022') {
                    // dd($data);
                    // }

                    if (!empty($data->entry)) {
                        foreach ($data->entry as $index => $dataRespone) {
                            foreach ($dataRespone as $dataPoint) {
                                // dd($dataPoint);
                                if (!empty($diagnosaSekunder)) {
                                    if (($index == 0) and ($dataPoint->resourceType == 'Encounter')) {
                                        $simpan = new ResponseSatuSehat();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                                        $simpan->encounter_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 1) and ($dataPoint->resourceType == 'Condition')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->condition_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 2) and ($dataPoint->resourceType == 'Condition')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->condition2_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 3) and ($dataPoint->resourceType == 'Observation')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->heart_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 4) and ($dataPoint->resourceType == 'Observation')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->respiratory_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 5) and ($dataPoint->resourceType == 'Observation')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->systol_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 6) and ($dataPoint->resourceType == 'Observation')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->diastol_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 7) and ($dataPoint->resourceType == 'Observation')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->temperature_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 8) and ($dataPoint->resourceType == 'Procedure')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->procedure_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 8) and ($dataPoint->resourceType == 'Composition')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->composition_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 9) and ($dataPoint->resourceType == 'Composition')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->composition_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    }
                                } else {
                                    if (($index == 0) and ($dataPoint->resourceType == 'Encounter')) {
                                        $simpan = new ResponseSatuSehat();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                                        $simpan->encounter_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 1) and ($dataPoint->resourceType == 'Condition')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->condition_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 2) and ($dataPoint->resourceType == 'Observation')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->heart_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 3) and ($dataPoint->resourceType == 'Observation')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->respiratory_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 4) and ($dataPoint->resourceType == 'Observation')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->systol_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 5) and ($dataPoint->resourceType == 'Observation')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->diastol_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 6) and ($dataPoint->resourceType == 'Observation')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->temperature_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 7) and ($dataPoint->resourceType == 'Procedure')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->procedure_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 7) and ($dataPoint->resourceType == 'Composition')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->composition_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    } elseif (($index == 8) and ($dataPoint->resourceType == 'Composition')) {
                                        $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                        $simpan->noRawat = $dataPengunjung->no_rawat;
                                        $simpan->composition_id = $dataPoint->resourceID;
                                        $simpan->save();
                                    }
                                }
                            }
                        }

                        $cekLog = LogErrorSatuSehat::where('subject', 'Bundle Ralan')
                            ->where('keterangan', 'like', '%' . $dataPengunjung->no_rawat . '%')
                            ->whereDate('created_at', Carbon::now())
                            ->get();
                        if (!empty($cekLog)) {
                            $hapus = LogErrorSatuSehat::where('subject', 'Bundle Ralan')
                                ->where('keterangan', 'like', '%' . $dataPengunjung->no_rawat . '%')
                                ->delete();
                        }
                        //Tambah variabel loop
                        ++$loop;
                    }

                    if ($loop == 10) {
                        goto Selesai;
                    }

                    KirimPasienlain:
                }
            }
            // }
        }
        // $dataLog = ResponseSatuSehat::whereDate('created_at', Carbon::now())
        //     ->get();
        Selesai:
        if (empty($request->get('tanggal'))) {
            $dataLog = ResponseSatuSehat::whereDate('created_at', $pasien_tanggal)->get();
        } else {
            $dataLog = ResponseSatuSehat::whereDate('tgl_registrasi', $pasien_tanggal)
                ->get();
        }
        // dd($dataLog);

        return view('satu_sehat.client_bundle', compact('dataLog'));
    }

    public function sendSingleBundle($norawat)
    {
        $norawat = Crypt::decrypt($norawat);

        $dataPengunjung = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli',
                'poliklinik.kd_poli'

            )
            ->where('reg_periksa.no_rawat', $norawat)
            ->first();

        $cekLog = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->count();
        if (($cekLog == 0) && ($dataPengunjung->nm_poli != 'IGD')) {
            $idRS = env('IDRS');
            //Karena masih masalah diminta kirim pakai dummy dulu
            $idPasien = SatuSehatController::patientSehat($dataPengunjung->ktp_pasien);
            if ($idPasien == null) {
                $cek = LogErrorSatuSehat::where('subject', 'Pasien')
                    ->where('keterangan', 'like', '%' . $dataPengunjung->no_rkm_medis . '%')
                    ->whereDate('created_at', Carbon::now())
                    ->get();

                if ($cek->count() == 0) {
                    $error = new LogErrorSatuSehat();
                    $error->subject = 'Pasien';
                    $error->keterangan =  ' cek NIK Pasien no RM ' . $dataPengunjung->no_rkm_medis;
                    $error->save();
                }
            }
            $idDokter = SatuSehatController::practitioner($dataPengunjung->ktp_dokter);
            if ($idDokter == null) {
                $cek = LogErrorSatuSehat::where('subject', 'Praktitioner')
                    ->where('keterangan', 'like', '%' . $dataPengunjung->nama_dokter . '%')
                    ->whereDate('created_at', Carbon::now())
                    ->get();

                if ($cek->count() == 0) {
                    $error = new LogErrorSatuSehat();
                    $error->subject = 'Praktitioner';
                    $error->keterangan = $dataPengunjung->nama_dokter . ' tidak ditemukan';
                    $error->save();
                }
            }
            $idLokasi = SatuSehatController::getIdPoli($dataPengunjung->kd_poli);
            $diagnosaPrimer = SatuSehatController::getDiagnosisPrimerRalan($dataPengunjung->no_rawat);

            // dd($dataPengunjung->no_rawat, $idPasien, $idDokter, $idLokasi, $diagnosaPrimer);

            if ((!empty($idPasien)) && (!empty($idDokter)) && (!empty($diagnosaPrimer)) && (!empty($idLokasi))) {

                $diagnosaSekunder = SatuSehatController::getDiagnosisSekunderRalan($dataPengunjung->no_rawat);
                // dd($diagnosaSekunder);
                $procedurePasien = SatuSehatController::getProcedureRalan($dataPengunjung->no_rawat);
                $cekDiet = SatuSehatController::getDiet($dataPengunjung->no_rawat, $dataPengunjung->tgl_registrasi); //nyoba bundle composition
                $waktuKeperawatan = SatuSehatController::getWaktuKeperawatan($dataPengunjung->no_rawat);

                //Definisi Vital
                $vital = SatuSehatController::getVital($dataPengunjung->no_rawat);
                if (!empty($vital)) {
                    if ($vital->nadi != '-') {
                        $heartRate = floatval($vital->nadi);
                    } else {
                        $heartRate = floatval(80);
                    }

                    if ($vital->respirasi != '-') {
                        $respiratory = floatval($vital->respirasi);
                    } else {
                        $respiratory = floatval(20);
                    }
                    if ($vital->tensi != '-') {
                        $darah = explode('/', $vital->tensi);
                        $sistole = floatval($darah[0]);
                        if (!empty($darah[1])) {
                            $diastole = floatval($darah[1]);
                        } else {
                            $diastole = floatval(80);
                        }
                    } else {
                        $sistole = floatval(120);
                        $diastole = floatval(80);
                    }

                    if ($vital->suhu_tubuh != '-') {
                        $temperature = floatval($vital->suhu_tubuh);
                    } else {
                        $temperature = floatval(37);
                    }
                } else {
                    $heartRate = floatval(80);
                    $sistole = floatval(120);
                    $diastole = floatval(80);
                    $respiratory = floatval(20);
                    $temperature = floatval(37);
                }

                //Waktu
                $waktuAwal = $waktuInprogress = $waktuSelesai = null;
                $waktuAwal = $dataPengunjung->tgl_registrasi . ' ' . $dataPengunjung->jam_reg;
                $waktu_mulai = new Carbon($waktuAwal);
                if ((!empty($waktuKeperawatan->tanggal))) {
                    $waktuInprogress = Carbon::parse($waktuKeperawatan->tanggal);
                    if ($waktu_mulai > $waktuInprogress) {
                        goto WaktuProses2;
                    }
                } else {
                    WaktuProses2:
                    $waktuInprogress = Carbon::parse($waktuAwal)->addMinute(10);
                }

                if ((!empty($vital->tgl_perawatan))) {
                    $waktuSelesai = Carbon::parse($vital->tgl_perawatan . ' ' . $vital->jam_rawat);
                    if ($waktuInprogress > $waktuSelesai) {

                        // goto WaktuSelesai2;
                        $selisih = $waktuInprogress->diffInMinutes($waktu_mulai, true);
                        $waktuSelesai = Carbon::parse($waktuAwal)->addMinute($selisih + 5);
                    }
                } else {
                    WaktuSelesai2:
                    $selisih = $waktuInprogress->diffInMinutes($waktu_mulai, true);
                    $waktuSelesai = Carbon::parse($waktuAwal)->addMinute($selisih + 5);
                }
                // dd($waktu_mulai, $waktuInprogress, $waktuSelesai, $selisih);
                $formatWaktuMulai = $waktu_mulai->setTimezone('UTC')->toW3cString();

                $formatWaktuProgress = $waktuInprogress->setTimezone('UTC')->toW3cString();

                $formatWaktuSelesai = $waktuSelesai->setTimezone('UTC')->toW3cString();

                $day = Carbon::parse($waktuAwal)->dayName;
                $day2 = Carbon::parse($waktuAwal)->format('d F Y');
                $formatDay = $day . ', ' . $day2;

                //UUID
                $uuidEncounter = Str::uuid();
                $uuidDiagnosaPrimer = Str::uuid();

                $uuidHeart = Str::uuid();
                $uuidRespiratory = Str::uuid();
                $uuidSistol = Str::uuid();
                $uuidDiastol = Str::uuid();
                $uuidTemperature = Str::uuid();
                if ($diagnosaSekunder != null) {
                    $uuidDiagnosaSekunder = Str::uuid();
                    //encounter 2 diagnosa
                    $Encounter1 = [
                        "fullUrl" => "urn:uuid:$uuidEncounter",
                        "resource" => [
                            "resourceType" => "Encounter",
                            "status" => "finished", //awal finished diganti in-progress
                            "class" => [
                                "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                                "code" => "AMB",
                                "display" => "ambulatory"
                            ],
                            "subject" => [
                                "reference" => "Patient/$idPasien",
                                "display" => "$dataPengunjung->nm_pasien"
                            ],
                            "participant" => [
                                [
                                    "type" => [
                                        [
                                            "coding" => [
                                                [
                                                    "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                                    "code" => "ATND",
                                                    "display" => "attender"
                                                ]
                                            ]
                                        ]
                                    ],
                                    "individual" => [
                                        "reference" => "Practitioner/$idDokter",
                                        "display" => "$dataPengunjung->nama_dokter"
                                    ]
                                ]
                            ],
                            "period" => [
                                "start" => "$formatWaktuMulai",
                                "end" => "$formatWaktuSelesai"
                            ],
                            "location" => [
                                [
                                    "location" => [
                                        "reference" => "Location/$idLokasi",
                                        "display" => "$dataPengunjung->nm_poli"
                                    ]
                                ]
                            ],
                            "diagnosis" => [
                                [
                                    "condition" => [
                                        "reference" => "urn:uuid:$uuidDiagnosaPrimer",
                                        "display" => "$diagnosaPrimer->nm_penyakit"
                                    ],
                                    "use" => [
                                        "coding" => [
                                            [
                                                "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
                                                "code" => "DD",
                                                "display" => "Discharge diagnosis"
                                            ]
                                        ]
                                    ],
                                    "rank" => 1
                                ],
                                [
                                    "condition" => [
                                        "reference" => "urn:uuid:$uuidDiagnosaSekunder",
                                        "display" => "$diagnosaSekunder->nm_penyakit"
                                    ],
                                    "use" => [
                                        "coding" => [
                                            [
                                                "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
                                                "code" => "DD",
                                                "display" => "Discharge diagnosis"
                                            ]
                                        ]
                                    ],
                                    "rank" => 2
                                ]

                            ],
                            "statusHistory" => [
                                [
                                    "status" => "arrived",
                                    "period" => [
                                        "start" => "$formatWaktuMulai",
                                        "end" => "$formatWaktuProgress"
                                    ]
                                ],
                                [
                                    "status" => "in-progress",
                                    "period" => [
                                        "start" => "$formatWaktuProgress",
                                        "end" => "$formatWaktuSelesai"
                                    ]
                                ],
                                [
                                    "status" => "finished",
                                    "period" => [
                                        "start" => "$formatWaktuSelesai",
                                        "end" => "$formatWaktuSelesai"
                                    ]
                                ]
                            ],
                            "serviceProvider" => [
                                "reference" => "Organization/$idRS"
                            ],
                            "identifier" => [
                                [
                                    "system" => "http://sys-ids.kemkes.go.id/encounter/$idRS",
                                    "value" => "$dataPengunjung->no_rawat"
                                ]
                            ]
                        ],
                        "request" => [
                            "method" => "POST",
                            "url" => "Encounter"
                        ]
                    ];
                    //diagnosa 2
                    $diagnosis2 = [
                        "fullUrl" => "urn:uuid:$uuidDiagnosaSekunder",
                        "resource" => [
                            "resourceType" => "Condition",
                            "clinicalStatus" => [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/condition-clinical",
                                        "code" => "active",
                                        "display" => "Active"
                                    ]
                                ]
                            ],
                            "category" => [
                                [
                                    "coding" => [
                                        [
                                            "system" => "http://terminology.hl7.org/CodeSystem/condition-category",
                                            "code" => "encounter-diagnosis",
                                            "display" => "Encounter Diagnosis"
                                        ]
                                    ]
                                ]
                            ],
                            "code" => [
                                "coding" => [
                                    [
                                        "system" => "http://hl7.org/fhir/sid/icd-10",
                                        "code" => "$diagnosaSekunder->kd_penyakit",
                                        "display" => "$diagnosaSekunder->nm_penyakit"
                                    ]
                                ]
                            ],
                            "subject" => [
                                "reference" => "Patient/$idPasien",
                                "display" => "$dataPengunjung->nm_pasien"
                            ],
                            "encounter" => [
                                "reference" => "urn:uuid:$uuidEncounter",
                                "display" => "Kunjungan $dataPengunjung->nm_pasien di hari $formatDay"
                            ]
                        ],
                        "request" => [
                            "method" => "POST",
                            "url" => "Condition"
                        ]
                    ];
                } else {
                    //Ecounter 1 diagnosa
                    $Encounter2 = [
                        "fullUrl" => "urn:uuid:$uuidEncounter",
                        "resource" => [
                            "resourceType" => "Encounter",
                            "status" => "finished", //coba diganti in-progress dari finished
                            "class" => [
                                "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                                "code" => "AMB",
                                "display" => "ambulatory"
                            ],
                            "subject" => [
                                "reference" => "Patient/$idPasien",
                                "display" => "$dataPengunjung->nm_pasien"
                            ],
                            "participant" => [
                                [
                                    "type" => [
                                        [
                                            "coding" => [
                                                [
                                                    "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                                    "code" => "ATND",
                                                    "display" => "attender"
                                                ]
                                            ]
                                        ]
                                    ],
                                    "individual" => [
                                        "reference" => "Practitioner/$idDokter",
                                        "display" => "$dataPengunjung->nama_dokter"
                                    ]
                                ]
                            ],
                            "period" => [
                                "start" => "$formatWaktuMulai",
                                "end" => "$formatWaktuSelesai"
                            ],
                            "location" => [
                                [
                                    "location" => [
                                        "reference" => "Location/$idLokasi",
                                        "display" => "$dataPengunjung->nm_poli"
                                    ]
                                ]
                            ],
                            "diagnosis" => [
                                [
                                    "condition" => [
                                        "reference" => "urn:uuid:$uuidDiagnosaPrimer",
                                        "display" => "$diagnosaPrimer->nm_penyakit"
                                    ],
                                    "use" => [
                                        "coding" => [
                                            [
                                                "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
                                                "code" => "DD",
                                                "display" => "Discharge diagnosis"
                                            ]
                                        ]
                                    ],
                                    "rank" => 1
                                ]
                            ],
                            "statusHistory" => [
                                [
                                    "status" => "arrived",
                                    "period" => [
                                        "start" => "$formatWaktuMulai",
                                        "end" => "$formatWaktuProgress"
                                    ]
                                ],
                                [
                                    "status" => "in-progress",
                                    "period" => [
                                        "start" => "$formatWaktuProgress",
                                        "end" => "$formatWaktuSelesai"
                                    ]
                                ],
                                [
                                    "status" => "finished",
                                    "period" => [
                                        "start" => "$formatWaktuSelesai",
                                        "end" => "$formatWaktuSelesai"
                                    ]
                                ]
                            ],
                            "serviceProvider" => [
                                "reference" => "Organization/$idRS"
                            ],
                            "identifier" => [
                                [
                                    "system" => "http://sys-ids.kemkes.go.id/encounter/$idRS",
                                    "value" => "$dataPengunjung->no_rawat"
                                ]
                            ]
                        ],
                        "request" => [
                            "method" => "POST",
                            "url" => "Encounter"
                        ]
                    ];
                }

                //diagnosa 1
                $diagnosis1 = [
                    "fullUrl" => "urn:uuid:$uuidDiagnosaPrimer",
                    "resource" => [
                        "resourceType" => "Condition",
                        "clinicalStatus" => [
                            "coding" => [
                                [
                                    "system" => "http://terminology.hl7.org/CodeSystem/condition-clinical",
                                    "code" => "active",
                                    "display" => "Active"
                                ]
                            ]
                        ],
                        "category" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/condition-category",
                                        "code" => "encounter-diagnosis",
                                        "display" => "Encounter Diagnosis"
                                    ]
                                ]
                            ]
                        ],
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://hl7.org/fhir/sid/icd-10",
                                    "code" => "$diagnosaPrimer->kd_penyakit",
                                    "display" => "$diagnosaPrimer->nm_penyakit"
                                ]
                            ]
                        ],
                        "subject" => [
                            "reference" => "Patient/$idPasien",
                            "display" => "$dataPengunjung->nm_pasien"
                        ],
                        "encounter" => [
                            "reference" => "urn:uuid:$uuidEncounter",
                            "display" => "Kunjungan $dataPengunjung->nm_pasien di hari $formatDay"
                        ]
                    ],
                    "request" => [
                        "method" => "POST",
                        "url" => "Condition"
                    ]
                ];

                $vital1 = [
                    "fullUrl" => "urn:uuid:$uuidHeart",
                    "resource" => [
                        "resourceType" => "Observation",
                        "status" => "final",
                        "category" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                        "code" => "vital-signs",
                                        "display" => "Vital Signs"
                                    ]
                                ]
                            ]
                        ],
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://loinc.org",
                                    "code" => "8867-4",
                                    "display" => "Heart rate"
                                ]
                            ]
                        ],
                        "subject" => [
                            "reference" => "Patient/$idPasien"
                        ],
                        "performer" => [
                            [
                                "reference" => "Practitioner/10004181193"
                            ]
                        ],
                        "encounter" => [
                            "reference" => "urn:uuid:$uuidEncounter",
                            "display" => "Pemeriksaan Fisik Nadi $dataPengunjung->nm_pasien di hari $formatDay"
                        ],
                        "effectiveDateTime" => "$formatWaktuProgress",
                        "valueQuantity" => [
                            "value" => $heartRate,
                            "unit" => "beats/minute",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "/min"
                        ]
                    ],
                    "request" => [
                        "method" => "POST",
                        "url" => "Observation"
                    ]
                ];
                $vital2 = [
                    "fullUrl" => "urn:uuid:$uuidRespiratory",
                    "resource" => [
                        "resourceType" => "Observation",
                        "status" => "final",
                        "category" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                        "code" => "vital-signs",
                                        "display" => "Vital Signs"
                                    ]
                                ]
                            ]
                        ],
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://loinc.org",
                                    "code" => "9279-1",
                                    "display" => "Respiratory rate"
                                ]
                            ]
                        ],
                        "subject" => [
                            "reference" => "Patient/$idPasien"
                        ],
                        "performer" => [
                            [
                                "reference" => "Practitioner/10004181193"
                            ]
                        ],
                        "encounter" => [
                            "reference" => "urn:uuid:$uuidEncounter",
                            "display" => "Pemeriksaan Fisik Pernafasan $dataPengunjung->nm_pasien di hari $formatDay"
                        ],
                        "effectiveDateTime" => "$formatWaktuProgress",
                        "valueQuantity" => [
                            "value" => $respiratory,
                            "unit" => "breaths/minute",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "/min"
                        ]
                    ],
                    "request" => [
                        "method" => "POST",
                        "url" => "Observation"
                    ]
                ];
                $vital3 = [
                    "fullUrl" => "urn:uuid:$uuidSistol",
                    "resource" => [
                        "resourceType" => "Observation",
                        "status" => "final",
                        "category" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                        "code" => "vital-signs",
                                        "display" => "Vital Signs"
                                    ]
                                ]
                            ]
                        ],
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://loinc.org",
                                    "code" => "8480-6",
                                    "display" => "Systolic blood pressure"
                                ]
                            ]
                        ],
                        "subject" => [
                            "reference" => "Patient/$idPasien"
                        ],
                        "performer" => [
                            [
                                "reference" => "Practitioner/10004181193"
                            ]
                        ],
                        "encounter" => [
                            "reference" => "urn:uuid:$uuidEncounter",
                            "display" => "Pemeriksaan Fisik Sistolik $dataPengunjung->nm_pasien di hari $formatDay"
                        ],
                        "effectiveDateTime" => "$formatWaktuProgress",
                        "bodySite" => [
                            "coding" => [
                                [
                                    "system" => "http://snomed.info/sct",
                                    "code" => "368209003",
                                    "display" => "Right arm"
                                ]
                            ]
                        ],
                        "valueQuantity" => [
                            "value" => $sistole,
                            "unit" => "mm[Hg]",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "mm[Hg]"
                        ]
                    ],
                    "request" => [
                        "method" => "POST",
                        "url" => "Observation"
                    ]
                ];
                $vital4 = [
                    "fullUrl" => "urn:uuid:$uuidDiastol",
                    "resource" => [
                        "resourceType" => "Observation",
                        "status" => "final",
                        "category" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                        "code" => "vital-signs",
                                        "display" => "Vital Signs"
                                    ]
                                ]
                            ]
                        ],
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://loinc.org",
                                    "code" => "8462-4",
                                    "display" => "Diastolic blood pressure"
                                ]
                            ]
                        ],
                        "subject" => [
                            "reference" => "Patient/$idPasien",
                            "display" => "$dataPengunjung->nm_pasien"
                        ],
                        "performer" => [
                            [
                                "reference" => "Practitioner/10004181193"
                            ]
                        ],
                        "encounter" => [
                            "reference" => "urn:uuid:$uuidEncounter",
                            "display" => "Pemeriksaan Fisik Diastolik $dataPengunjung->nm_pasien di hari $formatDay"
                        ],
                        "effectiveDateTime" => "$formatWaktuProgress",
                        "bodySite" => [
                            "coding" => [
                                [
                                    "system" => "http://snomed.info/sct",
                                    "code" => "368209003",
                                    "display" => "Right arm"
                                ]
                            ]
                        ],
                        "valueQuantity" => [
                            "value" => $diastole,
                            "unit" => "mm[Hg]",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "mm[Hg]"
                        ]
                    ],
                    "request" => [
                        "method" => "POST",
                        "url" => "Observation"
                    ]
                ];
                $vital5 = [
                    "fullUrl" => "urn:uuid:$uuidTemperature",
                    "resource" => [
                        "resourceType" => "Observation",
                        "status" => "final",
                        "category" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                        "code" => "vital-signs",
                                        "display" => "Vital Signs"
                                    ]
                                ]
                            ]
                        ],
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://loinc.org",
                                    "code" => "8310-5",
                                    "display" => "Body temperature"
                                ]
                            ]
                        ],
                        "subject" => [
                            "reference" => "Patient/$idPasien"
                        ],
                        "performer" => [
                            [
                                "reference" => "Practitioner/10004181193"
                            ]
                        ],
                        "encounter" => [
                            "reference" => "urn:uuid:$uuidEncounter",
                            "display" => "Pemeriksaan Fisik Suhu $dataPengunjung->nm_pasien di hari $formatDay"
                        ],
                        "effectiveDateTime" => "$formatWaktuProgress",
                        "valueQuantity" => [
                            "value" => $temperature,
                            "unit" => "C",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "Cel"
                        ]
                    ],
                    "request" => [
                        "method" => "POST",
                        "url" => "Observation"
                    ]
                ];
                if ($procedurePasien != null) {
                    $uuidProcedure = Str::uuid();

                    $procedure = [
                        "fullUrl" => "urn:uuid:$uuidProcedure",
                        "resource" => [
                            "resourceType" => "Procedure",
                            "status" => "completed",
                            "category" => [
                                "coding" => [
                                    [
                                        "system" => "http://snomed.info/sct",
                                        "code" => "103693007",
                                        "display" => "Diagnostic procedure"
                                    ]
                                ],
                                "text" => "Diagnostic procedure"
                            ],
                            "code" => [
                                "coding" => [
                                    [
                                        "system" => "http://hl7.org/fhir/sid/icd-9-cm",
                                        "code" => "$procedurePasien->kode",
                                        "display" => "$procedurePasien->deskripsi_panjang"
                                    ]
                                ]
                            ],
                            "subject" => [
                                "reference" => "Patient/$idPasien",
                                "display" => "$dataPengunjung->nm_pasien"
                            ],
                            "encounter" => [
                                "reference" => "urn:uuid:$uuidEncounter",
                                "display" => "Tindakan yang dilakukan kepada pasien $dataPengunjung->nm_pasien pada $formatDay"
                            ],
                            "performedPeriod" => [
                                "start" => "$formatWaktuProgress",
                                "end" => "$formatWaktuSelesai"
                            ],
                            "performer" => [
                                [
                                    "actor" => [
                                        "reference" => "Practitioner/$idDokter",
                                        "display" => "$dataPengunjung->nama_dokter"
                                    ]
                                ]
                            ],
                            "reasonCode" => [
                                [
                                    "coding" => [
                                        [
                                            "system" => "http://hl7.org/fhir/sid/icd-10",
                                            "code" => "$diagnosaPrimer->kd_penyakit",
                                            "display" => "$diagnosaPrimer->nm_penyakit"
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "request" => [
                            "method" => "POST",
                            "url" => "Procedure"
                        ]
                    ];
                }
                if ($cekDiet != null) {
                    $uuidComposition = Str::uuid();

                    $compositionData = [
                        "fullUrl" => "urn:uuid:$uuidComposition",
                        "resource" => [
                            "resourceType" => "Composition",
                            "identifier" => [
                                "system" => "http://sys-ids.kemkes.go.id/composition/$idRS",
                                "value" => "$dataPengunjung->no_rawat"
                            ],
                            "status" => "final",
                            "type" => [
                                "coding" => [
                                    [
                                        "system" => "http://loinc.org",
                                        "code" => "18842-5",
                                        "display" => "Discharge summary"
                                    ]
                                ]
                            ],
                            "category" => [
                                [
                                    "coding" => [
                                        [
                                            "system" => "http://loinc.org",
                                            "code" => "LP173421-1",
                                            "display" => "Report"
                                        ]
                                    ]
                                ]
                            ],
                            "subject" => [
                                "reference" => "Patient/$idPasien",
                                "display" => "$dataPengunjung->nm_pasien"
                            ],
                            "encounter" => [
                                "reference" => "urn:uuid:$uuidEncounter",
                                "display" => "Kunjungan $dataPengunjung->nm_pasien di hari $formatDay"
                            ],
                            "date" => "$dataPengunjung->tgl_registrasi",
                            "author" => [
                                [
                                    "reference" => "Practitioner/$idDokter",
                                    "display" => "$dataPengunjung->nama_dokter"
                                ]
                            ],
                            "title" => "Resume Medis Rawat Jalan",
                            "custodian" => [
                                "reference" => "Organization/$idRS"
                            ],
                            "section" => [
                                [
                                    "code" => [
                                        "coding" => [
                                            [
                                                "system" => "http://loinc.org",
                                                "code" => "42344-2",
                                                "display" => "Discharge diet (narrative)"
                                            ]
                                        ]
                                    ],
                                    "text" => [
                                        "status" => "additional",
                                        "div" => "$cekDiet->monitoring_evaluasi"
                                    ]
                                ]
                            ],
                        ],
                        "request" => [
                            "method" => "POST",
                            "url" => "Composition"
                        ]
                    ];
                }
                if ((!empty($diagnosaSekunder)) && (!empty($procedurePasien)) && (!empty($cekDiet))) {
                    $dataBundle = [$Encounter1, $diagnosis1, $diagnosis2, $vital1, $vital2, $vital3, $vital4, $vital5, $procedure, $compositionData];
                } elseif ((!empty($diagnosaSekunder)) && (!empty($procedurePasien)) && (empty($cekDiet))) {
                    $dataBundle = [$Encounter1, $diagnosis1, $diagnosis2, $vital1, $vital2, $vital3, $vital4, $vital5, $procedure];
                } elseif ((!empty($diagnosaSekunder)) && (empty($procedurePasien)) && (!empty($cekDiet))) {
                    $dataBundle = [$Encounter1, $diagnosis1, $diagnosis2, $vital1, $vital2, $vital3, $vital4, $vital5, $compositionData];
                } elseif ((!empty($diagnosaSekunder)) && (empty($procedurePasien)) && (empty($cekDiet))) {
                    $dataBundle = [$Encounter1, $diagnosis1, $diagnosis2, $vital1, $vital2, $vital3, $vital4, $vital5];
                } elseif ((empty($diagnosaSekunder)) && (!empty($procedurePasien)) && (!empty($cekDiet))) {
                    $dataBundle = [$Encounter2, $diagnosis1, $vital1, $vital2, $vital3, $vital4, $vital5, $procedure, $compositionData];
                } elseif ((empty($diagnosaSekunder)) && (!empty($procedurePasien)) && (empty($cekDiet))) {
                    $dataBundle = [$Encounter2, $diagnosis1, $vital1, $vital2, $vital3, $vital4, $vital5, $procedure];
                } elseif ((empty($diagnosaSekunder)) && (empty($procedurePasien)) && (!empty($cekDiet))) {
                    $dataBundle = [$Encounter2, $diagnosis1, $vital1, $vital2, $vital3, $vital4, $vital5, $compositionData];
                } else {
                    $dataBundle = [$Encounter2, $diagnosis1, $vital1, $vital2, $vital3, $vital4, $vital5];
                }

                // dd($dataBundle);
                SatuSehatController::getTokenSehat();
                $access_token = Session::get('tokenSatuSehat');
                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                try {
                    $response = $client->request('POST', 'fhir-r4/v1', [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}"
                        ],
                        'json' => [
                            "resourceType" => "Bundle",
                            "type" => "transaction",
                            "entry" => $dataBundle
                        ]
                    ]);
                } catch (RequestException $e) {
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();
                        $test = json_decode($response->getBody());
                        $errorCode = (array) $test;
                        // dd($test, $dataBundle);

                        if (!empty($errorCode)) {
                            if (!empty($errorCode['issue'][0])) {
                                $pesan = $errorCode['issue'][0]->details->text;
                                if (str_contains($pesan, 'duplicate')) {
                                    $simpan = new ResponseSatuSehat();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                                    $simpan->encounter_id = 'duplicate';
                                    $simpan->save();
                                }
                            } else {

                                $pesan = $errorCode['fault']->faultstring;
                                if (str_contains($pesan, 'Rate limit quota violation')) {
                                    Session::flash('error', $pesan);
                                }
                            }

                            $cek = LogErrorSatuSehat::where('subject', 'Bundle Ralan')
                                ->where('keterangan', 'like', '%' . $dataPengunjung->no_rawat . '%')
                                ->whereDate('created_at', Carbon::now())
                                ->get();
                            if ($cek->count() < 1) {
                                $error = new LogErrorSatuSehat();
                                $error->subject = 'Bundle Ralan';
                                $error->keterangan = $dataPengunjung->no_rawat . ' error kirim "' . $pesan . '"';
                                $error->save();
                            }

                            $message = "Error kirim bundle Pengunjung $dataPengunjung->no_rawat";
                            Session::flash('error', $message);
                        }
                    } else {
                        $message = "Error kirim bundle Pengunjung $dataPengunjung->no_rawat, no response!";
                        Session::flash('error', $message);
                    }
                }

                $data = json_decode($response->getBody());

                if (!empty($data->entry)) {
                    foreach ($data->entry as $index => $dataRespone) {
                        foreach ($dataRespone as $dataPoint) {
                            // dd($dataPoint);
                            if (!empty($diagnosaSekunder)) {
                                if (($index == 0) and ($dataPoint->resourceType == 'Encounter')) {
                                    $simpan = new ResponseSatuSehat();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                                    $simpan->encounter_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 1) and ($dataPoint->resourceType == 'Condition')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->condition_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 2) and ($dataPoint->resourceType == 'Condition')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->condition2_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 3) and ($dataPoint->resourceType == 'Observation')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->heart_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 4) and ($dataPoint->resourceType == 'Observation')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->respiratory_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 5) and ($dataPoint->resourceType == 'Observation')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->systol_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 6) and ($dataPoint->resourceType == 'Observation')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->diastol_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 7) and ($dataPoint->resourceType == 'Observation')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->temperature_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 8) and ($dataPoint->resourceType == 'Procedure')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->procedure_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 8) and ($dataPoint->resourceType == 'Composition')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->composition_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 9) and ($dataPoint->resourceType == 'Composition')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->composition_id = $dataPoint->resourceID;
                                    $simpan->save();
                                }
                            } else {
                                if (($index == 0) and ($dataPoint->resourceType == 'Encounter')) {
                                    $simpan = new ResponseSatuSehat();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                                    $simpan->encounter_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 1) and ($dataPoint->resourceType == 'Condition')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->condition_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 2) and ($dataPoint->resourceType == 'Observation')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->heart_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 3) and ($dataPoint->resourceType == 'Observation')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->respiratory_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 4) and ($dataPoint->resourceType == 'Observation')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->systol_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 5) and ($dataPoint->resourceType == 'Observation')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->diastol_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 6) and ($dataPoint->resourceType == 'Observation')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->temperature_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 7) and ($dataPoint->resourceType == 'Procedure')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->procedure_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 7) and ($dataPoint->resourceType == 'Composition')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->composition_id = $dataPoint->resourceID;
                                    $simpan->save();
                                } elseif (($index == 8) and ($dataPoint->resourceType == 'Composition')) {
                                    $simpan = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->composition_id = $dataPoint->resourceID;
                                    $simpan->save();
                                }
                            }
                        }
                    }

                    $cekLog = LogErrorSatuSehat::where('subject', 'Bundle Ralan')
                        ->where('keterangan', 'like', '%' . $dataPengunjung->no_rawat . '%')
                        ->whereDate('created_at', Carbon::now())
                        ->get();
                    if (!empty($cekLog)) {
                        $hapus = LogErrorSatuSehat::where('subject', 'Bundle Ralan')
                            ->where('keterangan', 'like', '%' . $dataPengunjung->no_rawat . '%')
                            ->delete();
                    }
                    $message = "Data bundle Pengunjung $dataPengunjung->no_rawat, berhasil dikirim";
                    Session::flash('sukses', $message);
                }
            } else {
                $message = "Cek kembali data IdSehat Pasien, idDokter, diagnosa, dan idPoli Pasien";
                Session::flash('error', $message);
            }
        } else {
            $message = "Data pasien ini sudah memiliki id encounter";
            Session::flash('error', $message);
        }

        return redirect()->back();
    }

    public function sendSingleEncounter($norawat)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Api Composition');
        session()->forget('cucu');
        set_time_limit(0);

        // $pasien_tanggal = '2022-09-13';
        $norawat = Crypt::decrypt($norawat);
        // dd($norawat);
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            ->where('reg_periksa.no_rawat', $norawat)
            ->where('reg_periksa.status_lanjut', 'Ralan')
            // ->where('reg_periksa.stts', 'Sudah')
            ->get();
        // dd($data);

        foreach ($data as $key => $dataPengunjung) {
            $cekLog = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->count();

            if ($cekLog == 0) {
                $idRS = env('IDRS');
                $idPasien = SatuSehatController::patientSehat($dataPengunjung->ktp_pasien);
                $idDokter = SatuSehatController::practitioner($dataPengunjung->ktp_dokter);
                $idLokasi = SatuSehatController::getIdPoli($dataPengunjung->kd_poli);

                if ((!empty($idPasien)) && (!empty($idDokter))) {
                    //Waktu
                    //Waktu
                    $waktuAwal = $dataPengunjung->tgl_registrasi . ' ' . $dataPengunjung->jam_reg;
                    $waktu_mulai = new Carbon($waktuAwal);
                    $waktuKeperawatan = SatuSehatController::getWaktuKeperawatan($dataPengunjung->no_rawat);

                    //Definisi Vital
                    $vital = SatuSehatController::getVital($dataPengunjung->no_rawat);
                    // dd($vital);
                    // $formatWaktuMulai = Carbon::parse($waktuAwal)->format('Y-m-d') . 'T' . Carbon::parse($waktuAwal)->format('H:i:s+07:00');
                    $formatWaktuMulai = $waktu_mulai->setTimezone('UTC')->toW3cString();
                    if ((!empty($waktuKeperawatan->tanggal))) {
                        $waktuInprogress = Carbon::parse($waktuKeperawatan->tanggal);
                        if ($waktu_mulai > $waktuInprogress) {
                            goto WaktuProses2;
                        }
                    } else {
                        WaktuProses2:
                        $waktuInprogress = Carbon::parse($waktuAwal)->addMinute(10);
                        // dd($dataPengunjung->no_rawat, $waktu_mulai, $waktuInprogress);
                    }
                    // $formatWaktuProgress = Carbon::parse($waktuInprogress)->format('Y-m-d') . 'T' . Carbon::parse($waktuInprogress)->format('H:i:s+07:00');
                    $formatWaktuProgress = $waktuInprogress->setTimezone('UTC')->toW3cString();
                    if ((!empty($vital->tgl_perawatan))) {
                        $waktuSelesai = Carbon::parse($vital->tgl_perawatan . ' ' . $vital->jam_rawat);
                        if ($waktuInprogress > $waktuSelesai) {
                            goto WaktuSelesai2;
                        }
                    } else {
                        WaktuSelesai2:
                        $waktuSelesai = Carbon::parse($waktuAwal)->addMinute(30);
                        // dd($dataPengunjung->no_rawat, $waktu_mulai, $waktuInprogress, $waktuSelesai);
                    }
                    // $formatWaktuSelesai = Carbon::parse($waktuSelesai)->format('Y-m-d') . 'T' . Carbon::parse($waktuSelesai)->format('H:i:s+07:00');
                    $formatWaktuSelesai = $waktuSelesai->setTimezone('UTC')->toW3cString();

                    $dataEncounter = [
                        "resourceType" => "Encounter",
                        "identifier" => [
                            [
                                "system" => "http://sys-ids.kemkes.go.id/encounter/$idRS",
                                "value" => "$dataPengunjung->no_rawat"
                            ]
                        ],
                        "status" => "arrived",
                        "class" => [
                            "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                            "code" => "AMB",
                            "display" => "ambulatory"
                        ],
                        "subject" => [
                            "reference" => "Patient/$idPasien",
                            "display" => "$dataPengunjung->nm_pasien"
                        ],
                        "participant" => [
                            [
                                "type" => [
                                    [
                                        "coding" => [
                                            [
                                                "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                                "code" => "ATND",
                                                "display" => "attender"
                                            ]
                                        ]
                                    ]
                                ],
                                "individual" => [
                                    "reference" => "Practitioner/$idDokter",
                                    "display" => "$dataPengunjung->nama_dokter"
                                ]
                            ]
                        ],
                        "period" => [
                            "start" => "$formatWaktuMulai"
                        ],
                        "location" => [
                            [
                                "location" => [
                                    "reference" => "Location/$idLokasi",
                                    "display" => "$dataPengunjung->nm_poli"
                                ]
                            ]
                        ],
                        "statusHistory" => [
                            [
                                "status" => "arrived",
                                "period" => [
                                    "start" => "$formatWaktuMulai",
                                    "end" => "$formatWaktuProgress"
                                ]
                            ]
                        ],
                        "serviceProvider" => [
                            "reference" => "Organization/$idRS"
                        ]
                    ];

                    // dd($dataEncounter);

                    //Send data
                    SatuSehatController::getTokenSehat();
                    $access_token = Session::get('tokenSatuSehat');
                    // dd($access_token);
                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Encounter', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataEncounter
                        ]);
                    } catch (BadResponseException $e) {
                        // echo $e->getRequest();
                        // echo $e->getResponse();
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            // dd($response);
                            $test = json_decode($response->getBody());
                            // dd($test->issue[0]->code, 'error2');
                            if (!empty($test->issue[0]->code == 'duplicate')) {
                                $simpan = new ResponseSatuSehat();
                                $simpan->noRawat = $dataPengunjung->no_rawat;
                                $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                                $simpan->encounter_id = 'duplicate encounter';
                                $simpan->save();
                            }
                        }

                        $message = $test->issue[0]->code;

                        Session::flash('error', $message);

                        return redirect()->back()->withInput();
                    }

                    // dd($response);

                    $data = json_decode($response->getBody());

                    // dd($data, $dataEncounter);
                    if ($data->id) {
                        $simpan = new ResponseSatuSehat();
                        $simpan->noRawat = $dataPengunjung->no_rawat;
                        $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                        $simpan->encounter_id = $data->id;
                        $simpan->save();

                        Session::flash('sukses', 'Data encounter berhasil dikirim');
                    }
                }
            } else {
                Session::flash('error', 'Data encounter sudah pernah dikirim');
            }
        }

        return redirect()->back();
    }

    public function sendComposition()
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Rajal Satu Sehat');
        session()->put('cucu', 'API Composition');
        set_time_limit(0);

        $pasien_tanggal = '2023-04-28';
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            ->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->where('reg_periksa.stts', 'Sudah')
            ->get();
        // dd($data);

        $lopp = 0;

        foreach ($data as $key => $dataPengunjung) {
            $cekLog = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
            $cekDiet = SatuSehatController::getDiet('2023/04/28/000005', '2023-04-28');

            // dd($cekDiet, $cekLog);

            if ((!empty($cekLog))  && (!empty($cekDiet))) {
                // dd($cekLog, $cekDiet->monitoring_evaluasi);
                $idRS = '100025586';
                $idPasien = SatuSehatController::patientSehat($dataPengunjung->ktp_pasien);
                $idDokter = SatuSehatController::practitioner($dataPengunjung->ktp_dokter);
                $idLokasi = SatuSehatController::getIdPoli($dataPengunjung->kd_poli);
                //Waktu
                $waktuAwal = $dataPengunjung->tgl_registrasi . ' ' . $dataPengunjung->jam_reg;
                $waktu_mulai = new Carbon($waktuAwal);
                $waktuSelesai = Carbon::parse($waktuAwal)->addHour(2);
                $formatWaktuMulai = Carbon::parse($waktuAwal)->format('Y-m-d') . 'T' . Carbon::parse($waktuAwal)->format('H:i:s+07:00');
                $waktuInprogress = Carbon::parse($waktuAwal)->addHour();
                $formatWaktuProgress = Carbon::parse($waktuInprogress)->format('Y-m-d') . 'T' . Carbon::parse($waktuInprogress)->format('H:i:s+07:00');
                $formatWaktuSelesai = Carbon::parse($waktuSelesai)->format('Y-m-d') . 'T' . Carbon::parse($waktuSelesai)->format('H:i:s+07:00');
                $day = Carbon::parse($waktuAwal)->dayName;
                $day2 = Carbon::parse($waktuAwal)->format('d F Y');
                $formatDay = $day . ', ' . $day2;

                $compositionData = [
                    "resourceType" => "Composition",
                    "identifier" => [
                        "system" => "http://sys-ids.kemkes.go.id/composition/$idRS",
                        "value" => "$dataPengunjung->no_rawat"
                    ],
                    "status" => "final",
                    "type" => [
                        "coding" => [
                            [
                                "system" => "http://loinc.org",
                                "code" => "18842-5",
                                "display" => "Discharge summary"
                            ]
                        ]
                    ],
                    "category" => [
                        [
                            "coding" => [
                                [
                                    "system" => "http://loinc.org",
                                    "code" => "LP173421-1",
                                    "display" => "Report"
                                ]
                            ]
                        ]
                    ],
                    "subject" => [
                        "reference" => "Patient/$idPasien",
                        "display" => "$dataPengunjung->nm_pasien"
                    ],
                    "encounter" => [
                        "reference" => "Encounter/$cekLog->encounter_id",
                        "display" => "Kunjungan $dataPengunjung->nm_pasien di hari $formatDay"
                    ],
                    "date" => "$dataPengunjung->tgl_registrasi",
                    "author" => [
                        [
                            "reference" => "Practitioner/$idDokter",
                            "display" => "$dataPengunjung->nama_dokter"
                        ]
                    ],
                    "title" => "Resume Medis Rawat Jalan",
                    "custodian" => [
                        "reference" => "Organization/$idRS"
                    ],
                    "section" => [
                        [
                            "code" => [
                                "coding" => [
                                    [
                                        "system" => "http://loinc.org",
                                        "code" => "42344-2",
                                        "display" => "Discharge diet (narrative)"
                                    ]
                                ]
                            ],
                            "text" => [
                                "status" => "additional",
                                "div" => "$cekDiet->monitoring_evaluasi"
                            ]
                        ]
                    ]
                ];

                // dd($compositionData);
                //Send data
                SatuSehatController::getTokenSehat();
                $access_token = Session::get('tokenSatuSehat');
                // dd($access_token);
                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                try {
                    $response = $client->request('POST', 'fhir-r4/pre-prod/v1/Composition', [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}"
                        ],
                        'json' => $compositionData
                    ]);
                } catch (BadResponseException $e) {
                    // echo $e->getRequest();
                    // echo $e->getResponse();
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();

                        // dd($response);
                        $test = json_decode($response->getBody());
                        dd($test);
                    }

                    $message = "";

                    Session::flash('error', $message);

                    return redirect()->back()->withInput();
                }

                // dd($response);

                $data = json_decode($response->getBody());

                dd($data);
            }
        }

        Selesai:

        dd('Selesai');
    }

    public function sendMedication(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Rajal Satu Sehat');
        session()->put('cucu', 'API Medication');
        set_time_limit(0);

        if (empty($request->get('tanggal'))) {
            $pasien_tanggal = Carbon::now()->format('Y-m-d');
            $kemarin = Carbon::yesterday();

            $data = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
                ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
                ->select(
                    'reg_periksa.no_rkm_medis',
                    'reg_periksa.no_rawat',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.jam_reg',
                    'reg_periksa.kd_dokter',
                    'reg_periksa.status_lanjut',
                    'reg_periksa.stts',
                    'reg_periksa.kd_poli',
                    'reg_periksa.kd_pj',
                    'pasien.nm_pasien',
                    'pasien.no_ktp as ktp_pasien',
                    'pasien.tgl_lahir',
                    'pasien.jk',
                    'pegawai.no_ktp as ktp_dokter',
                    'pegawai.nama as nama_dokter',
                    'poliklinik.nm_poli'
                )
                ->where('reg_periksa.status_lanjut', 'Ralan')
                ->where('reg_periksa.stts', 'Sudah')
                ->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
                ->orWhere('reg_periksa.tgl_registrasi', $kemarin)
                ->get();
        } else {
            $pasien_tanggal = new Carbon($request->get('tanggal'));

            $data = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
                ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
                ->select(
                    'reg_periksa.no_rkm_medis',
                    'reg_periksa.no_rawat',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.jam_reg',
                    'reg_periksa.kd_dokter',
                    'reg_periksa.status_lanjut',
                    'reg_periksa.stts',
                    'reg_periksa.kd_poli',
                    'reg_periksa.kd_pj',
                    'pasien.nm_pasien',
                    'pasien.no_ktp as ktp_pasien',
                    'pasien.tgl_lahir',
                    'pasien.jk',
                    'pegawai.no_ktp as ktp_dokter',
                    'pegawai.nama as nama_dokter',
                    'poliklinik.nm_poli'
                )
                ->where('reg_periksa.status_lanjut', 'Ralan')
                ->where('reg_periksa.stts', 'Sudah')
                ->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
                ->get();
        }
        // $pasien_tanggal = Carbon::now()->format('Y-m-d');
        $idRS = env('IDRS');
        $antrian = 0;

        foreach ($data as $key => $dataPengunjung) {
            if ($antrian == 5) {
                goto Selesai;
            }
            $idPasien = SatuSehatController::patientSehat($dataPengunjung->ktp_pasien);
            $idDokter = SatuSehatController::practitioner($dataPengunjung->ktp_dokter);
            // $idPasien = "P02478375538";
            // $idDokter = "10009880728";
            $idLokasi = SatuSehatController::getIdPoli($dataPengunjung->kd_poli);

            $getResep = SatuSehatController::getResepObat($dataPengunjung->no_rawat);
            $idCounter = SatuSehatController::getEncounterId($dataPengunjung->no_rawat);
            //Cek List Obat di Response Medication apakah sudah ada
            $cekResponse = ResponseMedicationSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
            // dd($cekResponse);
            // if (!empty($getResep) && !empty($cekResponse)) {
            //     dd($getResep, $cekResponse);
            // }
            if ((!empty($getResep)) && (!empty($idCounter)) && (empty($cekResponse))) {
                //Resep Obat Jadi di table resep_dokter
                $listObat = SatuSehatController::getListObat($getResep->no_resep);
                //Resep Obat Racikan tabel resep_dokter_racikan
                $listObatRacik = SatuSehatController::getListObatRacik($getResep->no_resep);
                $detailRacikan = SatuSehatController::getDetailRacikan($getResep->no_resep);
                $noresep = $noUrutResep = '';

                // dd($listObat);
                //Obat Jadi di Kirim dl
                if ($listObat->count() > 0) {
                    foreach ($listObat as $index => $dataListObat) {
                        // dd($dataListObat);
                        $noUrutResep = $index + 1;
                        $noresep = $dataListObat->no_resep . '-' . $noUrutResep;
                        // dd($noresep);

                        //Get Id Obat
                        $mappingObat = SatuSehatController::getIdObat($dataListObat->kode_brng);
                        // if (!empty($cekResponse) && !empty($mappingObat)) {
                        //     dd($getResep, $dataListObat, $mappingObat);
                        // }

                        if ((empty($cekResponse)) && (!empty($mappingObat))) {
                            $medication1 = [
                                "resourceType" => "Medication",
                                "meta" => [
                                    "profile" => [
                                        "https://fhir.kemkes.go.id/r4/StructureDefinition/Medication"
                                    ]
                                ],
                                "identifier" => [
                                    [
                                        "system" => "http://sys-ids.kemkes.go.id/medication/$idRS",
                                        "use" => "official",
                                        "value" => "$dataListObat->no_resep"
                                    ]
                                ],
                                "code" => [
                                    "coding" => [ //Iki dinggo mapping obate
                                        [
                                            "system" => "http://sys-ids.kemkes.go.id/kfa",
                                            "code" => "$mappingObat->id_ihs",
                                            "display" => "$dataListObat->nama_brng"
                                        ]
                                    ]
                                ],
                                "status" => "active",
                                // "manufacturer" => [ //optional
                                //     "reference" => "Organization/900001"
                                // ],
                                "form" => [
                                    "coding" => [ //Iki dinggo medication form tipe obate opo
                                        [
                                            "system" => "$mappingObat->form_coding_system",
                                            "code" => "$mappingObat->kode_medication",
                                            "display" => "$mappingObat->form_display"
                                        ]
                                    ]
                                ],
                                // "ingredient" => [ //untuk racikan yang wajib
                                //     [
                                //         "itemCodeableConcept" => [
                                //             "coding" => [
                                //                 [
                                //                     "system" => "http://sys-ids.kemkes.go.id/kfa",
                                //                     "code" => "91000330",
                                //                     "display" => "Rifampin"
                                //                 ]
                                //             ]
                                //         ],
                                //         "isActive" => true,
                                //         "strength" => [
                                //             "numerator" => [
                                //                 "value" => 150,
                                //                 "system" => "http://unitsofmeasure.org",
                                //                 "code" => "mg"
                                //             ],
                                //             "denominator" => [
                                //                 "value" => 1,
                                //                 "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                                //                 "code" => "TAB"
                                //             ]
                                //         ]
                                //     ],
                                //     [
                                //         "itemCodeableConcept" => [
                                //             "coding" => [
                                //                 [
                                //                     "system" => "http://sys-ids.kemkes.go.id/kfa",
                                //                     "code" => "91000328",
                                //                     "display" => "Isoniazid"
                                //                 ]
                                //             ]
                                //         ],
                                //         "isActive" => true,
                                //         "strength" => [
                                //             "numerator" => [
                                //                 "value" => 75,
                                //                 "system" => "http://unitsofmeasure.org",
                                //                 "code" => "mg"
                                //             ],
                                //             "denominator" => [
                                //                 "value" => 1,
                                //                 "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                                //                 "code" => "TAB"
                                //             ]
                                //         ]
                                //     ],
                                //     [
                                //         "itemCodeableConcept" => [
                                //             "coding" => [
                                //                 [
                                //                     "system" => "http://sys-ids.kemkes.go.id/kfa",
                                //                     "code" => "91000329",
                                //                     "display" => "Pyrazinamide"
                                //                 ]
                                //             ]
                                //         ],
                                //         "isActive" => true,
                                //         "strength" => [
                                //             "numerator" => [
                                //                 "value" => 400,
                                //                 "system" => "http://unitsofmeasure.org",
                                //                 "code" => "mg"
                                //             ],
                                //             "denominator" => [
                                //                 "value" => 1,
                                //                 "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                                //                 "code" => "TAB"
                                //             ]
                                //         ]
                                //     ],
                                //     [
                                //         "itemCodeableConcept" => [
                                //             "coding" => [
                                //                 [
                                //                     "system" => "http://sys-ids.kemkes.go.id/kfa",
                                //                     "code" => "91000288",
                                //                     "display" => "Ethambutol"
                                //                 ]
                                //             ]
                                //         ],
                                //         "isActive" => true,
                                //         "strength" => [
                                //             "numerator" => [
                                //                 "value" => 275,
                                //                 "system" => "http://unitsofmeasure.org",
                                //                 "code" => "mg"
                                //             ],
                                //             "denominator" => [
                                //                 "value" => 1,
                                //                 "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                                //                 "code" => "TAB"
                                //             ]
                                //         ]
                                //     ]
                                // ],
                                "extension" => [ //harus bos
                                    [
                                        "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/MedicationType",
                                        "valueCodeableConcept" => [
                                            "coding" => [
                                                [
                                                    "system" => "http://terminology.kemkes.go.id/CodeSystem/medication-type",
                                                    "code" => "NC",
                                                    "display" => "Non-compound"
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ];

                            // dd($medication1);

                            //Kirim/Create Medication
                            SatuSehatController::getTokenSehat();
                            $access_token = Session::get('tokenSatuSehat');
                            // dd($access_token);
                            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                            try {
                                $response = $client->request('POST', 'fhir-r4/v1/Medication', [
                                    'headers' => [
                                        'Authorization' => "Bearer {$access_token}"
                                    ],
                                    'json' => $medication1
                                ]);
                                // dd('terkirim');
                            } catch (BadResponseException $e) {
                                if ($e->hasResponse()) {
                                    $response = $e->getResponse();
                                    $test = json_decode($response->getBody());
                                    $errorCode = (array) $test;

                                    // dd($test, 'medication1');

                                    if (!empty($errorCode['issue'][0])) {
                                        $pesan = $errorCode['issue'][0]->details->text;

                                        $message = "Medication 1 error $pesan";

                                        Session::flash('error', $message);

                                        $cek = LogErrorSatuSehat::where('subject', 'Obat Medication1')
                                            ->where('keterangan', 'like', '%' . $dataPengunjung->no_rawat . '%')
                                            ->whereDate('created_at', Carbon::now())
                                            ->get();
                                        if ($cek->count() < 1) {
                                            $error = new LogErrorSatuSehat();
                                            $error->subject = 'Obat Medication1';
                                            $error->keterangan = $dataPengunjung->no_rawat . ' error kirim ' . $dataListObat->nama_brng . ' pesan ' . $pesan;
                                            $error->save();
                                        }

                                        // $dataLog = ResponseMedicationSatuSehat::whereDate('created_at', $pasien_tanggal)->get();
                                        // return view('satu_sehat.client_apotek', compact('dataLog'));
                                    } else {
                                        Session::flash('error', $errorCode['fault']->faultstring);

                                        $cek = LogErrorSatuSehat::where('subject', 'Obat Medication1')
                                            ->where('keterangan', 'like', '%' . $dataPengunjung->no_rawat . '%')
                                            ->whereDate('created_at', Carbon::now())
                                            ->get();
                                        if ($cek->count() < 1) {
                                            $error = new LogErrorSatuSehat();
                                            $error->subject = 'Obat Medication1';
                                            $error->keterangan = $dataPengunjung->no_rawat . ' error kirim ' . $dataListObat->nama_brng . ' pesan ' . $errorCode['fault']->faultstring;
                                            $error->save();
                                        }
                                        // $dataLog = ResponseMedicationSatuSehat::whereDate('created_at', $pasien_tanggal)->get();
                                        // return view('satu_sehat.client_apotek', compact('dataLog'));
                                    }
                                    goto NextObatJadi;
                                }
                            }

                            // dd($response);

                            $data = json_decode($response->getBody());

                            if (!empty($data->id) && $data->resourceType == "Medication") {

                                $simpan = new ResponseMedicationSatuSehat();
                                $simpan->noRawat = $dataPengunjung->no_rawat;
                                $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                                $simpan->noResep = $noresep;
                                $simpan->medication1 = $data->id;
                                $simpan->save();

                                //Off ini dulu buat pakai langsung dari inisialisasi idMedication1 saja
                                // $response1 = SatuSehatController::getMedicationId($noresep);
                                $idMedication1 = $data->id;
                                //Waktu Registrasi
                                $waktuRegis = Carbon::parse($dataPengunjung->tgl_registrasi . ' ' . $dataPengunjung->jam_reg);
                                $formatWaktuRegis = $waktuRegis->setTimezone('UTC')->toW3cString();
                                //Waktu Request Obat
                                $waktuRequest = Carbon::parse($getResep->tgl_permintaan . ' ' . $getResep->jam_permintaan);
                                $formatWaktuRequest = $waktuRequest->setTimezone('UTC')->toW3cString();
                                //Waktu Pemberian
                                $waktuPenyerahan = Carbon::parse($getResep->tgl_penyerahan . ' ' . $getResep->jam_penyerahan);
                                $formatWaktuPenyerahan = $waktuPenyerahan->setTimezone('UTC')->toW3cString();

                                $medicationRequest = [
                                    "resourceType" => "MedicationRequest",
                                    "identifier" => [
                                        [
                                            "system" => "http://sys-ids.kemkes.go.id/prescription/$idRS",
                                            "use" => "official",
                                            "value" => "$dataListObat->no_resep"
                                        ],
                                        [
                                            "system" => "http://sys-ids.kemkes.go.id/prescription-item/$idRS",
                                            "use" => "official",
                                            "value" => "$noresep"
                                        ]
                                    ],
                                    "status" => "completed",
                                    "intent" => "order",
                                    "category" => [
                                        [
                                            "coding" => [ //tetap saja karena buat rajal ya code dibawah
                                                [
                                                    "system" => "http://terminology.hl7.org/CodeSystem/medicationrequest-category",
                                                    "code" => "outpatient",
                                                    "display" => "Outpatient"
                                                ]
                                            ]
                                        ]
                                    ],
                                    "priority" => "routine",
                                    "medicationReference" => [
                                        "reference" => "Medication/$idMedication1",
                                        "display" => "$dataListObat->nama_brng"
                                    ],
                                    "subject" => [
                                        "reference" => "Patient/$idPasien",
                                        "display" => "$dataPengunjung->nm_pasien"
                                    ],
                                    "encounter" => [
                                        "reference" => "Encounter/$idCounter->encounter_id"
                                    ],
                                    "authoredOn" => "$formatWaktuRegis",
                                    "requester" => [
                                        "reference" => "Practitioner/$idDokter",
                                        "display" => "$dataPengunjung->nama_dokter"
                                    ],
                                    // "reasonCode" => [ //Optional
                                    //     [
                                    //         "coding" => [
                                    //             [
                                    //                 "system" => "http://hl7.org/fhir/sid/icd-10",
                                    //                 "code" => "A15.0", //diagnosa pasien icd 10
                                    //                 "display" => "Tuberculosis of lung, confirmed by sputum microscopy with or without culture"
                                    //             ]
                                    //         ]
                                    //     ]
                                    // ],
                                    // "courseOfTherapyType" => [ //optional
                                    //     "coding" => [
                                    //         [
                                    //             "system" => "http://terminology.hl7.org/CodeSystem/medicationrequest-course-of-therapy",
                                    //             "code" => "continuous",
                                    //             "display" => "Continuing long term therapy"
                                    //         ]
                                    //     ]
                                    // ],
                                    "dosageInstruction" => [
                                        [
                                            "sequence" => 1,
                                            "text" => "$dataListObat->aturan_pakai", //optional
                                            // "additionalInstruction" => [ //optional
                                            //     [
                                            //         "text" => "Diminum setiap hari"
                                            //     ]
                                            // ],
                                            "patientInstruction" => "$dataListObat->aturan_pakai", //opsional
                                            "timing" => [ //wajib dan ruwet
                                                "repeat" => [
                                                    "frequency" => 1,
                                                    "period" => 1,
                                                    "periodUnit" => "wk"
                                                ]
                                            ],
                                            "route" => [ //wajib
                                                "coding" => [
                                                    [
                                                        "system" => "$mappingObat->route_system",
                                                        "code" => "$mappingObat->kode_route",
                                                        "display" => "$mappingObat->route_display"
                                                    ]
                                                ]
                                            ],
                                            "doseAndRate" => [ //wajib
                                                [
                                                    "type" => [
                                                        "coding" => [
                                                            [
                                                                "system" => "http://terminology.hl7.org/CodeSystem/dose-rate-type",
                                                                "code" => "ordered",
                                                                "display" => "Ordered"
                                                            ]
                                                        ]
                                                    ],
                                                    "doseQuantity" => [
                                                        "value" => $dataListObat->jml, //perlu dikoreksi
                                                        "unit" => "$mappingObat->kode_ingredient",
                                                        "system" => "$mappingObat->ingredient_system",
                                                        "code" => "$mappingObat->kode_ingredient"
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    "dispenseRequest" => [
                                        // "dispenseInterval" => [ //optional
                                        //     "value" => 1,
                                        //     "unit" => "days",
                                        //     "system" => "http://unitsofmeasure.org",
                                        //     "code" => "d"
                                        // ],
                                        "validityPeriod" => [ //optional Waktu Peresepan
                                            "start" => "$formatWaktuRequest",
                                            "end" => "$formatWaktuPenyerahan"
                                        ],
                                        "numberOfRepeatsAllowed" => 0, //optional
                                        "quantity" => [ //wajib
                                            "value" => $dataListObat->jml,
                                            "unit" => "$mappingObat->kode_ingredient",
                                            "system" => "$mappingObat->ingredient_system",
                                            "code" => "$mappingObat->kode_ingredient"
                                        ],
                                        // "expectedSupplyDuration" => [ //optional
                                        //     "value" => 30,
                                        //     "unit" => "days",
                                        //     "system" => "http://unitsofmeasure.org",
                                        //     "code" => "d"
                                        // ],
                                        "performer" => [ //optional
                                            "reference" => "Organization/$idRS"
                                        ]
                                    ]
                                ];

                                //Kirim/Create Medication Request
                                SatuSehatController::getTokenSehat();
                                $access_token = Session::get('tokenSatuSehat');
                                // dd($access_token);
                                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                                try {
                                    $response = $client->request('POST', 'fhir-r4/v1/MedicationRequest', [
                                        'headers' => [
                                            'Authorization' => "Bearer {$access_token}"
                                        ],
                                        'json' => $medicationRequest
                                    ]);
                                } catch (BadResponseException $e) {
                                    if ($e->hasResponse()) {
                                        $response = $e->getResponse();
                                        $test = json_decode($response->getBody());

                                        dd($test, 'medicationRequest');
                                        $errorCode = (array) $test;
                                        if (!empty($errorCode['issue'][0])) {
                                            $pesan = $errorCode['issue'][0]->details->text;

                                            $message = "Medication Request error $pesan";

                                            Session::flash('error', $message);

                                            // $dataLog = ResponseMedicationSatuSehat::whereDate('created_at', $pasien_tanggal)->get();
                                            // return view('satu_sehat.client_apotek', compact('dataLog'));
                                        } else {
                                            Session::flash('error', $errorCode['fault']->faultstring);

                                            // $dataLog = ResponseMedicationSatuSehat::whereDate('created_at', $pasien_tanggal)->get();
                                            // return view('satu_sehat.client_apotek', compact('dataLog'));
                                        }
                                        goto NextObatJadi;
                                    }

                                    // $dataLog = ResponseMedicationSatuSehat::whereDate('created_at', $pasien_tanggal)->get();
                                    // return view('satu_sehat.client_apotek', compact('dataLog'));
                                }

                                // dd($response);
                                $data = json_decode($response->getBody());

                                //Update data di table respone medication request
                                $update = ResponseMedicationSatuSehat::where('medication1', $idMedication1)->first();
                                $update->medicationRequest = $data->id;
                                $update->save();

                                $idMedicationRequest = $data->id;
                                if (!empty($data->id) && $data->resourceType == "MedicationRequest") {
                                    //Langsung kirim medication 1 sebagai medication2
                                    //Wis tak perbaiki saiki gowo batch ama tanggal expire
                                    $medication2 = [
                                        "resourceType" => "Medication",
                                        "meta" => [
                                            "profile" => [
                                                "https://fhir.kemkes.go.id/r4/StructureDefinition/Medication"
                                            ]
                                        ],
                                        "identifier" => [
                                            [
                                                "system" => "http://sys-ids.kemkes.go.id/medication/$idRS",
                                                "use" => "official",
                                                "value" => "$dataListObat->no_resep"
                                            ]
                                        ],
                                        "code" => [
                                            "coding" => [ //Iki dinggo mapping obate
                                                [
                                                    "system" => "http://sys-ids.kemkes.go.id/kfa",
                                                    "code" => "$mappingObat->id_ihs",
                                                    "display" => "$dataListObat->nama_brng"
                                                ]
                                            ]
                                        ],
                                        "status" => "active",
                                        // "manufacturer" => [ //optional
                                        //     "reference" => "Organization/900001"
                                        // ],
                                        "form" => [
                                            "coding" => [ //Iki dinggo medication form tipe obate opo
                                                [
                                                    "system" => "$mappingObat->form_coding_system",
                                                    "code" => "$mappingObat->kode_medication",
                                                    "display" => "$mappingObat->form_display"
                                                ]
                                            ]
                                        ],
                                        // "ingredient" => [ //untuk racikan yang wajib
                                        //     [
                                        //         "itemCodeableConcept" => [
                                        //             "coding" => [
                                        //                 [
                                        //                     "system" => "http://sys-ids.kemkes.go.id/kfa",
                                        //                     "code" => "91000330",
                                        //                     "display" => "Rifampin"
                                        //                 ]
                                        //             ]
                                        //         ],
                                        //         "isActive" => true,
                                        //         "strength" => [
                                        //             "numerator" => [
                                        //                 "value" => 150,
                                        //                 "system" => "http://unitsofmeasure.org",
                                        //                 "code" => "mg"
                                        //             ],
                                        //             "denominator" => [
                                        //                 "value" => 1,
                                        //                 "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                                        //                 "code" => "TAB"
                                        //             ]
                                        //         ]
                                        //     ],
                                        //     [
                                        //         "itemCodeableConcept" => [
                                        //             "coding" => [
                                        //                 [
                                        //                     "system" => "http://sys-ids.kemkes.go.id/kfa",
                                        //                     "code" => "91000328",
                                        //                     "display" => "Isoniazid"
                                        //                 ]
                                        //             ]
                                        //         ],
                                        //         "isActive" => true,
                                        //         "strength" => [
                                        //             "numerator" => [
                                        //                 "value" => 75,
                                        //                 "system" => "http://unitsofmeasure.org",
                                        //                 "code" => "mg"
                                        //             ],
                                        //             "denominator" => [
                                        //                 "value" => 1,
                                        //                 "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                                        //                 "code" => "TAB"
                                        //             ]
                                        //         ]
                                        //     ],
                                        //     [
                                        //         "itemCodeableConcept" => [
                                        //             "coding" => [
                                        //                 [
                                        //                     "system" => "http://sys-ids.kemkes.go.id/kfa",
                                        //                     "code" => "91000329",
                                        //                     "display" => "Pyrazinamide"
                                        //                 ]
                                        //             ]
                                        //         ],
                                        //         "isActive" => true,
                                        //         "strength" => [
                                        //             "numerator" => [
                                        //                 "value" => 400,
                                        //                 "system" => "http://unitsofmeasure.org",
                                        //                 "code" => "mg"
                                        //             ],
                                        //             "denominator" => [
                                        //                 "value" => 1,
                                        //                 "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                                        //                 "code" => "TAB"
                                        //             ]
                                        //         ]
                                        //     ],
                                        //     [
                                        //         "itemCodeableConcept" => [
                                        //             "coding" => [
                                        //                 [
                                        //                     "system" => "http://sys-ids.kemkes.go.id/kfa",
                                        //                     "code" => "91000288",
                                        //                     "display" => "Ethambutol"
                                        //                 ]
                                        //             ]
                                        //         ],
                                        //         "isActive" => true,
                                        //         "strength" => [
                                        //             "numerator" => [
                                        //                 "value" => 275,
                                        //                 "system" => "http://unitsofmeasure.org",
                                        //                 "code" => "mg"
                                        //             ],
                                        //             "denominator" => [
                                        //                 "value" => 1,
                                        //                 "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                                        //                 "code" => "TAB"
                                        //             ]
                                        //         ]
                                        //     ]
                                        // ],
                                        "batch" => [
                                            "lotNumber" => "-",
                                            "expirationDate" => "$dataListObat->expire"
                                        ],
                                        "extension" => [ //harus bos
                                            [
                                                "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/MedicationType",
                                                "valueCodeableConcept" => [
                                                    "coding" => [
                                                        [
                                                            "system" => "http://terminology.kemkes.go.id/CodeSystem/medication-type",
                                                            "code" => "NC",
                                                            "display" => "Non-compound"
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ];
                                    //Kirim/Create Medication
                                    SatuSehatController::getTokenSehat();
                                    $access_token = Session::get('tokenSatuSehat');
                                    // dd($access_token);
                                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                                    try {
                                        $response = $client->request('POST', 'fhir-r4/v1/Medication', [
                                            'headers' => [
                                                'Authorization' => "Bearer {$access_token}"
                                            ],
                                            'json' => $medication2
                                        ]);
                                    } catch (BadResponseException $e) {
                                        if ($e->hasResponse()) {
                                            $response = $e->getResponse();
                                            $test = json_decode($response->getBody());
                                            $errorCode = (array) $test;

                                            dd($test, 'medication2');
                                            if (!empty($errorCode['issue'][0])) {
                                                $pesan = $errorCode['issue'][0]->details->text;

                                                $message = "Medication2 error $pesan";

                                                Session::flash('error', $message);

                                                // $dataLog = ResponseMedicationSatuSehat::whereDate('created_at', $pasien_tanggal)->get();
                                                // return view('satu_sehat.client_apotek', compact('dataLog'));
                                            } else {
                                                Session::flash('error', $errorCode['fault']->faultstring);

                                                // $dataLog = ResponseMedicationSatuSehat::whereDate('created_at', $pasien_tanggal)->get();
                                                // return view('satu_sehat.client_apotek', compact('dataLog'));
                                            }
                                            goto NextObatJadi;
                                        }

                                        // $dataLog = ResponseMedicationSatuSehat::whereDate('created_at', $pasien_tanggal)->get();
                                        // return view('satu_sehat.client_apotek', compact('dataLog'));
                                    }

                                    $data = json_decode($response->getBody());

                                    //Update data di table respone medication2
                                    $update = ResponseMedicationSatuSehat::where('medication1', $idMedication1)->first();
                                    $update->medication2 = $data->id;
                                    $update->save();

                                    //variabel dinamis
                                    $idMedication2 = $data->id;
                                    // $apoteker = SatuSehatController::practitioner('3309090909870004');
                                    $apoteker = "10007445367"; //Pak Wahid
                                    $lokasiApotek = '5ca46bfc-9c51-4ed5-b160-bbabd1a50163';
                                    //Waktu
                                    $waktuAwal = $getResep->tgl_permintaan . ' ' . $getResep->jam_permintaan;
                                    $waktu_mulai = new Carbon($waktuAwal);
                                    // $formatWaktuMulai = Carbon::parse($waktuAwal)->format('Y-m-d') . 'T' . Carbon::parse($waktuAwal)->format('H:i:s+07:00');
                                    $formatWaktuMulai = $waktu_mulai->setTimezone('UTC')->toW3cString();
                                    $waktuSelesai = $getResep->tgl_penyerahan . ' ' . $getResep->jam_penyerahan;
                                    $waktu_selesai = new Carbon($waktuSelesai);
                                    // $formatWaktuSelesai = Carbon::parse($waktuSelesai)->format('Y-m-d') . 'T' . Carbon::parse($waktuSelesai)->format('H:i:s+07:00');
                                    $formatWaktuSelesai = $waktu_selesai->setTimezone('UTC')->toW3cString();
                                    //Cek Obat yang diberikan
                                    $obatPasien = SatuSehatController::obatDiberikan($getResep->no_rawat, $dataListObat->kode_brng);

                                    $jmlObatPasien = $obatPasien[0];
                                    if ($jmlObatPasien == null) {
                                        $jmlObatPasien = 0;
                                    } else {
                                        $jmlObatPasien = $jmlObatPasien->jml;
                                    }
                                    $aturanObatPasien = $obatPasien[1];
                                    if ($aturanObatPasien == null) {
                                        $aturanObatPasien = '';
                                    } else {
                                        $aturanObatPasien = $aturanObatPasien->aturan;
                                    }

                                    if (!empty($data->id) && $data->resourceType == "Medication") {
                                        $medicationDispense = [
                                            "resourceType" => "MedicationDispense",
                                            "identifier" => [
                                                [
                                                    "system" => "http://sys-ids.kemkes.go.id/prescription/$idRS",
                                                    "use" => "official",
                                                    "value" => "$dataListObat->no_resep"
                                                ],
                                                [
                                                    "system" => "http://sys-ids.kemkes.go.id/prescription-item/$idRS",
                                                    "use" => "official",
                                                    "value" => "$noresep"
                                                ]
                                            ],
                                            "status" => "completed", //wajib
                                            "category" => [ //wajib
                                                "coding" => [
                                                    [
                                                        "system" => "http://terminology.hl7.org/fhir/CodeSystem/medicationdispense-category",
                                                        "code" => "outpatient",
                                                        "display" => "Outpatient"
                                                    ]
                                                ]
                                            ],
                                            "medicationReference" => [
                                                "reference" => "Medication/$idMedication2", //wajib
                                                "display" => "$dataListObat->nama_brng" //free text
                                            ],
                                            "subject" => [ //wajib
                                                "reference" => "Patient/$idPasien",
                                                "display" => "$dataPengunjung->nm_pasien"
                                            ],
                                            "context" => [ //wajib
                                                "reference" => "Encounter/$idCounter->encounter_id"
                                            ],
                                            "performer" => [ //optional
                                                [
                                                    "actor" => [
                                                        "reference" => "Practitioner/$apoteker",
                                                        "display" => "WAHID BUDI NUGROHO, S.Farm, Apt"
                                                    ]
                                                ]
                                            ],
                                            "location" => [ //wajib
                                                "reference" => "Location/$lokasiApotek",
                                                "display" => "Apotek RSUP Surakarta"
                                            ],
                                            "authorizingPrescription" => [
                                                [
                                                    "reference" => "MedicationRequest/$idMedicationRequest"
                                                ]
                                            ],
                                            "quantity" => [ //wajib
                                                "system" => "$mappingObat->ingredient_system",
                                                "code" => "$mappingObat->kode_ingredient",
                                                "value" => $jmlObatPasien
                                            ],
                                            // "daysSupply" => [ //optional
                                            //     "value" => 30,
                                            //     "unit" => "Day",
                                            //     "system" => "http://unitsofmeasure.org",
                                            //     "code" => "d"
                                            // ],
                                            "whenPrepared" => "$formatWaktuMulai", //optional
                                            "whenHandedOver" => "$formatWaktuSelesai", //optional
                                            "dosageInstruction" => [
                                                [
                                                    "sequence" => 1, //wajib
                                                    "text" => "$aturanObatPasien",
                                                    "timing" => [
                                                        "repeat" => [
                                                            "frequency" => 1,
                                                            "period" => 1,
                                                            "periodUnit" => "wk" //ben ambigu rpp sek dinggo sek text
                                                        ]
                                                    ],
                                                    "doseAndRate" => [
                                                        [
                                                            "type" => [
                                                                "coding" => [
                                                                    [
                                                                        "system" => "http://terminology.hl7.org/CodeSystem/dose-rate-type",
                                                                        "code" => "ordered",
                                                                        "display" => "Ordered"
                                                                    ]
                                                                ]
                                                            ],
                                                            "doseQuantity" => [ //wajib
                                                                "value" => $jmlObatPasien, //ndadak mecah iki dosise piro sekali minum
                                                                "unit" => "$mappingObat->kode_ingredient",
                                                                "system" => "$mappingObat->ingredient_system",
                                                                "code" => "$mappingObat->kode_ingredient"
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ];
                                        // dd($medicationDispense);
                                        SatuSehatController::getTokenSehat();
                                        $access_token = Session::get('tokenSatuSehat');
                                        // dd($access_token);
                                        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                                        try {
                                            $response = $client->request('POST', 'fhir-r4/v1/MedicationDispense', [
                                                'headers' => [
                                                    'Authorization' => "Bearer {$access_token}"
                                                ],
                                                'json' => $medicationDispense
                                            ]);
                                        } catch (BadResponseException $e) {
                                            if ($e->hasResponse()) {
                                                $response = $e->getResponse();
                                                $test = json_decode($response->getBody());
                                                $errorCode = (array) $test;

                                                dd($test, 'medication dispance', $medicationDispense);
                                                if (!empty($errorCode['issue'][0])) {
                                                    $pesan = $errorCode['issue'][0]->details->text;

                                                    $message = "Medication Dispance error $pesan";

                                                    Session::flash('error', $message);

                                                    // $dataLog = ResponseMedicationSatuSehat::whereDate('created_at', $pasien_tanggal)->get();
                                                    // return view('satu_sehat.client_apotek', compact('dataLog'));
                                                } else {
                                                    Session::flash('error', $errorCode['fault']->faultstring);

                                                    // $dataLog = ResponseMedicationSatuSehat::whereDate('created_at', $pasien_tanggal)->get();
                                                    // return view('satu_sehat.client_apotek', compact('dataLog'));
                                                }
                                            }

                                            goto NextObatJadi;
                                            // $dataLog = ResponseMedicationSatuSehat::whereDate('created_at', $pasien_tanggal)->get();
                                            // return view('satu_sehat.client_apotek', compact('dataLog'));
                                        }

                                        $data = json_decode($response->getBody());

                                        //Update data di table respone medication request
                                        $update = ResponseMedicationSatuSehat::where('medication1', $idMedication1)->first();
                                        $update->medicationDispence = $data->id;
                                        $update->save();
                                    }
                                }
                            }
                            NextObatJadi:
                        }
                    }
                }

                //Obat racikan dikirim
                // if (($listObatRacik->count() > 0) && ($detailRacikan->count() > 0)) {
                //     foreach ($listObatRacik as $resepRacikan) {
                //         $formRacik = SatuSehatController::getMedicationForm($resepRacikan->kd_racik);
                //         $listIngridient = array();
                //         // for ($i = 0; $i < $detailRacikan->count(); $i++) {
                //         foreach ($detailRacikan as $ingridientRacikan) {
                //             // dd($ingridientRacikan);
                //             $itemObat = SatuSehatController::getIdObat($ingridientRacikan->kode_brng);
                //             // dd($resepRacikan, $ingridientRacikan, $itemObat);
                //             if (!empty($itemObat)) {
                //                 if ($formRacik->kd_ingredient != null) {
                //                     $jenis = DB::connection('mysqlkhanza')->table('fhir_master_ingredient')
                //                         ->select(
                //                             'fhir_master_ingredient.kd_ingredient',
                //                             'fhir_master_ingredient.display',
                //                             'fhir_master_ingredient.system'
                //                         )
                //                         ->where('fhir_master_ingredient.kd_ingredient', $formRacik->kd_ingredient)
                //                         ->first();

                //                     $ingridient = [
                //                         "itemCodeableConcept" => [
                //                             "coding" => [
                //                                 [
                //                                     "system" => "http://sys-ids.kemkes.go.id/kfa",
                //                                     "code" => "$itemObat->id_ihs",
                //                                     "display" => "$ingridientRacikan->nama_brng"
                //                                 ]
                //                             ]
                //                         ],
                //                         "isActive" => true,
                //                         "strength" => [
                //                             "numerator" => [
                //                                 "value" => $ingridientRacikan->jml,
                //                                 "system" => "$itemObat->ingredient_system",
                //                                 "code" => "$itemObat->kode_ingredient"
                //                             ],
                //                             "denominator" => [
                //                                 "value" => $resepRacikan->jml_dr,
                //                                 "system" => "$jenis->system",
                //                                 "code" => "$jenis->kd_ingredient"
                //                             ]
                //                         ]
                //                     ];
                //                 } else {
                //                     $jenis = DB::connection('mysqlkhanza')->table('fhir_master_ucum')
                //                         ->select(
                //                             'fhir_master_ucum.kd_ucum',
                //                             'fhir_master_ucum.name',
                //                             'fhir_master_ucum.system'
                //                         )
                //                         ->where('fhir_master_ucum.kd_ucum', $formRacik->kd_ucum)
                //                         ->first();

                //                     $ingridient = [
                //                         "itemCodeableConcept" => [
                //                             "coding" => [
                //                                 [
                //                                     "system" => "http://sys-ids.kemkes.go.id/kfa",
                //                                     "code" => "$itemObat->id_ihs",
                //                                     "display" => "$ingridientRacikan->nama_brng"
                //                                 ]
                //                             ]
                //                         ],
                //                         "isActive" => true,
                //                         "strength" => [
                //                             "numerator" => [
                //                                 "value" => $ingridientRacikan->jml,
                //                                 "system" => "$itemObat->ucum_system",
                //                                 "code" => "$itemObat->kode_ucum"
                //                             ],
                //                             "denominator" => [
                //                                 "value" => $resepRacikan->jml_dr,
                //                                 "system" => "$jenis->system",
                //                                 "code" => "$jenis->kd_ucum"
                //                             ]
                //                         ]
                //                     ];
                //                 }

                //                 array_push($listIngridient, $ingridient);
                //             }
                //         }

                //         $medicationRacik1 = [
                //             "resourceType" => "Medication",
                //             "meta" => [
                //                 "profile" => [
                //                     "https://fhir.kemkes.go.id/r4/StructureDefinition/Medication"
                //                 ]
                //             ],
                //             "status" => "active",
                //             "form" => [
                //                 "coding" => [
                //                     [
                //                         "system" => "$formRacik->coding_system",
                //                         "code" => "$formRacik->kode_medication",
                //                         "display" => "$formRacik->display"
                //                     ]
                //                 ]
                //             ],
                //             "ingredient" => $listIngridient,
                //             "extension" => [
                //                 [
                //                     "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/MedicationType",
                //                     "valueCodeableConcept" => [
                //                         "coding" => [
                //                             [
                //                                 "system" => "https://terminology.kemkes.go.id/CodeSystem/medication-type",
                //                                 "code" => "EP",
                //                                 "display" => "Divide into equal parts"
                //                             ]
                //                         ]
                //                     ]
                //                 ]
                //             ]
                //         ];

                //         //Kirim/Create Medication
                //         SatuSehatController::getTokenSehat();
                //         $access_token = Session::get('tokenSatuSehat');
                //         // dd($access_token);
                //         $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                //         try {
                //             $response = $client->request('POST', 'fhir-r4/v1/Medication', [
                //                 'headers' => [
                //                     'Authorization' => "Bearer {$access_token}"
                //                 ],
                //                 'json' => $medicationRacik1
                //             ]);
                //         } catch (BadResponseException $e) {
                //             if ($e->hasResponse()) {
                //                 $response = $e->getResponse();
                //                 $test = json_decode($response->getBody());
                //                 $errorCode = (array) $test;
                //                 dd($test);

                //                 $pesan = $errorCode['issue'][0]->details->text;

                //                 $message = "Medication Racik 1 error $pesan";

                //                 Session::flash('error', $message);

                //                 goto SkipObatRacik;

                //                 $dataLog = ResponseMedicationSatuSehat::all();

                //                 // dd($dataLog);

                //                 return view('satu_sehat.client_apotek', compact('dataLog'));
                //             }

                //             $dataLog = ResponseMedicationSatuSehat::all();

                //             // dd($dataLog);

                //             return view('satu_sehat.client_apotek', compact('dataLog'));
                //         }

                //         // dd($response);

                //         $data = json_decode($response->getBody());

                //         // dd($medicationRacik1, $data, $noresep, $noUrutResep);
                //         if (!empty($data->id) && $data->resourceType == "Medication") {

                //             $simpan = new ResponseMedicationSatuSehat();
                //             $simpan->noRawat = $dataPengunjung->no_rawat;
                //             $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                //             $simpan->noResep = $resepRacikan->no_resep . '-' . ++$noUrutResep;
                //             $simpan->medication1 = $data->id;
                //             $simpan->save();

                //             $idMedicationRacik1 = $data->id;

                //             $routeRacik = SatuSehatController::getRouteRacik($resepRacikan->kd_racik);

                //             $medicationRequestRacik = [
                //                 "resourceType" => "MedicationRequest",
                //                 "identifier" => [
                //                     [
                //                         "system" => "http://sys-ids.kemkes.go.id/prescription/$idRS",
                //                         "use" => "official",
                //                         "value" => "$resepRacikan->no_resep"
                //                     ],
                //                     [
                //                         "system" => "http://sys-ids.kemkes.go.id/prescription-item/$idRS",
                //                         "use" => "official",
                //                         "value" => $resepRacikan->no_resep . "-" . $noUrutResep
                //                     ]
                //                 ],
                //                 "status" => "completed",
                //                 "intent" => "order",
                //                 "category" => [
                //                     [
                //                         "coding" => [
                //                             [
                //                                 "system" => "http://terminology.hl7.org/CodeSystem/medicationrequest-category",
                //                                 "code" => "outpatient",
                //                                 "display" => "Outpatient"
                //                             ]
                //                         ]
                //                     ]
                //                 ],
                //                 "priority" => "routine",
                //                 "medicationReference" => [
                //                     "reference" => "Medication/$idMedicationRacik1"
                //                 ],
                //                 "subject" => [
                //                     "reference" => "Patient/$idPasien",
                //                     "display" => "$dataPengunjung->nm_pasien"
                //                 ],
                //                 "encounter" => [
                //                     "reference" => "Encounter/$idCounter->encounter_id"
                //                 ],
                //                 "authoredOn" => "$dataPengunjung->tgl_registrasi",
                //                 "requester" => [
                //                     "reference" => "Practitioner/$idDokter",
                //                     "display" => "$dataPengunjung->nama_dokter"
                //                 ],
                //                 "dosageInstruction" => [
                //                     [
                //                         "sequence" => 1,
                //                         "text" => "$resepRacikan->aturan_pakai",
                //                         "patientInstruction" => "$resepRacikan->aturan_pakai",
                //                         "timing" => [
                //                             "repeat" => [
                //                                 "frequency" => 3,
                //                                 "period" => 1,
                //                                 "periodUnit" => "wk" //sengaja
                //                             ]
                //                         ],
                //                         "route" => [
                //                             "coding" => [
                //                                 [
                //                                     "system" => "$routeRacik->system",
                //                                     "code" => "$routeRacik->kd_route",
                //                                     "display" => "$routeRacik->display"
                //                                 ]
                //                             ]
                //                         ],
                //                         "doseAndRate" => [
                //                             [
                //                                 "type" => [
                //                                     "coding" => [
                //                                         [
                //                                             "system" => "http://terminology.hl7.org/CodeSystem/dose-rate-type",
                //                                             "code" => "ordered",
                //                                             "display" => "Ordered"
                //                                         ]

                //                                     ]
                //                                 ],
                //                                 "doseQuantity" => [
                //                                     "value" => $resepRacikan->jml_dr,
                //                                     "unit" => "$jenis->kd_ingredient",
                //                                     "system" => "$jenis->system",
                //                                     "code" => "$jenis->kd_ingredient"
                //                                 ]
                //                             ]
                //                         ]
                //                     ]
                //                 ],
                //                 "dispenseRequest" => [
                //                     // "dispenseInterval" => [
                //                     //     "value" => 1,
                //                     //     "unit" => "days",
                //                     //     "system" => "http://unitsofmeasure.org",
                //                     //     "code" => "d"
                //                     // ],
                //                     "validityPeriod" => [
                //                         "start" => "$getResep->tgl_permintaan",
                //                         "end" => "$getResep->tgl_penyerahan"
                //                     ],
                //                     "numberOfRepeatsAllowed" => 0,
                //                     "quantity" => [
                //                         "value" => $resepRacikan->jml_dr,
                //                         "unit" => "$jenis->kd_ingredient",
                //                         "system" => "$jenis->system",
                //                         "code" => "$jenis->kd_ingredient"
                //                     ]
                //                     // ,
                //                     // "expectedSupplyDuration" => [
                //                     //     "value" => 10,
                //                     //     "unit" => "days",
                //                     //     "system" => "http://unitsofmeasure.org",
                //                     //     "code" => "d"
                //                     // ]
                //                 ]
                //             ];

                //             //Kirim/Create Medication Request
                //             SatuSehatController::getTokenSehat();
                //             $access_token = Session::get('tokenSatuSehat');
                //             // dd($access_token);
                //             $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                //             try {
                //                 $response = $client->request('POST', 'fhir-r4/v1/MedicationRequest', [
                //                     'headers' => [
                //                         'Authorization' => "Bearer {$access_token}"
                //                     ],
                //                     'json' => $medicationRequestRacik
                //                 ]);
                //             } catch (BadResponseException $e) {
                //                 if ($e->hasResponse()) {
                //                     $response = $e->getResponse();
                //                     $test = json_decode($response->getBody());
                //                     $errorCode = (array) $test;
                //                     dd($test);

                //                     $pesan = $errorCode['issue'][0]->details->text;

                //                     $message = "Medication Request Racik error $pesan";

                //                     Session::flash('error', $message);

                //                     $dataLog = ResponseMedicationSatuSehat::all();

                //                     // dd($dataLog);

                //                     return view('satu_sehat.client_apotek', compact('dataLog'));
                //                 }

                //                 $dataLog = ResponseMedicationSatuSehat::all();

                //                 // dd($dataLog);

                //                 return view('satu_sehat.client_apotek', compact('dataLog'));
                //             }

                //             // dd($response);
                //             $data = json_decode($response->getBody());
                //             if (!empty($data->id) && $data->resourceType == "MedicationRequest") {
                //                 //Update data di table respone medication request
                //                 $update = ResponseMedicationSatuSehat::where('medication1', $idMedicationRacik1)->first();
                //                 $update->medicationRequest = $data->id;
                //                 $update->save();

                //                 $idMedicationRequestRacik = $data->id;

                //                 $medicationRacik2 = $medicationRacik1;

                //                 //Kirim/Create Medication 2
                //                 SatuSehatController::getTokenSehat();
                //                 $access_token = Session::get('tokenSatuSehat');
                //                 // dd($access_token);
                //                 $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                //                 try {
                //                     $response = $client->request('POST', 'fhir-r4/v1/Medication', [
                //                         'headers' => [
                //                             'Authorization' => "Bearer {$access_token}"
                //                         ],
                //                         'json' => $medicationRacik2
                //                     ]);
                //                 } catch (BadResponseException $e) {
                //                     if ($e->hasResponse()) {
                //                         $response = $e->getResponse();
                //                         $test = json_decode($response->getBody());
                //                         $errorCode = (array) $test;
                //                         dd($test);

                //                         $pesan = $errorCode['issue'][0]->details->text;

                //                         $message = "Medication Racik 2 error $pesan";

                //                         Session::flash('error', $message);

                //                         $dataLog = ResponseMedicationSatuSehat::all();

                //                         // dd($dataLog);

                //                         return view('satu_sehat.client_apotek', compact('dataLog'));
                //                     }

                //                     $dataLog = ResponseMedicationSatuSehat::all();

                //                     // dd($dataLog);

                //                     return view('satu_sehat.client_apotek', compact('dataLog'));
                //                 }

                //                 // dd($response);

                //                 $data = json_decode($response->getBody());

                //                 if (!empty($data->id) && $data->resourceType == "Medication") {
                //                     //Update data di table respone medication2
                //                     $update = ResponseMedicationSatuSehat::where('medication1', $idMedicationRacik1)->first();
                //                     $update->medication2 = $data->id;
                //                     $update->save();
                //                 }
                //             }
                //         }
                //         SkipObatRacik:
                //     }
                // }
                ++$antrian;
            }
            // }
            SkipData:
        }
        Selesai:

        $dataLog = ResponseMedicationSatuSehat::whereDate('created_at', $pasien_tanggal)
            ->orderBy('created_at', 'DESC')->get();

        // dd($dataLog);

        return view('satu_sehat.client_apotek', compact('dataLog'));
    }

    public function sendLab()
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Rajal Satu Sehat');
        session()->put('cucu', 'API Lab');
        set_time_limit(0);

        // $pasien_tanggal = '2022-11-25';
        $pasien_tanggal = Carbon::now()->format('Y-m-d');
        $kemarin = Carbon::yesterday();
        $idRS = Env('IDRS');

        $dataPengunjung = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->where('reg_periksa.stts', 'Sudah')
            ->where('reg_periksa.kd_poli', '!=', 'LAB')
            ->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
            ->orWhere('reg_periksa.tgl_registrasi', $kemarin)
            ->get();

        // dd($dataPengunjung);

        foreach ($dataPengunjung as $pasienLab) {
            $cekLab = DB::connection('mysqlkhanza')->table('permintaan_lab')
                ->join('pegawai', 'pegawai.nik', '=', 'permintaan_lab.dokter_perujuk')
                ->select(
                    'permintaan_lab.noorder',
                    'permintaan_lab.no_rawat',
                    'permintaan_lab.tgl_permintaan',
                    'permintaan_lab.jam_permintaan',
                    'permintaan_lab.tgl_sampel',
                    'permintaan_lab.jam_sampel',
                    'permintaan_lab.tgl_hasil',
                    'permintaan_lab.jam_hasil',
                    'permintaan_lab.dokter_perujuk',
                    'permintaan_lab.status',
                    'pegawai.no_ktp as ktp_dokter',
                    'pegawai.nama as nama_dokter'
                )
                ->where('no_rawat', $pasienLab->no_rawat)
                ->where('permintaan_lab.status', 'ralan')
                ->where('permintaan_lab.jam_hasil', '!=', '00:00:00')
                ->first();

            $idCounter = SatuSehatController::getEncounterId($pasienLab->no_rawat);

            if ((!empty($cekLab)) && (!empty($idCounter))) {
                //Cek apakah sudah pernah kirim data
                $cekResponseLab = ResponseLabSatuSehat::where('noOrder', $cekLab->noorder)->first();

                if (empty($cekResponseLab)) {
                    $dokterPerujuk = SatuSehatController::practitioner($cekLab->ktp_dokter);
                    $idPasien = SatuSehatController::patientSehat($pasienLab->ktp_pasien);
                    // $idPasien = "P02478375538";
                    // $dokterPerujuk = "10009880728";
                    //cek data periksa lab
                    $periksaLab = DB::connection('mysqlkhanza')->table('periksa_lab')
                        ->join('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'periksa_lab.kd_jenis_prw')
                        ->select(
                            'periksa_lab.no_rawat',
                            'periksa_lab.kd_jenis_prw',
                            'periksa_lab.tgl_periksa',
                            'periksa_lab.jam',
                            'periksa_lab.nip as petugas',
                            'periksa_lab.dokter_perujuk',
                            'jns_perawatan_lab.nm_perawatan'
                        )
                        ->where('no_rawat', $pasienLab->no_rawat)
                        ->get();
                    // dd($pasienLab, $cekLab, $idCounter->encounter_id, $cekLab->dokter_perujuk, $dokterPerujuk);
                    // dd($periksaLab);

                    foreach ($periksaLab as $PeriksaLab) {
                        //ambil data mapping Loinc
                        $mappingLoinc = SatuSehatController::getLoinc($PeriksaLab->kd_jenis_prw);
                        $waktuPerawatan = $PeriksaLab->tgl_periksa . ' ' . $PeriksaLab->jam;
                        $waktu_perawatan = new Carbon($waktuPerawatan);
                        $formatWaktuPerawatan = $waktu_perawatan->setTimezone('UTC')->toW3cString();

                        // dd($mappingLoinc);

                        //Cek apakah sudah ada mapping belum
                        if (!empty($mappingLoinc)) {
                            // dd($mappingLoinc);
                            //data JSON
                            $ServiceRequest = [
                                "resourceType" => "ServiceRequest",
                                "identifier" => [
                                    [
                                        "system" => "http://sys-ids.kemkes.go.id/servicerequest/$idRS",
                                        "value" => "$cekLab->noorder"
                                    ]
                                ],
                                "status" => "active",
                                "intent" => "original-order",
                                "priority" => "routine",
                                "code" => [
                                    "coding" => [
                                        [
                                            "system" => "$mappingLoinc->code_system",
                                            "code" => "$mappingLoinc->code",
                                            "display" => "$mappingLoinc->display"
                                        ]
                                    ],
                                    "text" => "$PeriksaLab->nm_perawatan"
                                ],
                                "subject" => [
                                    "reference" => "Patient/$idPasien"
                                ],
                                "encounter" => [
                                    "reference" => "Encounter/$idCounter->encounter_id",
                                    "display" => "Permintaan $PeriksaLab->nm_perawatan pada $PeriksaLab->tgl_periksa pukul $PeriksaLab->jam WIB"
                                ],
                                "occurrenceDateTime" => $formatWaktuPerawatan,
                                "requester" => [
                                    "reference" => "Practitioner/$dokterPerujuk",
                                    "display" => "$cekLab->nama_dokter"
                                ]
                                // ,
                                // "performer" => [
                                //     [
                                //         "reference" => "Practitioner/N10000005",
                                //         "display" => "Fatma"
                                //     ]
                                // ],
                                // "reasonCode" => [
                                //     [
                                //         "text" => "Periksa Keseimbangan Elektrolit"
                                //     ]
                                // ]
                            ];

                            // if ($PeriksaLab->kd_jenis_prw == "J000280") {
                            //     dd($ServiceRequest);
                            // }

                            //Kirim/Create Service Request
                            SatuSehatController::getTokenSehat();
                            $access_token = Session::get('tokenSatuSehat');
                            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                            try {
                                $response = $client->request('POST', 'fhir-r4/v1/ServiceRequest', [
                                    'headers' => [
                                        'Authorization' => "Bearer {$access_token}"
                                    ],
                                    'json' => $ServiceRequest
                                ]);
                            } catch (BadResponseException $e) {
                                if ($e->hasResponse()) {
                                    $response = $e->getResponse();
                                    $test = json_decode($response->getBody());

                                    // dd($test);
                                }

                                $message = "Error Kirim Service Request $PeriksaLab->kd_jenis_prw $PeriksaLab->no_rawat";

                                Session::flash('error', $message);

                                // return redirect()->back()->withInput();
                                $dataLog = ResponseLabSatuSehat::all();

                                // dd($dataLog);

                                return view('satu_sehat.client_rujuklab', compact('dataLog'));
                            }

                            $data = json_decode($response->getBody());

                            if (!empty($data->id) && $data->resourceType == "ServiceRequest") {
                                $idServiceRequest = $data->id;

                                $simpan = new ResponseLabSatuSehat();
                                $simpan->noRawat = $pasienLab->no_rawat;
                                $simpan->tgl_registrasi = $pasienLab->tgl_registrasi;
                                $simpan->noOrder = $cekLab->noorder;
                                $simpan->serviceRequest_id = $idServiceRequest;
                                $simpan->save();

                                //ambil kode spesimen
                                $mapingSpecimen = SatuSehatController::getSpecimen($mappingLoinc->kd_loinc);
                                //Waktu Sampel
                                $waktuSampel = $cekLab->tgl_sampel . ' ' . $cekLab->jam_sampel;
                                $waktu_sampel = new Carbon($waktuPerawatan);
                                $formatWaktuSampel = $waktu_sampel->setTimezone('UTC')->toW3cString();

                                $Specimen = [
                                    "resourceType" => "Specimen",
                                    "identifier" => [
                                        [
                                            "system" => "http://sys-ids.kemkes.go.id/specimen/$idRS",
                                            "value" => "$cekLab->noorder",
                                            "assigner" => [
                                                "reference" => "Organization/$idRS"
                                            ]
                                        ]
                                    ],
                                    "status" => "available",
                                    "type" => [
                                        "coding" => [
                                            [
                                                "system" => "$mapingSpecimen->coding_system",
                                                "code" => "$mapingSpecimen->kd_snomed",
                                                "display" => "$mapingSpecimen->display"
                                            ]
                                        ]
                                    ],
                                    // "collection" => [
                                    //     "method" => [
                                    //         "coding" => [
                                    //             [
                                    //                 "system" => "https://snomed.info/sct",
                                    //                 "code" => "82078001",
                                    //                 "display" => "Collection of blood specimen for laboratory (procedure)"
                                    //             ]
                                    //         ]
                                    //     ],
                                    //     "collectedDateTime" => "2022-06-14T08:15:00+07:00"
                                    // ],
                                    "subject" => [
                                        "reference" => "Patient/$idPasien",
                                        "display" => "$pasienLab->nm_pasien"
                                    ],
                                    "request" => [
                                        [
                                            "reference" => "ServiceRequest/$idServiceRequest"
                                        ]
                                    ],
                                    "receivedTime" => $formatWaktuSampel
                                ];

                                //Kirim/Create Specimen
                                SatuSehatController::getTokenSehat();
                                $access_token = Session::get('tokenSatuSehat');
                                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                                try {
                                    $response = $client->request('POST', 'fhir-r4/v1/Specimen', [
                                        'headers' => [
                                            'Authorization' => "Bearer {$access_token}"
                                        ],
                                        'json' => $Specimen
                                    ]);
                                } catch (BadResponseException $e) {
                                    if ($e->hasResponse()) {
                                        $response = $e->getResponse();
                                        $test = json_decode($response->getBody());
                                        // dd($test);
                                    }

                                    $message = "Error Kirim Specimen dengan id service " . $idServiceRequest;

                                    Session::flash('error', $message);

                                    // return redirect()->back()->withInput();
                                    $dataLog = ResponseLabSatuSehat::all();

                                    // dd($dataLog);

                                    return view('satu_sehat.client_rujuklab', compact('dataLog'));
                                }

                                $responseSpecimen = json_decode($response->getBody());

                                if (!empty($responseSpecimen->id) && $responseSpecimen->resourceType == "Specimen") {
                                    //Update data di table respone medication request
                                    $update = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                                    $update->specimen_id = $responseSpecimen->id;
                                    $update->save();

                                    //cek detail data hasil
                                    // if ($mappingLoinc->permintaan_hasil == "Permintaan dan Hasil") {
                                    $detailLab = DB::connection('mysqlkhanza')->table('detail_periksa_lab')
                                        ->join('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'detail_periksa_lab.kd_jenis_prw')
                                        ->join('template_laboratorium', 'template_laboratorium.id_template', '=', 'detail_periksa_lab.id_template')
                                        ->select(
                                            'detail_periksa_lab.no_rawat',
                                            'detail_periksa_lab.kd_jenis_prw',
                                            'detail_periksa_lab.tgl_periksa',
                                            'detail_periksa_lab.jam',
                                            'detail_periksa_lab.nilai',
                                            'detail_periksa_lab.nilai_rujukan',
                                            'detail_periksa_lab.keterangan',
                                            'detail_periksa_lab.keterangan',
                                            'template_laboratorium.id_template',
                                            'template_laboratorium.Pemeriksaan',
                                            'jns_perawatan_lab.nm_perawatan'
                                        )
                                        ->where('detail_periksa_lab.no_rawat', $pasienLab->no_rawat)
                                        ->where('detail_periksa_lab.kd_jenis_prw', $PeriksaLab->kd_jenis_prw)
                                        ->get();

                                    if ($detailLab->count() > 1) { //id template perlu ditambah di mapping
                                        // dd($detailLab);
                                        foreach ($detailLab as $DetailLab) {
                                            //cek nilai hasil lab kosong atau masih dalam proses jika tidak lanjut
                                            if ((!empty($DetailLab->nilai)) && (strpos($DetailLab->nilai, "proses") === false)) {
                                                //Seharusnya cek dulu ini paket atau tidak hasilnya juga di foreach tp ini lurus2 aja dulu
                                                //dah diatas ya dicek

                                                $dataHasil = SatuSehatController::getTemplateLoinc($DetailLab->id_template);
                                                //Waktu Hasil
                                                $waktuHasil = $DetailLab->tgl_periksa . ' ' . $DetailLab->jam;
                                                $waktu_hasil = new Carbon($waktuHasil);
                                                $formatWaktuHasil = $waktu_hasil->setTimezone('UTC')->toW3cString();

                                                if (!empty($dataHasil)) {
                                                    // dd($dataHasil);
                                                    if ($dataHasil->tipe_hasil_pemeriksaan == "Nominal") { //Answer List diperlukan
                                                        //Get AnswerList Loinc
                                                        $answerList = SatuSehatController::getAnswerLoinc($dataHasil->code, $DetailLab->nilai);

                                                        //Answer List harus sesuai dengan standart jika tidak akan null dan masuk ke jenis narasi
                                                        if (!empty($answerList)) {
                                                            // dd($answerList);
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder"
                                                                    ]
                                                                ],
                                                                "status" => "final",
                                                                "category" => [
                                                                    [
                                                                        "coding" => [
                                                                            [
                                                                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                                "code" => "laboratory",
                                                                                "display" => "Laboratory"
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                "code" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$dataHasil->code_system",
                                                                            "code" => "$dataHasil->code",
                                                                            "display" => "$dataHasil->display"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "subject" => [
                                                                    "reference" => "Patient/$idPasien"
                                                                ],
                                                                "encounter" => [
                                                                    "reference" => "Encounter/$idCounter->encounter_id"
                                                                ],
                                                                "effectiveDateTime" => "$DetailLab->tgl_periksa",
                                                                "issued" => $formatWaktuHasil,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/10006926841"
                                                                    ],
                                                                    [
                                                                        "reference" => "Organization/$idRS"
                                                                    ]
                                                                ],
                                                                "specimen" => [
                                                                    "reference" => "Specimen/$responseSpecimen->id"
                                                                ],
                                                                "basedOn" => [
                                                                    [
                                                                        "reference" => "ServiceRequest/$idServiceRequest"
                                                                    ]
                                                                ],
                                                                "valueCodeableConcept" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$answerList->code_system",
                                                                            "code" => "$answerList->answer_string_id",
                                                                            "display" => "$answerList->display_text"
                                                                        ]
                                                                    ]
                                                                ]
                                                            ];
                                                        } else {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder"
                                                                    ]
                                                                ],
                                                                "status" => "final",
                                                                "category" => [
                                                                    [
                                                                        "coding" => [
                                                                            [
                                                                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                                "code" => "laboratory",
                                                                                "display" => "Laboratory"
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                "code" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$dataHasil->code_system",
                                                                            "code" => "$dataHasil->code",
                                                                            "display" => "$dataHasil->display"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "subject" => [
                                                                    "reference" => "Patient/$idPasien"
                                                                ],
                                                                "encounter" => [
                                                                    "reference" => "Encounter/$idCounter->encounter_id"
                                                                ],
                                                                "effectiveDateTime" => "$DetailLab->tgl_periksa",
                                                                "issued" => $formatWaktuHasil,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/10006926841"
                                                                    ],
                                                                    [
                                                                        "reference" => "Organization/$idRS"
                                                                    ]
                                                                ],
                                                                "specimen" => [
                                                                    "reference" => "Specimen/$responseSpecimen->id"
                                                                ],
                                                                "basedOn" => [
                                                                    [
                                                                        "reference" => "ServiceRequest/$idServiceRequest"
                                                                    ]
                                                                ],
                                                                "valueString" => "Hasil: $DetailLab->nilai, Keterangan: $DetailLab->keterangan, Nilai Rujukan: $DetailLab->nilai_rujukan"
                                                            ];
                                                        }
                                                    } elseif ($dataHasil->tipe_hasil_pemeriksaan == "Ordinal") { //Masih perlu data untuk hasil TBA + ++
                                                        //Get AnswerList Loinc
                                                        $answerList = SatuSehatController::getAnswerLoinc($dataHasil->code, $DetailLab->nilai);
                                                        //Answer List harus sesuai dengan standart jika tidak akan null dan masuk ke jenis narasi
                                                        // dd($answerList);
                                                        if (!empty($answerList)) {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder"
                                                                    ]
                                                                ],
                                                                "status" => "final",
                                                                "category" => [
                                                                    [
                                                                        "coding" => [
                                                                            [
                                                                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                                "code" => "laboratory",
                                                                                "display" => "Laboratory"
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                "code" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$dataHasil->code_system",
                                                                            "code" => "$dataHasil->code",
                                                                            "display" => "$dataHasil->display"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "subject" => [
                                                                    "reference" => "Patient/$idPasien"
                                                                ],
                                                                "encounter" => [
                                                                    "reference" => "Encounter/$idCounter->encounter_id"
                                                                ],
                                                                "effectiveDateTime" => "$DetailLab->tgl_periksa",
                                                                "issued" => $formatWaktuHasil,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/10006926841"
                                                                    ],
                                                                    [
                                                                        "reference" => "Organization/$idRS"
                                                                    ]
                                                                ],
                                                                "specimen" => [
                                                                    "reference" => "Specimen/$responseSpecimen->id"
                                                                ],
                                                                "basedOn" => [
                                                                    [
                                                                        "reference" => "ServiceRequest/$idServiceRequest"
                                                                    ]
                                                                ],
                                                                "valueCodeableConcept" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$answerList->code_system",
                                                                            "code" => "$answerList->answer_string_id",
                                                                            "display" => "$answerList->display_text"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "referenceRange" => [
                                                                    [
                                                                        "text" => "$DetailLab->nilai_rujukan"
                                                                    ]
                                                                ]
                                                            ];
                                                        } else {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder"
                                                                    ]
                                                                ],
                                                                "status" => "final",
                                                                "category" => [
                                                                    [
                                                                        "coding" => [
                                                                            [
                                                                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                                "code" => "laboratory",
                                                                                "display" => "Laboratory"
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                "code" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$dataHasil->code_system",
                                                                            "code" => "$dataHasil->code",
                                                                            "display" => "$dataHasil->display"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "subject" => [
                                                                    "reference" => "Patient/$idPasien"
                                                                ],
                                                                "encounter" => [
                                                                    "reference" => "Encounter/$idCounter->encounter_id"
                                                                ],
                                                                "effectiveDateTime" => "$DetailLab->tgl_periksa",
                                                                "issued" => $formatWaktuHasil,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/10006926841"
                                                                    ],
                                                                    [
                                                                        "reference" => "Organization/$idRS"
                                                                    ]
                                                                ],
                                                                "specimen" => [
                                                                    "reference" => "Specimen/$responseSpecimen->id"
                                                                ],
                                                                "basedOn" => [
                                                                    [
                                                                        "reference" => "ServiceRequest/$idServiceRequest"
                                                                    ]
                                                                ],
                                                                "valueString" => "Hasil: $DetailLab->nilai, Keterangan: $DetailLab->keterangan, Nilai Rujukan: $DetailLab->nilai_rujukan"
                                                            ];
                                                        }
                                                    } elseif ($dataHasil->tipe_hasil_pemeriksaan == "Quantitative") { //OK tinggal data practioner dan pasien
                                                        $Observation = [
                                                            "resourceType" => "Observation",
                                                            "identifier" => [
                                                                [
                                                                    "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                    "value" => "$cekLab->noorder"
                                                                ]
                                                            ],
                                                            "status" => "final",
                                                            "category" => [
                                                                [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                            "code" => "laboratory",
                                                                            "display" => "Laboratory"
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            "code" => [
                                                                "coding" => [
                                                                    [
                                                                        "system" => "$dataHasil->code_system",
                                                                        "code" => "$dataHasil->code",
                                                                        "display" => "$dataHasil->display"
                                                                    ]
                                                                ]
                                                            ],
                                                            "subject" => [
                                                                "reference" => "Patient/$idPasien"
                                                            ],
                                                            "encounter" => [
                                                                "reference" => "Encounter/$idCounter->encounter_id"
                                                            ],
                                                            "effectiveDateTime" => "$DetailLab->tgl_periksa",
                                                            "issued" => $formatWaktuHasil,
                                                            "performer" => [
                                                                [
                                                                    "reference" => "Practitioner/10006926841"
                                                                ],
                                                                [
                                                                    "reference" => "Organization/$idRS"
                                                                ]
                                                            ],
                                                            "specimen" => [
                                                                "reference" => "Specimen/$responseSpecimen->id"
                                                            ],
                                                            "basedOn" => [
                                                                [
                                                                    "reference" => "ServiceRequest/$idServiceRequest"
                                                                ]
                                                            ],
                                                            "valueQuantity" => [
                                                                "value" => floatval($DetailLab->nilai),
                                                                "unit" => "$dataHasil->satuan",
                                                                "system" => "http://unitsofmeasure.org",
                                                                "code" => "$dataHasil->satuan"
                                                            ],
                                                            "referenceRange" => [
                                                                [
                                                                    "text" => "$DetailLab->nilai_rujukan"
                                                                ]
                                                            ]
                                                            // ,
                                                            // "interpretation" => [
                                                            //     [
                                                            //         "coding" => [
                                                            //             [
                                                            //                 "system" => "http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation",
                                                            //                 "code" => "H",
                                                            //                 "display" => "High"
                                                            //             ]
                                                            //         ]
                                                            //     ]
                                                            // ],
                                                            // "referenceRange" => [
                                                            //     [
                                                            //         "low" => [
                                                            //             "value" => 135,
                                                            //             "unit" => "mmol/L",
                                                            //             "system" => "http://unitsofmeasure.org",
                                                            //             "code" => "mmol/L"
                                                            //         ],
                                                            //         "high" => [
                                                            //             "value" => 145,
                                                            //             "unit" => "mmol/L",
                                                            //             "system" => "http://unitsofmeasure.org",
                                                            //             "code" => "mmol/L"
                                                            //         ]
                                                            //     ]
                                                            // ]
                                                        ];
                                                    } elseif ($dataHasil->tipe_hasil_pemeriksaan == "Narative") { //
                                                        // dd($DetailLab, "test", $dataHasil, is_string($DetailLab->nilai), is_numeric($DetailLab->nilai), empty($DetailLab->nilai));

                                                        $Observation = [
                                                            "resourceType" => "Observation",
                                                            "identifier" => [
                                                                [
                                                                    "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                    "value" => "$cekLab->noorder"
                                                                ]
                                                            ],
                                                            "status" => "final",
                                                            "category" => [
                                                                [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                            "code" => "laboratory",
                                                                            "display" => "Laboratory"
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            "code" => [
                                                                "coding" => [
                                                                    [
                                                                        "system" => "$dataHasil->code_system",
                                                                        "code" => "$dataHasil->code",
                                                                        "display" => "$dataHasil->display"
                                                                    ]
                                                                ]
                                                            ],
                                                            "subject" => [
                                                                "reference" => "Patient/$idPasien"
                                                            ],
                                                            "encounter" => [
                                                                "reference" => "Encounter/$idCounter->encounter_id"
                                                            ],
                                                            "effectiveDateTime" => "$DetailLab->tgl_periksa",
                                                            "issued" => $formatWaktuHasil,
                                                            "performer" => [
                                                                [
                                                                    "reference" => "Practitioner/10006926841"
                                                                ],
                                                                [
                                                                    "reference" => "Organization/$idRS"
                                                                ]
                                                            ],
                                                            "specimen" => [
                                                                "reference" => "Specimen/$responseSpecimen->id"
                                                            ],
                                                            "basedOn" => [
                                                                [
                                                                    "reference" => "ServiceRequest/$idServiceRequest"
                                                                ]
                                                            ],
                                                            "valueString" => "Hasil: $DetailLab->nilai, Keterangan: $DetailLab->keterangan, Nilai Rujukan: $DetailLab->nilai_rujukan"
                                                        ];
                                                    }

                                                    //Kirim/Create Observation
                                                    SatuSehatController::getTokenSehat();
                                                    $access_token = Session::get('tokenSatuSehat');
                                                    // dd($access_token);
                                                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                                                    try {
                                                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                                                            'headers' => [
                                                                'Authorization' => "Bearer {$access_token}"
                                                            ],
                                                            'json' => $Observation
                                                        ]);
                                                    } catch (BadResponseException $e) {
                                                        // echo $e->getRequest();
                                                        // echo $e->getResponse();
                                                        if ($e->hasResponse()) {
                                                            $response = $e->getResponse();

                                                            // dd($response);
                                                            $test = json_decode($response->getBody());
                                                            // dd($test);
                                                        }

                                                        $message = "Error Kirim Observation Lab id service " . $idServiceRequest;

                                                        Session::flash('error', $message);

                                                        // return redirect()->back()->withInput();
                                                        $dataLog = ResponseLabSatuSehat::all();

                                                        // dd($dataLog);

                                                        return view('satu_sehat.client_rujuklab', compact('dataLog'));
                                                    }

                                                    $responseObservation = json_decode($response->getBody());
                                                    // dd($responseObservation);
                                                    if (!empty($responseObservation->id) && $responseObservation->resourceType == "Observation") {
                                                        //Create data di table respone observation lab
                                                        $update = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                                                        $newObservation = new ResponseObservationLab();
                                                        $newObservation->response_lab_satu_sehat_id = $update->id;
                                                        $newObservation->observation_id = $responseObservation->id;
                                                        $newObservation->save();
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        foreach ($detailLab as $DetailLab) {
                                            //cek nilai hasil lab kosong atau masih dalam proses jika tidak lanjut
                                            if ((!empty($DetailLab->nilai)) && (strpos($DetailLab->nilai, "proses") === false)) {
                                                //Seharusnya cek dulu ini paket atau tidak hasilnya juga di foreach tp ini lurus2 aja dulu
                                                //dah diatas ya dicek

                                                $dataHasil = SatuSehatController::getLoinc($DetailLab->kd_jenis_prw);
                                                //Waktu Hasil
                                                $waktuHasil = $DetailLab->tgl_periksa . ' ' . $DetailLab->jam;
                                                $waktu_hasil = new Carbon($waktuHasil);
                                                $formatWaktuHasil = $waktu_hasil->setTimezone('UTC')->toW3cString();

                                                if (!empty($dataHasil)) {
                                                    if ($dataHasil->tipe_hasil_pemeriksaan == "Nominal") { //Answer List diperlukan
                                                        //Get AnswerList Loinc
                                                        $answerList = SatuSehatController::getAnswerLoinc($dataHasil->code, $DetailLab->nilai);

                                                        //Answer List harus sesuai dengan standart jika tidak akan null dan masuk ke jenis narasi
                                                        if (!empty($answerList)) {
                                                            // dd($answerList);
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder"
                                                                    ]
                                                                ],
                                                                "status" => "final",
                                                                "category" => [
                                                                    [
                                                                        "coding" => [
                                                                            [
                                                                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                                "code" => "laboratory",
                                                                                "display" => "Laboratory"
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                "code" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$dataHasil->code_system",
                                                                            "code" => "$dataHasil->code",
                                                                            "display" => "$dataHasil->display"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "subject" => [
                                                                    "reference" => "Patient/$idPasien"
                                                                ],
                                                                "encounter" => [
                                                                    "reference" => "Encounter/$idCounter->encounter_id"
                                                                ],
                                                                "effectiveDateTime" => "$DetailLab->tgl_periksa",
                                                                "issued" => $formatWaktuHasil,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/10006926841"
                                                                    ],
                                                                    [
                                                                        "reference" => "Organization/$idRS"
                                                                    ]
                                                                ],
                                                                "specimen" => [
                                                                    "reference" => "Specimen/$responseSpecimen->id"
                                                                ],
                                                                "basedOn" => [
                                                                    [
                                                                        "reference" => "ServiceRequest/$idServiceRequest"
                                                                    ]
                                                                ],
                                                                "valueCodeableConcept" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$answerList->code_system",
                                                                            "code" => "$answerList->answer_string_id",
                                                                            "display" => "$answerList->display_text"
                                                                        ]
                                                                    ]
                                                                ]
                                                            ];
                                                        } else {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder"
                                                                    ]
                                                                ],
                                                                "status" => "final",
                                                                "category" => [
                                                                    [
                                                                        "coding" => [
                                                                            [
                                                                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                                "code" => "laboratory",
                                                                                "display" => "Laboratory"
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                "code" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$dataHasil->code_system",
                                                                            "code" => "$dataHasil->code",
                                                                            "display" => "$dataHasil->display"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "subject" => [
                                                                    "reference" => "Patient/$idPasien"
                                                                ],
                                                                "encounter" => [
                                                                    "reference" => "Encounter/$idCounter->encounter_id"
                                                                ],
                                                                "effectiveDateTime" => "$DetailLab->tgl_periksa",
                                                                "issued" => $formatWaktuHasil,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/10006926841"
                                                                    ],
                                                                    [
                                                                        "reference" => "Organization/$idRS"
                                                                    ]
                                                                ],
                                                                "specimen" => [
                                                                    "reference" => "Specimen/$responseSpecimen->id"
                                                                ],
                                                                "basedOn" => [
                                                                    [
                                                                        "reference" => "ServiceRequest/$idServiceRequest"
                                                                    ]
                                                                ],
                                                                "valueString" => "Hasil: $DetailLab->nilai, Keterangan: $DetailLab->keterangan, Nilai Rujukan: $DetailLab->nilai_rujukan"
                                                            ];
                                                        }
                                                    } elseif ($dataHasil->tipe_hasil_pemeriksaan == "Ordinal") { //Masih perlu data untuk hasil TBA + ++
                                                        //Get AnswerList Loinc
                                                        $answerList = SatuSehatController::getAnswerLoinc($dataHasil->code, $DetailLab->nilai);
                                                        //Answer List harus sesuai dengan standart jika tidak akan null dan masuk ke jenis narasi
                                                        // dd($answerList);
                                                        if (!empty($answerList)) {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder"
                                                                    ]
                                                                ],
                                                                "status" => "final",
                                                                "category" => [
                                                                    [
                                                                        "coding" => [
                                                                            [
                                                                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                                "code" => "laboratory",
                                                                                "display" => "Laboratory"
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                "code" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$dataHasil->code_system",
                                                                            "code" => "$dataHasil->code",
                                                                            "display" => "$dataHasil->display"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "subject" => [
                                                                    "reference" => "Patient/$idPasien"
                                                                ],
                                                                "encounter" => [
                                                                    "reference" => "Encounter/$idCounter->encounter_id"
                                                                ],
                                                                "effectiveDateTime" => "$DetailLab->tgl_periksa",
                                                                "issued" => $formatWaktuHasil,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/10006926841"
                                                                    ],
                                                                    [
                                                                        "reference" => "Organization/$idRS"
                                                                    ]
                                                                ],
                                                                "specimen" => [
                                                                    "reference" => "Specimen/$responseSpecimen->id"
                                                                ],
                                                                "basedOn" => [
                                                                    [
                                                                        "reference" => "ServiceRequest/$idServiceRequest"
                                                                    ]
                                                                ],
                                                                "valueCodeableConcept" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$answerList->code_system",
                                                                            "code" => "$answerList->answer_string_id",
                                                                            "display" => "$answerList->display_text"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "referenceRange" => [
                                                                    [
                                                                        "text" => "$DetailLab->nilai_rujukan"
                                                                    ]
                                                                ]
                                                            ];
                                                        } else {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder"
                                                                    ]
                                                                ],
                                                                "status" => "final",
                                                                "category" => [
                                                                    [
                                                                        "coding" => [
                                                                            [
                                                                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                                "code" => "laboratory",
                                                                                "display" => "Laboratory"
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                "code" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$dataHasil->code_system",
                                                                            "code" => "$dataHasil->code",
                                                                            "display" => "$dataHasil->display"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "subject" => [
                                                                    "reference" => "Patient/$idPasien"
                                                                ],
                                                                "encounter" => [
                                                                    "reference" => "Encounter/$idCounter->encounter_id"
                                                                ],
                                                                "effectiveDateTime" => "$DetailLab->tgl_periksa",
                                                                "issued" => $formatWaktuHasil,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/10006926841"
                                                                    ],
                                                                    [
                                                                        "reference" => "Organization/$idRS"
                                                                    ]
                                                                ],
                                                                "specimen" => [
                                                                    "reference" => "Specimen/$responseSpecimen->id"
                                                                ],
                                                                "basedOn" => [
                                                                    [
                                                                        "reference" => "ServiceRequest/$idServiceRequest"
                                                                    ]
                                                                ],
                                                                "valueString" => "Hasil: $DetailLab->nilai, Keterangan: $DetailLab->keterangan, Nilai Rujukan: $DetailLab->nilai_rujukan"
                                                            ];
                                                        }
                                                    } elseif ($dataHasil->tipe_hasil_pemeriksaan == "Quantitative") { //OK tinggal data practioner dan pasien
                                                        $Observation = [
                                                            "resourceType" => "Observation",
                                                            "identifier" => [
                                                                [
                                                                    "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                    "value" => "$cekLab->noorder"
                                                                ]
                                                            ],
                                                            "status" => "final",
                                                            "category" => [
                                                                [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                            "code" => "laboratory",
                                                                            "display" => "Laboratory"
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            "code" => [
                                                                "coding" => [
                                                                    [
                                                                        "system" => "$dataHasil->code_system",
                                                                        "code" => "$dataHasil->code",
                                                                        "display" => "$dataHasil->display"
                                                                    ]
                                                                ]
                                                            ],
                                                            "subject" => [
                                                                "reference" => "Patient/$idPasien"
                                                            ],
                                                            "encounter" => [
                                                                "reference" => "Encounter/$idCounter->encounter_id"
                                                            ],
                                                            "effectiveDateTime" => "$DetailLab->tgl_periksa",
                                                            "issued" => $formatWaktuHasil,
                                                            "performer" => [
                                                                [
                                                                    "reference" => "Practitioner/10006926841"
                                                                ],
                                                                [
                                                                    "reference" => "Organization/$idRS"
                                                                ]
                                                            ],
                                                            "specimen" => [
                                                                "reference" => "Specimen/$responseSpecimen->id"
                                                            ],
                                                            "basedOn" => [
                                                                [
                                                                    "reference" => "ServiceRequest/$idServiceRequest"
                                                                ]
                                                            ],
                                                            "valueQuantity" => [
                                                                "value" => floatval($DetailLab->nilai),
                                                                "unit" => "$dataHasil->satuan",
                                                                "system" => "http://unitsofmeasure.org",
                                                                "code" => "$dataHasil->satuan"
                                                            ],
                                                            "referenceRange" => [
                                                                [
                                                                    "text" => "$DetailLab->nilai_rujukan"
                                                                ]
                                                            ]
                                                            // ,
                                                            // "interpretation" => [
                                                            //     [
                                                            //         "coding" => [
                                                            //             [
                                                            //                 "system" => "http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation",
                                                            //                 "code" => "H",
                                                            //                 "display" => "High"
                                                            //             ]
                                                            //         ]
                                                            //     ]
                                                            // ],
                                                            // "referenceRange" => [
                                                            //     [
                                                            //         "low" => [
                                                            //             "value" => 135,
                                                            //             "unit" => "mmol/L",
                                                            //             "system" => "http://unitsofmeasure.org",
                                                            //             "code" => "mmol/L"
                                                            //         ],
                                                            //         "high" => [
                                                            //             "value" => 145,
                                                            //             "unit" => "mmol/L",
                                                            //             "system" => "http://unitsofmeasure.org",
                                                            //             "code" => "mmol/L"
                                                            //         ]
                                                            //     ]
                                                            // ]
                                                        ];
                                                    } elseif ($dataHasil->tipe_hasil_pemeriksaan == "Narative") { //

                                                        $Observation = [
                                                            "resourceType" => "Observation",
                                                            "identifier" => [
                                                                [
                                                                    "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                    "value" => "$cekLab->noorder"
                                                                ]
                                                            ],
                                                            "status" => "final",
                                                            "category" => [
                                                                [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                            "code" => "laboratory",
                                                                            "display" => "Laboratory"
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            "code" => [
                                                                "coding" => [
                                                                    [
                                                                        "system" => "$dataHasil->code_system",
                                                                        "code" => "$dataHasil->code",
                                                                        "display" => "$dataHasil->display"
                                                                    ]
                                                                ]
                                                            ],
                                                            "subject" => [
                                                                "reference" => "Patient/$idPasien"
                                                            ],
                                                            "encounter" => [
                                                                "reference" => "Encounter/$idCounter->encounter_id"
                                                            ],
                                                            "effectiveDateTime" => "$DetailLab->tgl_periksa",
                                                            "issued" => $formatWaktuHasil,
                                                            "performer" => [
                                                                [
                                                                    "reference" => "Practitioner/10006926841"
                                                                ],
                                                                [
                                                                    "reference" => "Organization/$idRS"
                                                                ]
                                                            ],
                                                            "specimen" => [
                                                                "reference" => "Specimen/$responseSpecimen->id"
                                                            ],
                                                            "basedOn" => [
                                                                [
                                                                    "reference" => "ServiceRequest/$idServiceRequest"
                                                                ]
                                                            ],
                                                            "valueString" => "Hasil: $DetailLab->nilai, Keterangan: $DetailLab->keterangan, Nilai Rujukan: $DetailLab->nilai_rujukan"
                                                        ];
                                                    }

                                                    //Kirim/Create Observation
                                                    SatuSehatController::getTokenSehat();
                                                    $access_token = Session::get('tokenSatuSehat');
                                                    // dd($access_token);
                                                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                                                    try {
                                                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                                                            'headers' => [
                                                                'Authorization' => "Bearer {$access_token}"
                                                            ],
                                                            'json' => $Observation
                                                        ]);
                                                    } catch (BadResponseException $e) {
                                                        // echo $e->getRequest();
                                                        // echo $e->getResponse();
                                                        if ($e->hasResponse()) {
                                                            $response = $e->getResponse();

                                                            // dd($response);
                                                            $test = json_decode($response->getBody());
                                                            // dd($test);
                                                        }

                                                        $message = "Error Kirim Observation Lab id service " . $idServiceRequest;

                                                        Session::flash('error', $message);

                                                        // return redirect()->back()->withInput();
                                                        $dataLog = ResponseLabSatuSehat::all();

                                                        // dd($dataLog);

                                                        return view('satu_sehat.client_rujuklab', compact('dataLog'));
                                                    }

                                                    $responseObservation = json_decode($response->getBody());
                                                    // dd($responseObservation);
                                                    if (!empty($responseObservation->id) && $responseObservation->resourceType == "Observation") {
                                                        //Create data di table respone observation lab
                                                        $update = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                                                        $newObservation = new ResponseObservationLab();
                                                        $newObservation->response_lab_satu_sehat_id = $update->id;
                                                        $newObservation->observation_id = $responseObservation->id;
                                                        $newObservation->save();
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    // dd($pasienLab, $cekLab, $idCounter->encounter_id, $cekLab->dokter_perujuk, $dokterPerujuk);

                                } else {
                                    dd($responseSpecimen);
                                }

                                //Diagnostic Report
                                //Cek dulu Observasinya ada berapa hasil
                                $cekID = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                                $cekObservation = ResponseObservationLab::where('response_lab_satu_sehat_id', $cekID->id)->get();
                                // dd($cekObservation);

                                if (count($cekObservation) > 0) {

                                    $arrObservation = array($cekObservation);
                                    // $arrObservation = array();
                                    $noObservation = 0;

                                    for ($i = 0; $i < $cekObservation->count(); $i++) {
                                        ++$noObservation;
                                        $idOb = $arrObservation[0][$i]['observation_id'];
                                        $tambah[$i] = array(
                                            'id' => "$noObservation",
                                            'reference' => "Observation/$idOb",
                                        );
                                    }

                                    if (empty($tambah)) {
                                        dd($cekObservation, "cek");
                                    }

                                    $Report = [
                                        "resourceType" => "DiagnosticReport",
                                        "identifier" => [
                                            [
                                                "system" => "http://sys-ids.kemkes.go.id/diagnostic/$idRS/lab",
                                                "use" => "official",
                                                "value" => "$cekLab->noorder"
                                            ]
                                        ],
                                        "status" => "final",
                                        "category" => [
                                            [
                                                "coding" => [
                                                    [
                                                        "system" => "http://terminology.hl7.org/CodeSystem/v2-0074",
                                                        "code" => "CH",
                                                        "display" => "Chemistry"
                                                    ]
                                                ]
                                            ]
                                        ],
                                        "code" => [
                                            "coding" => [
                                                [
                                                    "system" => "$mappingLoinc->code_system",
                                                    "code" => "$mappingLoinc->code",
                                                    "display" => "$mappingLoinc->display"
                                                ]
                                            ]
                                        ],
                                        "subject" => [
                                            "reference" => "Patient/$idPasien"
                                        ],
                                        "encounter" => [
                                            "reference" => "Encounter/$idCounter->encounter_id"
                                        ],
                                        "effectiveDateTime" => $PeriksaLab->tgl_periksa . "T" . $PeriksaLab->jam . "+07:00",
                                        "issued" => $PeriksaLab->tgl_periksa . "T" . $PeriksaLab->jam . "+07:00",
                                        "performer" => [
                                            // [
                                            //     "reference" => "Practitioner/10006926841"
                                            // ],
                                            [
                                                "reference" => "Organization/$idRS"
                                            ]
                                        ],
                                        // "result" => [
                                        //     [
                                        //         "id" => "1",
                                        //         "reference" => "Observation/b04db6af-2d31-4509-9a00-9b5fc073b389"
                                        //     ],
                                        //     [
                                        //         "id" => "2",
                                        //         "reference" => "Observation/150bc626-7d79-4e38-a37a-38ab8e1a23d2"
                                        //     ],
                                        //     [
                                        //         "id" => "3",
                                        //         "reference" => "Observation/26800d31-b6fd-43fa-83e2-4750410c789c"
                                        //     ]
                                        // ],
                                        "result" => $tambah,
                                        "specimen" => [
                                            [
                                                "reference" => "Specimen/$responseSpecimen->id"
                                            ]
                                        ],
                                        "basedOn" => [
                                            [
                                                "reference" => "ServiceRequest/$idServiceRequest"
                                            ]
                                        ]
                                        // ,
                                        // "conclusion" => "Hipernatremia, Hiperkloremia, Hipokalemia"
                                    ];

                                    // array_push($Report['result'], $tambah);
                                    // dd($Report);
                                    //Kirim/Create Diagnostic Report
                                    SatuSehatController::getTokenSehat();
                                    $access_token = Session::get('tokenSatuSehat');
                                    // dd($access_token);
                                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                                    try {
                                        $response = $client->request('POST', 'fhir-r4/v1/DiagnosticReport', [
                                            'headers' => [
                                                'Authorization' => "Bearer {$access_token}"
                                            ],
                                            'json' => $Report
                                        ]);
                                    } catch (BadResponseException $e) {
                                        // echo $e->getRequest();
                                        // echo $e->getResponse();
                                        if ($e->hasResponse()) {
                                            $response = $e->getResponse();

                                            // dd($response);
                                            $test = json_decode($response->getBody());
                                            dd($test);
                                        }

                                        $message = "Error Kirim Report lab id service request " . $idServiceRequest;

                                        Session::flash('error', $message);

                                        // return redirect()->back()->withInput();
                                        $dataLog = ResponseLabSatuSehat::all();

                                        // dd($dataLog);

                                        return view('satu_sehat.client_rujuklab', compact('dataLog'));
                                    }

                                    $responseReport = json_decode($response->getBody());
                                    // dd($responseReport->id);
                                    if (!empty($responseReport->id) && $responseReport->resourceType == "DiagnosticReport") {
                                        //Update data di table respone lab
                                        $update = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                                        $update->report_id = $responseReport->id;
                                        $update->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $dataLog = ResponseLabSatuSehat::whereDate('created_at', new Carbon($pasien_tanggal))->get();

        // dd($dataLog);

        return view('satu_sehat.client_rujuklab', compact('dataLog'));
    }

    public function bundleLab(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Rajal Satu Sehat');
        session()->put('cucu', 'API Lab/MCU/CL');
        set_time_limit(0);

        if (empty($request->get('tanggal'))) {
            $pasien_tanggal = Carbon::now();
        } else {
            $pasien_tanggal = new Carbon($request->get('tanggal'));
        }

        // $pasien_tanggal = Carbon::now()->format('Y-m-d');
        $idRS = env('IDRS');

        // dd($pasien_tanggal);

        $dataPengunjung = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            ->whereDate('reg_periksa.tgl_registrasi', $pasien_tanggal)
            ->where('reg_periksa.status_lanjut', 'Ralan')
            // ->where('reg_periksa.stts', 'Sudah')
            ->where(function ($q) {
                $q->where('reg_periksa.kd_poli', '=', 'mcu')
                    ->orWhere('reg_periksa.kd_poli', '=', 'lab');
            })
            ->get();

        // dd($dataPengunjung);
        //Mengirim data Encounter
        foreach ($dataPengunjung as $key => $pengunjung) {
            $cekLog = ResponseSatuSehat::where('noRawat', $pengunjung->no_rawat)->count();

            if ($cekLog == 0) {
                $idDokter = SatuSehatController::practitioner($pengunjung->ktp_dokter);
                $idPasien = SatuSehatController::patientSehat($pengunjung->ktp_pasien);
                $idLokasi = SatuSehatController::getIdPoli($pengunjung->kd_poli);
                $waktuPermintaan = SatuSehatController::getWaktuLab($pengunjung->no_rawat);

                if ((!empty($idPasien)) && (!empty($idDokter)) && (!empty($waktuPermintaan))) {
                    //Waktu
                    // $waktuAwal = $pengunjung->tgl_registrasi . ' ' . $pengunjung->jam_reg;
                    // $waktu_mulai = new Carbon($waktuAwal);
                    // $waktuSelesai = Carbon::parse($waktuAwal)->addHour(2);
                    // $formatWaktuMulai = Carbon::parse($waktuAwal)->format('Y-m-d') . 'T' . Carbon::parse($waktuAwal)->format('H:i:s+07:00');
                    // $waktuInprogress = Carbon::parse($waktuAwal)->addHour();
                    // $formatWaktuProgress = Carbon::parse($waktuInprogress)->format('Y-m-d') . 'T' . Carbon::parse($waktuInprogress)->format('H:i:s+07:00');

                    //Waktu
                    // dd($waktuPermintaan);
                    $waktuAwal = $waktuPermintaan->tgl_permintaan . ' ' . $waktuPermintaan->jam_permintaan;
                    $waktu_mulai = new Carbon($waktuAwal);
                    // $formatWaktuMulai = Carbon::parse($waktuAwal)->format('Y-m-d') . 'T' . Carbon::parse($waktuAwal)->format('H:i:s+07:00');
                    $formatWaktuMulai = $waktu_mulai->setTimezone('UTC')->toW3cString();

                    if (!empty($waktuPermintaan->tgl_sampel) && ($waktuPermintaan->tgl_hasil != '0000-00-00')) {
                        $waktuInprogress = Carbon::parse("$waktuPermintaan->tgl_sampel $waktuPermintaan->jam_sampel");
                    } else {
                        $waktuInprogress = Carbon::parse($waktuAwal)->addMinute(10);
                        // dd($dataPengunjung->no_rawat, $waktu_mulai, $waktuInprogress);
                    }
                    // $formatWaktuProgress = Carbon::parse($waktuInprogress)->format('Y-m-d') . 'T' . Carbon::parse($waktuInprogress)->format('H:i:s+07:00');
                    $formatWaktuProgress = $waktuInprogress->setTimezone('UTC')->toW3cString();

                    if (!empty($waktuPermintaan->tgl_hasil) && ($waktuPermintaan->tgl_hasil != '0000-00-00')) {
                        // dd($waktuPermintaan->tgl_hasil);
                        $waktuSelesai = Carbon::parse($waktuPermintaan->tgl_hasil . ' ' . $waktuPermintaan->jam_hasil);
                    } else {
                        $waktuSelesai = Carbon::parse($waktuAwal)->addMinute(30);
                        // dd($dataPengunjung->no_rawat, $waktu_mulai, $waktuInprogress, $waktuSelesai);
                    }
                    // $formatWaktuSelesai = Carbon::parse($waktuSelesai)->format('Y-m-d') . 'T' . Carbon::parse($waktuSelesai)->format('H:i:s+07:00');
                    $formatWaktuSelesai = $waktuSelesai->setTimezone('UTC')->toW3cString();

                    if ($waktuAwal > $waktuSelesai) {
                        $waktuAwal = $pengunjung->tgl_registrasi . ' ' . $pengunjung->jam_reg;
                        $waktu_mulai = new Carbon($waktuAwal);
                        $formatWaktuMulai = $waktu_mulai->setTimezone('UTC')->toW3cString();
                    }

                    // dd($waktuAwal, $waktuSelesai, $formatWaktuMulai, $formatWaktuSelesai);

                    $dataEncounter = [
                        "resourceType" => "Encounter",
                        "identifier" => [
                            [
                                "system" => "http://sys-ids.kemkes.go.id/encounter/$idRS",
                                "value" => "$pengunjung->no_rawat"
                            ]
                        ],
                        "status" => "arrived",
                        "class" => [
                            "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                            "code" => "AMB",
                            "display" => "ambulatory"
                        ],
                        "subject" => [
                            "reference" => "Patient/$idPasien",
                            "display" => "$pengunjung->nm_pasien"
                        ],
                        "participant" => [
                            [
                                "type" => [
                                    [
                                        "coding" => [
                                            [
                                                "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                                "code" => "ATND",
                                                "display" => "attender"
                                            ]
                                        ]
                                    ]
                                ],
                                "individual" => [
                                    "reference" => "Practitioner/$idDokter",
                                    "display" => "$pengunjung->nama_dokter"
                                ]
                            ]
                        ],
                        "period" => [
                            "start" => "$formatWaktuMulai"
                        ],
                        "location" => [
                            [
                                "location" => [
                                    "reference" => "Location/$idLokasi",
                                    "display" => "$pengunjung->nm_poli"
                                ]
                            ]
                        ],
                        "statusHistory" => [
                            [
                                "status" => "arrived",
                                "period" => [
                                    "start" => "$formatWaktuMulai",
                                    "end" => "$formatWaktuSelesai"
                                ]
                            ]
                        ],
                        "serviceProvider" => [
                            "reference" => "Organization/$idRS"
                        ]
                    ];

                    // dd($dataEncounter);

                    //Send data
                    SatuSehatController::getTokenSehat();
                    $access_token = Session::get('tokenSatuSehat');
                    // dd($access_token);
                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Encounter', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataEncounter
                        ]);
                    } catch (BadResponseException $e) {
                        // echo $e->getRequest();
                        // echo $e->getResponse();
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            // dd($response);
                            $test = json_decode($response->getBody());
                            // dd($test, $test->issue[0]->details->text, $pengunjung, $dataEncounter);
                        }
                        $message = "Error Kirim Encounter No Rawat" . $pengunjung->no_rawat;

                        // dd($message);

                        Session::flash('error', $message);

                        goto KirimEncounterLainnya;
                    }

                    // dd($response);

                    $data = json_decode($response->getBody());

                    // dd($data);

                    $simpan = new ResponseSatuSehat();
                    $simpan->noRawat = $pengunjung->no_rawat;
                    $simpan->tgl_registrasi = $pengunjung->tgl_registrasi;
                    $simpan->encounter_id = $data->id;
                    $simpan->save();
                }
            }

            KirimEncounterLainnya:
        }

        //Mengirim data Lab nya
        foreach ($dataPengunjung as $pasienLab) {
            $cekLab = DB::connection('mysqlkhanza')->table('permintaan_lab')
                ->join('pegawai', 'pegawai.nik', '=', 'permintaan_lab.dokter_perujuk')
                ->select(
                    'permintaan_lab.noorder',
                    'permintaan_lab.no_rawat',
                    'permintaan_lab.tgl_permintaan',
                    'permintaan_lab.jam_permintaan',
                    'permintaan_lab.tgl_sampel',
                    'permintaan_lab.jam_sampel',
                    'permintaan_lab.tgl_hasil',
                    'permintaan_lab.jam_hasil',
                    'permintaan_lab.dokter_perujuk',
                    'permintaan_lab.status',
                    'pegawai.no_ktp as ktp_dokter',
                    'pegawai.nama as nama_dokter'
                )
                ->where('no_rawat', $pasienLab->no_rawat)
                ->where('permintaan_lab.status', 'ralan')
                ->where('permintaan_lab.jam_hasil', '!=', '00:00:00')
                ->first();

            $idCounter = SatuSehatController::getEncounterId($pasienLab->no_rawat);
            $idDokter = SatuSehatController::practitioner($pasienLab->ktp_dokter);
            $idPasien = SatuSehatController::patientSehat($pasienLab->ktp_pasien);

            if ((!empty($cekLab)) && (!empty($idCounter))) {
                //Cek apakah sudah pernah kirim data
                $cekResponseLab = ResponseLabSatuSehat::where('noOrder', $cekLab->noorder)->first();

                if (empty($cekResponseLab)) {
                    $dokterPerujuk = SatuSehatController::practitioner($cekLab->ktp_dokter);
                    // $idPasien = "P02478375538";
                    // $dokterPerujuk = "10009880728";
                    //cek data periksa lab
                    $periksaLab = DB::connection('mysqlkhanza')->table('periksa_lab')
                        ->join('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'periksa_lab.kd_jenis_prw')
                        ->select(
                            'periksa_lab.no_rawat',
                            'periksa_lab.kd_jenis_prw',
                            'periksa_lab.tgl_periksa',
                            'periksa_lab.jam',
                            'periksa_lab.nip as petugas',
                            'periksa_lab.dokter_perujuk',
                            'jns_perawatan_lab.nm_perawatan'
                        )
                        ->where('no_rawat', $pasienLab->no_rawat)
                        ->get();
                    // dd($pasienLab, $cekLab, $idCounter->encounter_id, $cekLab->dokter_perujuk, $dokterPerujuk);
                    // dd($periksaLab);
                    $loop = 0;
                    foreach ($periksaLab as $PeriksaLab) {
                        //ambil data mapping Loinc
                        $mappingLoinc = SatuSehatController::getLoinc($PeriksaLab->kd_jenis_prw);
                        $waktuPeriksaLab = new Carbon("$PeriksaLab->tgl_periksa $PeriksaLab->jam");
                        $formatPeriksaLab = $waktuPeriksaLab->setTimezone('UTC')->toW3cString();
                        $waktuSampel = new Carbon("$cekLab->tgl_sampel $cekLab->jam_sampel");
                        $formatWaktuSampel = $waktuSampel->setTimezone('UTC')->toW3cString();

                        // dd($PeriksaLab->tgl_periksa, $PeriksaLab->jam, $formatPeriksaLab);

                        //Cek apakah sudah ada mapping belum
                        if (!empty($mappingLoinc) && $loop <= 10) {
                            // dd($mappingLoinc);
                            //data JSON
                            $ServiceRequest = [
                                "resourceType" => "ServiceRequest",
                                "identifier" => [
                                    [
                                        "system" => "http://sys-ids.kemkes.go.id/servicerequest/$idRS",
                                        "value" => "$cekLab->noorder"
                                    ]
                                ],
                                "status" => "active",
                                "intent" => "original-order",
                                "priority" => "routine",
                                "code" => [
                                    "coding" => [
                                        [
                                            "system" => "$mappingLoinc->code_system",
                                            "code" => "$mappingLoinc->code",
                                            "display" => "$mappingLoinc->display"
                                        ]
                                    ],
                                    "text" => "$PeriksaLab->nm_perawatan"
                                ],
                                "subject" => [
                                    "reference" => "Patient/$idPasien"
                                ],
                                "encounter" => [
                                    "reference" => "Encounter/$idCounter->encounter_id",
                                    "display" => "Permintaan $PeriksaLab->nm_perawatan pada $PeriksaLab->tgl_periksa pukul $PeriksaLab->jam WIB"
                                ],
                                "occurrenceDateTime" => $formatPeriksaLab,
                                "requester" => [
                                    "reference" => "Practitioner/$dokterPerujuk",
                                    "display" => "$cekLab->nama_dokter"
                                ],
                                "performer" => [
                                    [
                                        "reference" => "Practitioner/$idDokter"
                                        // ,
                                        // "display" => "Fatma"
                                    ]
                                ],
                                // "reasonCode" => [
                                //     [
                                //         "text" => "Periksa Keseimbangan Elektrolit"
                                //     ]
                                // ]
                            ];

                            // if ($PeriksaLab->kd_jenis_prw == "J000280") {
                            //     dd($ServiceRequest);
                            // }

                            //Kirim/Create Service Request
                            SatuSehatController::getTokenSehat();
                            $access_token = Session::get('tokenSatuSehat');
                            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                            try {
                                $response = $client->request('POST', 'fhir-r4/v1/ServiceRequest', [
                                    'headers' => [
                                        'Authorization' => "Bearer {$access_token}"
                                    ],
                                    'json' => $ServiceRequest
                                ]);
                            } catch (BadResponseException $e) {
                                if ($e->hasResponse()) {
                                    $response = $e->getResponse();
                                    $test = json_decode($response->getBody());

                                    dd($test);
                                }

                                $message = "Error Kirim Service Request $PeriksaLab->kd_jenis_prw $PeriksaLab->no_rawat";

                                Session::flash('error', $message);

                                goto KirimPasienLain;
                            }

                            $data = json_decode($response->getBody());

                            if (!empty($data->id) && $data->resourceType == "ServiceRequest") {
                                $idServiceRequest = $data->id;

                                $simpan = new ResponseLabSatuSehat();
                                $simpan->noRawat = $pasienLab->no_rawat;
                                $simpan->tgl_registrasi = $pasienLab->tgl_registrasi;
                                $simpan->noOrder = $cekLab->noorder;
                                $simpan->serviceRequest_id = $idServiceRequest;
                                $simpan->save();

                                //ambil kode spesimen
                                $mapingSpecimen = SatuSehatController::getSpecimen($mappingLoinc->kd_loinc);

                                $Specimen = [
                                    "resourceType" => "Specimen",
                                    "identifier" => [
                                        [
                                            "system" => "http://sys-ids.kemkes.go.id/specimen/$idRS",
                                            "value" => "$cekLab->noorder",
                                            "assigner" => [
                                                "reference" => "Organization/$idRS"
                                            ]
                                        ]
                                    ],
                                    "status" => "available",
                                    "type" => [
                                        "coding" => [
                                            [
                                                "system" => "$mapingSpecimen->coding_system",
                                                "code" => "$mapingSpecimen->kd_snomed",
                                                "display" => "$mapingSpecimen->display"
                                            ]
                                        ]
                                    ],
                                    // "collection" => [
                                    //     "method" => [
                                    //         "coding" => [
                                    //             [
                                    //                 "system" => "https://snomed.info/sct",
                                    //                 "code" => "82078001",
                                    //                 "display" => "Collection of blood specimen for laboratory (procedure)"
                                    //             ]
                                    //         ]
                                    //     ],
                                    //     "collectedDateTime" => "2022-06-14T08:15:00+07:00"
                                    // ],
                                    "subject" => [
                                        "reference" => "Patient/$idPasien",
                                        "display" => "$pasienLab->nm_pasien"
                                    ],
                                    "request" => [
                                        [
                                            "reference" => "ServiceRequest/$idServiceRequest"
                                        ]
                                    ],
                                    "receivedTime" => $formatWaktuSampel
                                ];

                                //Kirim/Create Specimen
                                SatuSehatController::getTokenSehat();
                                $access_token = Session::get('tokenSatuSehat');
                                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                                try {
                                    $response = $client->request('POST', 'fhir-r4/v1/Specimen', [
                                        'headers' => [
                                            'Authorization' => "Bearer {$access_token}"
                                        ],
                                        'json' => $Specimen
                                    ]);
                                } catch (BadResponseException $e) {
                                    if ($e->hasResponse()) {
                                        $response = $e->getResponse();
                                        $test = json_decode($response->getBody());
                                        dd($test);
                                    }

                                    $message = "Error Kirim Specimen dengan id service " . $idServiceRequest;

                                    Session::flash('error', $message);

                                    goto KirimPasienLain;
                                }

                                $responseSpecimen = json_decode($response->getBody());

                                if (!empty($responseSpecimen->id) && $responseSpecimen->resourceType == "Specimen") {
                                    //Update data di table respone medication request
                                    $update = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                                    $update->specimen_id = $responseSpecimen->id;
                                    $update->save();

                                    //cek detail data hasil
                                    // if ($mappingLoinc->permintaan_hasil == "Permintaan dan Hasil") {
                                    $detailLab = DB::connection('mysqlkhanza')->table('detail_periksa_lab')
                                        ->join('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'detail_periksa_lab.kd_jenis_prw')
                                        ->join('template_laboratorium', 'template_laboratorium.id_template', '=', 'detail_periksa_lab.id_template')
                                        ->select(
                                            'detail_periksa_lab.no_rawat',
                                            'detail_periksa_lab.kd_jenis_prw',
                                            'detail_periksa_lab.tgl_periksa',
                                            'detail_periksa_lab.jam',
                                            'detail_periksa_lab.nilai',
                                            'detail_periksa_lab.nilai_rujukan',
                                            'detail_periksa_lab.keterangan',
                                            'detail_periksa_lab.keterangan',
                                            'template_laboratorium.id_template',
                                            'template_laboratorium.Pemeriksaan',
                                            'jns_perawatan_lab.nm_perawatan'
                                        )
                                        ->where('detail_periksa_lab.no_rawat', $pasienLab->no_rawat)
                                        ->where('detail_periksa_lab.kd_jenis_prw', $PeriksaLab->kd_jenis_prw)
                                        ->get();

                                    if ($detailLab->count() > 1) { //id template perlu ditambah di mapping
                                        // dd($detailLab);
                                        foreach ($detailLab as $DetailLab) {
                                            //cek nilai hasil lab kosong atau masih dalam proses jika tidak lanjut
                                            if ((!empty($DetailLab->nilai)) && (strpos($DetailLab->nilai, "proses") === false)) {
                                                //Seharusnya cek dulu ini paket atau tidak hasilnya juga di foreach tp ini lurus2 aja dulu
                                                //dah diatas ya dicek

                                                $dataHasil = SatuSehatController::getTemplateLoinc($DetailLab->id_template);

                                                if (!empty($dataHasil)) {
                                                    // dd($dataHasil);
                                                    if ($dataHasil->tipe_hasil_pemeriksaan == "Nominal") { //Answer List diperlukan
                                                        //Get AnswerList Loinc
                                                        $answerList = SatuSehatController::getAnswerLoinc($dataHasil->code, $DetailLab->nilai);

                                                        //Answer List harus sesuai dengan standart jika tidak akan null dan masuk ke jenis narasi
                                                        if (!empty($answerList)) {
                                                            // dd($answerList);
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder"
                                                                    ]
                                                                ],
                                                                "status" => "final",
                                                                "category" => [
                                                                    [
                                                                        "coding" => [
                                                                            [
                                                                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                                "code" => "laboratory",
                                                                                "display" => "Laboratory"
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                "code" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$dataHasil->code_system",
                                                                            "code" => "$dataHasil->code",
                                                                            "display" => "$dataHasil->display"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "subject" => [
                                                                    "reference" => "Patient/$idPasien"
                                                                ],
                                                                "encounter" => [
                                                                    "reference" => "Encounter/$idCounter->encounter_id"
                                                                ],
                                                                "effectiveDateTime" => "$formatPeriksaLab",
                                                                "issued" => $formatPeriksaLab,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/10006926841"
                                                                    ],
                                                                    [
                                                                        "reference" => "Organization/$idRS"
                                                                    ]
                                                                ],
                                                                "specimen" => [
                                                                    "reference" => "Specimen/$responseSpecimen->id"
                                                                ],
                                                                "basedOn" => [
                                                                    [
                                                                        "reference" => "ServiceRequest/$idServiceRequest"
                                                                    ]
                                                                ],
                                                                "valueCodeableConcept" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$answerList->code_system",
                                                                            "code" => "$answerList->answer_string_id",
                                                                            "display" => "$answerList->display_text"
                                                                        ]
                                                                    ]
                                                                ]
                                                            ];
                                                        } else {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder"
                                                                    ]
                                                                ],
                                                                "status" => "final",
                                                                "category" => [
                                                                    [
                                                                        "coding" => [
                                                                            [
                                                                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                                "code" => "laboratory",
                                                                                "display" => "Laboratory"
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                "code" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$dataHasil->code_system",
                                                                            "code" => "$dataHasil->code",
                                                                            "display" => "$dataHasil->display"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "subject" => [
                                                                    "reference" => "Patient/$idPasien"
                                                                ],
                                                                "encounter" => [
                                                                    "reference" => "Encounter/$idCounter->encounter_id"
                                                                ],
                                                                "effectiveDateTime" => "$formatPeriksaLab",
                                                                "issued" => $formatPeriksaLab,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/10006926841"
                                                                    ],
                                                                    [
                                                                        "reference" => "Organization/$idRS"
                                                                    ]
                                                                ],
                                                                "specimen" => [
                                                                    "reference" => "Specimen/$responseSpecimen->id"
                                                                ],
                                                                "basedOn" => [
                                                                    [
                                                                        "reference" => "ServiceRequest/$idServiceRequest"
                                                                    ]
                                                                ],
                                                                "valueString" => "Hasil: $DetailLab->nilai, Keterangan: $DetailLab->keterangan, Nilai Rujukan: $DetailLab->nilai_rujukan"
                                                            ];
                                                        }
                                                    } elseif ($dataHasil->tipe_hasil_pemeriksaan == "Ordinal") { //Masih perlu data untuk hasil TBA + ++
                                                        //Get AnswerList Loinc
                                                        $answerList = SatuSehatController::getAnswerLoinc($dataHasil->code, $DetailLab->nilai);
                                                        //Answer List harus sesuai dengan standart jika tidak akan null dan masuk ke jenis narasi
                                                        // dd($answerList);
                                                        if (!empty($answerList)) {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder"
                                                                    ]
                                                                ],
                                                                "status" => "final",
                                                                "category" => [
                                                                    [
                                                                        "coding" => [
                                                                            [
                                                                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                                "code" => "laboratory",
                                                                                "display" => "Laboratory"
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                "code" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$dataHasil->code_system",
                                                                            "code" => "$dataHasil->code",
                                                                            "display" => "$dataHasil->display"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "subject" => [
                                                                    "reference" => "Patient/$idPasien"
                                                                ],
                                                                "encounter" => [
                                                                    "reference" => "Encounter/$idCounter->encounter_id"
                                                                ],
                                                                "effectiveDateTime" => "$formatPeriksaLab",
                                                                "issued" => $formatPeriksaLab,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/10006926841"
                                                                    ],
                                                                    [
                                                                        "reference" => "Organization/$idRS"
                                                                    ]
                                                                ],
                                                                "specimen" => [
                                                                    "reference" => "Specimen/$responseSpecimen->id"
                                                                ],
                                                                "basedOn" => [
                                                                    [
                                                                        "reference" => "ServiceRequest/$idServiceRequest"
                                                                    ]
                                                                ],
                                                                "valueCodeableConcept" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$answerList->code_system",
                                                                            "code" => "$answerList->answer_string_id",
                                                                            "display" => "$answerList->display_text"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "referenceRange" => [
                                                                    [
                                                                        "text" => "$DetailLab->nilai_rujukan"
                                                                    ]
                                                                ]
                                                            ];
                                                        } else {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder"
                                                                    ]
                                                                ],
                                                                "status" => "final",
                                                                "category" => [
                                                                    [
                                                                        "coding" => [
                                                                            [
                                                                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                                "code" => "laboratory",
                                                                                "display" => "Laboratory"
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                "code" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$dataHasil->code_system",
                                                                            "code" => "$dataHasil->code",
                                                                            "display" => "$dataHasil->display"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "subject" => [
                                                                    "reference" => "Patient/$idPasien"
                                                                ],
                                                                "encounter" => [
                                                                    "reference" => "Encounter/$idCounter->encounter_id"
                                                                ],
                                                                "effectiveDateTime" => "$formatPeriksaLab",
                                                                "issued" => $formatPeriksaLab,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/10006926841"
                                                                    ],
                                                                    [
                                                                        "reference" => "Organization/$idRS"
                                                                    ]
                                                                ],
                                                                "specimen" => [
                                                                    "reference" => "Specimen/$responseSpecimen->id"
                                                                ],
                                                                "basedOn" => [
                                                                    [
                                                                        "reference" => "ServiceRequest/$idServiceRequest"
                                                                    ]
                                                                ],
                                                                "valueString" => "Hasil: $DetailLab->nilai, Keterangan: $DetailLab->keterangan, Nilai Rujukan: $DetailLab->nilai_rujukan"
                                                            ];
                                                        }
                                                    } elseif ($dataHasil->tipe_hasil_pemeriksaan == "Quantitative") { //OK tinggal data practioner dan pasien
                                                        $Observation = [
                                                            "resourceType" => "Observation",
                                                            "identifier" => [
                                                                [
                                                                    "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                    "value" => "$cekLab->noorder"
                                                                ]
                                                            ],
                                                            "status" => "final",
                                                            "category" => [
                                                                [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                            "code" => "laboratory",
                                                                            "display" => "Laboratory"
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            "code" => [
                                                                "coding" => [
                                                                    [
                                                                        "system" => "$dataHasil->code_system",
                                                                        "code" => "$dataHasil->code",
                                                                        "display" => "$dataHasil->display"
                                                                    ]
                                                                ]
                                                            ],
                                                            "subject" => [
                                                                "reference" => "Patient/$idPasien"
                                                            ],
                                                            "encounter" => [
                                                                "reference" => "Encounter/$idCounter->encounter_id"
                                                            ],
                                                            "effectiveDateTime" => "$formatPeriksaLab",
                                                            "issued" => $formatPeriksaLab,
                                                            "performer" => [
                                                                [
                                                                    "reference" => "Practitioner/10006926841"
                                                                ],
                                                                [
                                                                    "reference" => "Organization/$idRS"
                                                                ]
                                                            ],
                                                            "specimen" => [
                                                                "reference" => "Specimen/$responseSpecimen->id"
                                                            ],
                                                            "basedOn" => [
                                                                [
                                                                    "reference" => "ServiceRequest/$idServiceRequest"
                                                                ]
                                                            ],
                                                            "valueQuantity" => [
                                                                "value" => floatval($DetailLab->nilai),
                                                                "unit" => "$dataHasil->satuan",
                                                                "system" => "http://unitsofmeasure.org",
                                                                "code" => "$dataHasil->satuan"
                                                            ],
                                                            "referenceRange" => [
                                                                [
                                                                    "text" => "$DetailLab->nilai_rujukan"
                                                                ]
                                                            ]
                                                            // ,
                                                            // "interpretation" => [
                                                            //     [
                                                            //         "coding" => [
                                                            //             [
                                                            //                 "system" => "http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation",
                                                            //                 "code" => "H",
                                                            //                 "display" => "High"
                                                            //             ]
                                                            //         ]
                                                            //     ]
                                                            // ],
                                                            // "referenceRange" => [
                                                            //     [
                                                            //         "low" => [
                                                            //             "value" => 135,
                                                            //             "unit" => "mmol/L",
                                                            //             "system" => "http://unitsofmeasure.org",
                                                            //             "code" => "mmol/L"
                                                            //         ],
                                                            //         "high" => [
                                                            //             "value" => 145,
                                                            //             "unit" => "mmol/L",
                                                            //             "system" => "http://unitsofmeasure.org",
                                                            //             "code" => "mmol/L"
                                                            //         ]
                                                            //     ]
                                                            // ]
                                                        ];
                                                    } elseif ($dataHasil->tipe_hasil_pemeriksaan == "Narative") { //
                                                        // dd($DetailLab, "test", $dataHasil, is_string($DetailLab->nilai), is_numeric($DetailLab->nilai), empty($DetailLab->nilai));

                                                        $Observation = [
                                                            "resourceType" => "Observation",
                                                            "identifier" => [
                                                                [
                                                                    "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                    "value" => "$cekLab->noorder"
                                                                ]
                                                            ],
                                                            "status" => "final",
                                                            "category" => [
                                                                [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                            "code" => "laboratory",
                                                                            "display" => "Laboratory"
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            "code" => [
                                                                "coding" => [
                                                                    [
                                                                        "system" => "$dataHasil->code_system",
                                                                        "code" => "$dataHasil->code",
                                                                        "display" => "$dataHasil->display"
                                                                    ]
                                                                ]
                                                            ],
                                                            "subject" => [
                                                                "reference" => "Patient/$idPasien"
                                                            ],
                                                            "encounter" => [
                                                                "reference" => "Encounter/$idCounter->encounter_id"
                                                            ],
                                                            "effectiveDateTime" => "$formatPeriksaLab",
                                                            "issued" => $formatPeriksaLab,
                                                            "performer" => [
                                                                [
                                                                    "reference" => "Practitioner/10006926841"
                                                                ],
                                                                [
                                                                    "reference" => "Organization/$idRS"
                                                                ]
                                                            ],
                                                            "specimen" => [
                                                                "reference" => "Specimen/$responseSpecimen->id"
                                                            ],
                                                            "basedOn" => [
                                                                [
                                                                    "reference" => "ServiceRequest/$idServiceRequest"
                                                                ]
                                                            ],
                                                            "valueString" => "Hasil: $DetailLab->nilai, Keterangan: $DetailLab->keterangan, Nilai Rujukan: $DetailLab->nilai_rujukan"
                                                        ];
                                                    }

                                                    //Kirim/Create Observation
                                                    SatuSehatController::getTokenSehat();
                                                    $access_token = Session::get('tokenSatuSehat');
                                                    // dd($access_token);
                                                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                                                    try {
                                                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                                                            'headers' => [
                                                                'Authorization' => "Bearer {$access_token}"
                                                            ],
                                                            'json' => $Observation
                                                        ]);
                                                    } catch (BadResponseException $e) {
                                                        // echo $e->getRequest();
                                                        // echo $e->getResponse();
                                                        if ($e->hasResponse()) {
                                                            $response = $e->getResponse();

                                                            // dd($response);
                                                            $test = json_decode($response->getBody());
                                                            // dd($test);
                                                        }

                                                        $message = "Error Kirim Observation Lab id service " . $idServiceRequest;

                                                        Session::flash('error', $message);

                                                        goto Selesai;
                                                    }

                                                    $responseObservation = json_decode($response->getBody());
                                                    // dd($responseObservation);
                                                    if (!empty($responseObservation->id) && $responseObservation->resourceType == "Observation") {
                                                        //Create data di table respone observation lab
                                                        $update = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                                                        $newObservation = new ResponseObservationLab();
                                                        $newObservation->response_lab_satu_sehat_id = $update->id;
                                                        $newObservation->observation_id = $responseObservation->id;
                                                        $newObservation->save();
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        foreach ($detailLab as $DetailLab) {
                                            //cek nilai hasil lab kosong atau masih dalam proses jika tidak lanjut
                                            if ((!empty($DetailLab->nilai)) && (strpos($DetailLab->nilai, "proses") === false)) {
                                                //Seharusnya cek dulu ini paket atau tidak hasilnya juga di foreach tp ini lurus2 aja dulu
                                                //dah diatas ya dicek

                                                $dataHasil = SatuSehatController::getLoinc($DetailLab->kd_jenis_prw);

                                                if (!empty($dataHasil)) {
                                                    if ($dataHasil->tipe_hasil_pemeriksaan == "Nominal") { //Answer List diperlukan
                                                        //Get AnswerList Loinc
                                                        $answerList = SatuSehatController::getAnswerLoinc($dataHasil->code, $DetailLab->nilai);

                                                        //Answer List harus sesuai dengan standart jika tidak akan null dan masuk ke jenis narasi
                                                        if (!empty($answerList)) {
                                                            // dd($answerList);
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder"
                                                                    ]
                                                                ],
                                                                "status" => "final",
                                                                "category" => [
                                                                    [
                                                                        "coding" => [
                                                                            [
                                                                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                                "code" => "laboratory",
                                                                                "display" => "Laboratory"
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                "code" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$dataHasil->code_system",
                                                                            "code" => "$dataHasil->code",
                                                                            "display" => "$dataHasil->display"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "subject" => [
                                                                    "reference" => "Patient/$idPasien"
                                                                ],
                                                                "encounter" => [
                                                                    "reference" => "Encounter/$idCounter->encounter_id"
                                                                ],
                                                                "effectiveDateTime" => "$formatPeriksaLab",
                                                                "issued" => $formatPeriksaLab,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/10006926841"
                                                                    ],
                                                                    [
                                                                        "reference" => "Organization/$idRS"
                                                                    ]
                                                                ],
                                                                "specimen" => [
                                                                    "reference" => "Specimen/$responseSpecimen->id"
                                                                ],
                                                                "basedOn" => [
                                                                    [
                                                                        "reference" => "ServiceRequest/$idServiceRequest"
                                                                    ]
                                                                ],
                                                                "valueCodeableConcept" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$answerList->code_system",
                                                                            "code" => "$answerList->answer_string_id",
                                                                            "display" => "$answerList->display_text"
                                                                        ]
                                                                    ]
                                                                ]
                                                            ];
                                                        } else {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder"
                                                                    ]
                                                                ],
                                                                "status" => "final",
                                                                "category" => [
                                                                    [
                                                                        "coding" => [
                                                                            [
                                                                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                                "code" => "laboratory",
                                                                                "display" => "Laboratory"
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                "code" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$dataHasil->code_system",
                                                                            "code" => "$dataHasil->code",
                                                                            "display" => "$dataHasil->display"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "subject" => [
                                                                    "reference" => "Patient/$idPasien"
                                                                ],
                                                                "encounter" => [
                                                                    "reference" => "Encounter/$idCounter->encounter_id"
                                                                ],
                                                                "effectiveDateTime" => "$formatPeriksaLab",
                                                                "issued" => $formatPeriksaLab,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/10006926841"
                                                                    ],
                                                                    [
                                                                        "reference" => "Organization/$idRS"
                                                                    ]
                                                                ],
                                                                "specimen" => [
                                                                    "reference" => "Specimen/$responseSpecimen->id"
                                                                ],
                                                                "basedOn" => [
                                                                    [
                                                                        "reference" => "ServiceRequest/$idServiceRequest"
                                                                    ]
                                                                ],
                                                                "valueString" => "Hasil: $DetailLab->nilai, Keterangan: $DetailLab->keterangan, Nilai Rujukan: $DetailLab->nilai_rujukan"
                                                            ];
                                                        }
                                                    } elseif ($dataHasil->tipe_hasil_pemeriksaan == "Ordinal") { //Masih perlu data untuk hasil TBA + ++
                                                        //Get AnswerList Loinc
                                                        $answerList = SatuSehatController::getAnswerLoinc($dataHasil->code, $DetailLab->nilai);
                                                        //Answer List harus sesuai dengan standart jika tidak akan null dan masuk ke jenis narasi
                                                        // dd($answerList);
                                                        if (!empty($answerList)) {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder"
                                                                    ]
                                                                ],
                                                                "status" => "final",
                                                                "category" => [
                                                                    [
                                                                        "coding" => [
                                                                            [
                                                                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                                "code" => "laboratory",
                                                                                "display" => "Laboratory"
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                "code" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$dataHasil->code_system",
                                                                            "code" => "$dataHasil->code",
                                                                            "display" => "$dataHasil->display"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "subject" => [
                                                                    "reference" => "Patient/$idPasien"
                                                                ],
                                                                "encounter" => [
                                                                    "reference" => "Encounter/$idCounter->encounter_id"
                                                                ],
                                                                "effectiveDateTime" => "$formatPeriksaLab",
                                                                "issued" => $formatPeriksaLab,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/10006926841"
                                                                    ],
                                                                    [
                                                                        "reference" => "Organization/$idRS"
                                                                    ]
                                                                ],
                                                                "specimen" => [
                                                                    "reference" => "Specimen/$responseSpecimen->id"
                                                                ],
                                                                "basedOn" => [
                                                                    [
                                                                        "reference" => "ServiceRequest/$idServiceRequest"
                                                                    ]
                                                                ],
                                                                "valueCodeableConcept" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$answerList->code_system",
                                                                            "code" => "$answerList->answer_string_id",
                                                                            "display" => "$answerList->display_text"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "referenceRange" => [
                                                                    [
                                                                        "text" => "$DetailLab->nilai_rujukan"
                                                                    ]
                                                                ]
                                                            ];
                                                        } else {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder"
                                                                    ]
                                                                ],
                                                                "status" => "final",
                                                                "category" => [
                                                                    [
                                                                        "coding" => [
                                                                            [
                                                                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                                "code" => "laboratory",
                                                                                "display" => "Laboratory"
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                "code" => [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "$dataHasil->code_system",
                                                                            "code" => "$dataHasil->code",
                                                                            "display" => "$dataHasil->display"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "subject" => [
                                                                    "reference" => "Patient/$idPasien"
                                                                ],
                                                                "encounter" => [
                                                                    "reference" => "Encounter/$idCounter->encounter_id"
                                                                ],
                                                                "effectiveDateTime" => "$formatPeriksaLab",
                                                                "issued" => $formatPeriksaLab,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/10006926841"
                                                                    ],
                                                                    [
                                                                        "reference" => "Organization/$idRS"
                                                                    ]
                                                                ],
                                                                "specimen" => [
                                                                    "reference" => "Specimen/$responseSpecimen->id"
                                                                ],
                                                                "basedOn" => [
                                                                    [
                                                                        "reference" => "ServiceRequest/$idServiceRequest"
                                                                    ]
                                                                ],
                                                                "valueString" => "Hasil: $DetailLab->nilai, Keterangan: $DetailLab->keterangan, Nilai Rujukan: $DetailLab->nilai_rujukan"
                                                            ];
                                                        }
                                                    } elseif ($dataHasil->tipe_hasil_pemeriksaan == "Quantitative") { //OK tinggal data practioner dan pasien
                                                        $Observation = [
                                                            "resourceType" => "Observation",
                                                            "identifier" => [
                                                                [
                                                                    "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                    "value" => "$cekLab->noorder"
                                                                ]
                                                            ],
                                                            "status" => "final",
                                                            "category" => [
                                                                [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                            "code" => "laboratory",
                                                                            "display" => "Laboratory"
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            "code" => [
                                                                "coding" => [
                                                                    [
                                                                        "system" => "$dataHasil->code_system",
                                                                        "code" => "$dataHasil->code",
                                                                        "display" => "$dataHasil->display"
                                                                    ]
                                                                ]
                                                            ],
                                                            "subject" => [
                                                                "reference" => "Patient/$idPasien"
                                                            ],
                                                            "encounter" => [
                                                                "reference" => "Encounter/$idCounter->encounter_id"
                                                            ],
                                                            "effectiveDateTime" => "$formatPeriksaLab",
                                                            "issued" => $formatPeriksaLab,
                                                            "performer" => [
                                                                [
                                                                    "reference" => "Practitioner/10006926841"
                                                                ],
                                                                [
                                                                    "reference" => "Organization/$idRS"
                                                                ]
                                                            ],
                                                            "specimen" => [
                                                                "reference" => "Specimen/$responseSpecimen->id"
                                                            ],
                                                            "basedOn" => [
                                                                [
                                                                    "reference" => "ServiceRequest/$idServiceRequest"
                                                                ]
                                                            ],
                                                            "valueQuantity" => [
                                                                "value" => floatval($DetailLab->nilai),
                                                                "unit" => "$dataHasil->satuan",
                                                                "system" => "http://unitsofmeasure.org",
                                                                "code" => "$dataHasil->satuan"
                                                            ],
                                                            "referenceRange" => [
                                                                [
                                                                    "text" => "$DetailLab->nilai_rujukan"
                                                                ]
                                                            ]
                                                            // ,
                                                            // "interpretation" => [
                                                            //     [
                                                            //         "coding" => [
                                                            //             [
                                                            //                 "system" => "http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation",
                                                            //                 "code" => "H",
                                                            //                 "display" => "High"
                                                            //             ]
                                                            //         ]
                                                            //     ]
                                                            // ],
                                                            // "referenceRange" => [
                                                            //     [
                                                            //         "low" => [
                                                            //             "value" => 135,
                                                            //             "unit" => "mmol/L",
                                                            //             "system" => "http://unitsofmeasure.org",
                                                            //             "code" => "mmol/L"
                                                            //         ],
                                                            //         "high" => [
                                                            //             "value" => 145,
                                                            //             "unit" => "mmol/L",
                                                            //             "system" => "http://unitsofmeasure.org",
                                                            //             "code" => "mmol/L"
                                                            //         ]
                                                            //     ]
                                                            // ]
                                                        ];
                                                    } elseif ($dataHasil->tipe_hasil_pemeriksaan == "Narative") { //
                                                        // dd($DetailLab, "test", $dataHasil, is_string($DetailLab->nilai), is_numeric($DetailLab->nilai), empty($DetailLab->nilai));

                                                        $Observation = [
                                                            "resourceType" => "Observation",
                                                            "identifier" => [
                                                                [
                                                                    "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                    "value" => "$cekLab->noorder"
                                                                ]
                                                            ],
                                                            "status" => "final",
                                                            "category" => [
                                                                [
                                                                    "coding" => [
                                                                        [
                                                                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                                                            "code" => "laboratory",
                                                                            "display" => "Laboratory"
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            "code" => [
                                                                "coding" => [
                                                                    [
                                                                        "system" => "$dataHasil->code_system",
                                                                        "code" => "$dataHasil->code",
                                                                        "display" => "$dataHasil->display"
                                                                    ]
                                                                ]
                                                            ],
                                                            "subject" => [
                                                                "reference" => "Patient/$idPasien"
                                                            ],
                                                            "encounter" => [
                                                                "reference" => "Encounter/$idCounter->encounter_id"
                                                            ],
                                                            "effectiveDateTime" => "$formatPeriksaLab",
                                                            "issued" => $formatPeriksaLab,
                                                            "performer" => [
                                                                [
                                                                    "reference" => "Practitioner/10006926841"
                                                                ],
                                                                [
                                                                    "reference" => "Organization/$idRS"
                                                                ]
                                                            ],
                                                            "specimen" => [
                                                                "reference" => "Specimen/$responseSpecimen->id"
                                                            ],
                                                            "basedOn" => [
                                                                [
                                                                    "reference" => "ServiceRequest/$idServiceRequest"
                                                                ]
                                                            ],
                                                            "valueString" => "Hasil: $DetailLab->nilai, Keterangan: $DetailLab->keterangan, Nilai Rujukan: $DetailLab->nilai_rujukan"
                                                        ];
                                                    }

                                                    //Kirim/Create Observation
                                                    SatuSehatController::getTokenSehat();
                                                    $access_token = Session::get('tokenSatuSehat');
                                                    // dd($access_token);
                                                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                                                    try {
                                                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                                                            'headers' => [
                                                                'Authorization' => "Bearer {$access_token}"
                                                            ],
                                                            'json' => $Observation
                                                        ]);
                                                    } catch (BadResponseException $e) {
                                                        // echo $e->getRequest();
                                                        // echo $e->getResponse();
                                                        if ($e->hasResponse()) {
                                                            $response = $e->getResponse();

                                                            // dd($response);
                                                            $test = json_decode($response->getBody());
                                                            // dd($test);
                                                        }

                                                        $message = "Error Kirim Observation Lab id service " . $idServiceRequest;

                                                        Session::flash('error', $message);

                                                        goto KirimPasienLain;
                                                    }

                                                    $responseObservation = json_decode($response->getBody());
                                                    // dd($responseObservation);
                                                    if (!empty($responseObservation->id) && $responseObservation->resourceType == "Observation") {
                                                        //Create data di table respone observation lab
                                                        $update = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                                                        $newObservation = new ResponseObservationLab();
                                                        $newObservation->response_lab_satu_sehat_id = $update->id;
                                                        $newObservation->observation_id = $responseObservation->id;
                                                        $newObservation->save();
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    // dd($pasienLab, $cekLab, $idCounter->encounter_id, $cekLab->dokter_perujuk, $dokterPerujuk);

                                } else {
                                    dd($responseSpecimen);
                                }

                                //Diagnostic Report
                                //Cek dulu Observasinya ada berapa hasil
                                $cekID = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                                $cekObservation = ResponseObservationLab::where('response_lab_satu_sehat_id', $cekID->id)->get();
                                // dd($cekObservation);

                                if (count($cekObservation) > 0) {

                                    $arrObservation = array($cekObservation);
                                    // $arrObservation = array();
                                    $noObservation = 0;

                                    for ($i = 0; $i < $cekObservation->count(); $i++) {
                                        ++$noObservation;
                                        $idOb = $arrObservation[0][$i]['observation_id'];
                                        $tambah[$i] = array(
                                            'id' => "$noObservation",
                                            'reference' => "Observation/$idOb",
                                        );
                                    }

                                    if (empty($tambah)) {
                                        dd($cekObservation, "cek");
                                    }

                                    $Report = [
                                        "resourceType" => "DiagnosticReport",
                                        "identifier" => [
                                            [
                                                "system" => "http://sys-ids.kemkes.go.id/diagnostic/$idRS/lab",
                                                "use" => "official",
                                                "value" => "$cekLab->noorder"
                                            ]
                                        ],
                                        "status" => "final",
                                        "category" => [
                                            [
                                                "coding" => [
                                                    [
                                                        "system" => "http://terminology.hl7.org/CodeSystem/v2-0074",
                                                        "code" => "CH",
                                                        "display" => "Chemistry"
                                                    ]
                                                ]
                                            ]
                                        ],
                                        "code" => [
                                            "coding" => [
                                                [
                                                    "system" => "$mappingLoinc->code_system",
                                                    "code" => "$mappingLoinc->code",
                                                    "display" => "$mappingLoinc->display"
                                                ]
                                            ]
                                        ],
                                        "subject" => [
                                            "reference" => "Patient/$idPasien"
                                        ],
                                        "encounter" => [
                                            "reference" => "Encounter/$idCounter->encounter_id"
                                        ],
                                        "effectiveDateTime" => $formatPeriksaLab,
                                        "issued" => $formatPeriksaLab,
                                        "performer" => [
                                            [
                                                "reference" => "Practitioner/10006926841"
                                            ],
                                            [
                                                "reference" => "Organization/$idRS"
                                            ]
                                        ],
                                        // "result" => [
                                        //     [
                                        //         "id" => "1",
                                        //         "reference" => "Observation/b04db6af-2d31-4509-9a00-9b5fc073b389"
                                        //     ],
                                        //     [
                                        //         "id" => "2",
                                        //         "reference" => "Observation/150bc626-7d79-4e38-a37a-38ab8e1a23d2"
                                        //     ],
                                        //     [
                                        //         "id" => "3",
                                        //         "reference" => "Observation/26800d31-b6fd-43fa-83e2-4750410c789c"
                                        //     ]
                                        // ],
                                        "result" => $tambah,
                                        "specimen" => [
                                            [
                                                "reference" => "Specimen/$responseSpecimen->id"
                                            ]
                                        ],
                                        "basedOn" => [
                                            [
                                                "reference" => "ServiceRequest/$idServiceRequest"
                                            ]
                                        ]
                                        // ,
                                        // "conclusion" => "Hipernatremia, Hiperkloremia, Hipokalemia"
                                    ];

                                    // array_push($Report['result'], $tambah);
                                    // dd($Report);
                                    //Kirim/Create Diagnostic Report
                                    SatuSehatController::getTokenSehat();
                                    $access_token = Session::get('tokenSatuSehat');
                                    // dd($access_token);
                                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                                    try {
                                        $response = $client->request('POST', 'fhir-r4/v1/DiagnosticReport', [
                                            'headers' => [
                                                'Authorization' => "Bearer {$access_token}"
                                            ],
                                            'json' => $Report
                                        ]);
                                    } catch (BadResponseException $e) {
                                        // echo $e->getRequest();
                                        // echo $e->getResponse();
                                        if ($e->hasResponse()) {
                                            $response = $e->getResponse();

                                            // dd($response);
                                            $test = json_decode($response->getBody());
                                            dd($test);
                                        }

                                        $message = "Error Kirim Report lab id service request " . $idServiceRequest;

                                        Session::flash('error', $message);

                                        goto KirimPasienLain;
                                    }

                                    $responseReport = json_decode($response->getBody());
                                    // dd($responseReport->id);
                                    if (!empty($responseReport->id) && $responseReport->resourceType == "DiagnosticReport") {
                                        //Update data di table respone lab
                                        $update = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                                        $update->report_id = $responseReport->id;
                                        $update->save();

                                        ++$loop;
                                    }
                                }
                            }
                        }

                        KirimPasienLain:
                    }
                }
            }
        }

        Selesai:
        $dataLog = ResponseLabSatuSehat::whereDate('tgl_registrasi', $pasien_tanggal)->get();

        // dd($dataLog);

        return view('satu_sehat.client_rujuklab', compact('dataLog'));
    }

    public static function tokenSehat()
    {
        $setting = Setting::where('nama', 'satusehat')->first();
        // dd($setting);
        session()->put('base_url', $setting->base_url);
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => $setting->base_url]);
            $response = $client->request('POST', 'oauth2/v1/accesstoken?grant_type=client_credentials', [
                'headers' => [
                    'Content-Type' => "application/x-www-form-urlencoded"
                ],
                'form_params' => [
                    'client_id' => $setting->satker,
                    'client_secret' => $setting->key,
                ]
            ]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }

            // $id = Crypt::encrypt($id);
            // dd($test);
            Session::flash('error', $test->message);

            return redirect()->back()->withInput();
        }

        $data = json_decode($response->getBody());

        // dd($data, $data->status);

        if ($data->status == "approved") {
            session()->put('tokenSatuSehat', $data->access_token);

            $expired = Carbon::now()->addHour();
            session()->put('expiredSatuSehat', $expired);
        }
        // dd(session('tokenSatuSehat'), session('expiredSatuSehat'), $expired);
    }

    public static function getTokenSehat()
    {
        $now = Carbon::now();

        if ((new Carbon(Session::get('expiredSatuSehat')) <= $now) or (empty(Session::get('tokenSatuSehat')))) {
            SatuSehatController::tokenSehat();
        }
        // else {
        //     dd('false', $now, Session::get('expiredSatuSehat'));
        // }

        // dd(Session::get('tokenSatuSehat'));
    }

    public static function patientSehat($id)
    {
        // $nik = $id;

        if (is_numeric($id)) {
            $cek_lokal = PasienSehat::where('nik', $id)->first();
            // dd($cek_lokal);

            if (!empty($cek_lokal)) {
                return $cek_lokal->satu_sehat_id;
            } else {
                // $cek = DB::connection('mysqlkhanza')->table('pasien')
                //     ->select(
                //         'pasien.nm_pasien',
                //         'pasien.no_ktp',
                //         'pasien.tgl_lahir'
                //     )
                //     ->where('pasien.no_ktp', $id)
                //     ->first();
                // dd($cek);
                SatuSehatController::getTokenSehat();
                $access_token = Session::get('tokenSatuSehat');
                // dd($access_token);
                try {
                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                    $response = $client->request('GET', 'fhir-r4/v1/Patient?identifier=https://fhir.kemkes.go.id/id/nik|' . $id, [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}"
                        ]
                    ]);
                    // $response = $client->request('GET', 'fhir-r4/v1/Patient?name=' . $cek->nm_pasien . '&birthdate=' . $cek->tgl_lahir . '&identifier=https://fhir.kemkes.go.id/id/nik|' . $id, [
                    //     'headers' => [
                    //         'Authorization' => "Bearer {$access_token}"
                    //     ]
                    // ]);
                } catch (RequestException $e) {
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();
                        $test = json_decode((string) $response->getBody());
                        // dd($test->fault);
                        // $id = Crypt::encrypt($id);
                        Session::flash('error', 'Ambil id pasien gagal!');

                        // return redirect()->back()->withInput();
                        goto SimpanError;
                    } else {
                        Session::flash('error', 'Server tidak respon!');

                        // return redirect()->back()->withInput();
                        return null;
                    }
                }

                $data = json_decode($response->getBody());
                // $data = $data->entry;


                if ($data->total == 1) {
                    // dd($data, 'pasien');
                    if (!empty($data->entry)) {
                        foreach ($data->entry as $responseData) {
                            // dd($responseData->resource);
                            $data = $responseData->resource->id;
                        }

                        $simpanID = new PasienSehat();
                        $simpanID->nik = $id;
                        $simpanID->satu_sehat_id = $data;
                        $simpanID->save();

                        return $data;
                        // dd($data);
                    } else {
                        goto SimpanError;
                    }
                } else {
                    SimpanError:
                    $cek = LogErrorSatuSehat::where('subject', 'Pasien')
                        ->where('keterangan', 'like', "%$id%")
                        ->count();

                    if ($cek == 0) {
                        $error = new LogErrorSatuSehat();
                        $error->subject = 'Pasien';
                        $error->keterangan = $id . ' tidak ditemukan di Satu Sehat';
                        $error->save();

                        return null;
                    }
                }
            }
        } else {
            return null;
        }
    }

    public static function practitioner($id)
    {
        // $nik = $id;
        if (is_numeric($id)) {
            $cek_lokal = PraktisiSehat::where('nik', $id)->first();

            if (!empty($cek_lokal)) {
                return $cek_lokal->satu_sehat_id;
            } else {
                SatuSehatController::getTokenSehat();
                $access_token = Session::get('tokenSatuSehat');
                // dd($access_token);
                try {
                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                    $response = $client->request('GET', 'fhir-r4/v1/Practitioner?identifier=https://fhir.kemkes.go.id/id/nik|' . $id, [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}"
                        ]
                    ]);
                } catch (ClientException $e) {
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();
                        $test = json_decode((string) $response->getBody());
                    } else {
                        Session::flash('error', 'Server tidak respon!');

                        // return redirect()->back()->withInput();
                        return null;
                    }
                    // dd($test->fault);
                    // $id = Crypt::encrypt($id);
                    Session::flash('error', 'Ambil respon practitioner gagal!');

                    // return redirect()->back()->withInput();
                    goto ErrorPractitioner;
                }

                $data = json_decode($response->getBody());
                // $data = $data->entry;

                // dd($data->resource->id);
                if ($data->total == 1) {
                    // dd($data, 'practitioner');

                    foreach ($data->entry as $responseData) {
                        $data = $responseData->resource->id;
                        // dd($responseData->resource);
                    }

                    $simpanID = new PraktisiSehat();
                    $simpanID->nik = $id;
                    $simpanID->satu_sehat_id = $data;
                    $simpanID->save();

                    return $data;
                } else {
                    ErrorPractitioner:
                    $cek = LogErrorSatuSehat::where('subject', 'Praktitioner')
                        ->where('keterangan', 'like', "%$id%")
                        ->count();

                    if ($cek < 1) {
                        $error = new LogErrorSatuSehat();
                        $error->subject = 'Praktitioner';
                        $error->keterangan = $id . ' tidak ditemukan di Satu Sehat';
                        $error->save();

                        return null;
                    }
                }
            }
        } else {
            // $cek = LogErrorSatuSehat::where('subject', 'Praktitioner')
            //     ->where('keterangan', 'like', "%$id%")
            //     ->where('created_at', Carbon::now())
            //     ->count();

            // if ($cek == 0) {
            //     $error = new LogErrorSatuSehat();
            //     $error->subject = 'Praktitioner';
            //     $error->keterangan = $id . ' terindikasi ada kesalahan data';
            //     $error->save();

            //     return null;
            // }

            return null;
        }
    }

    public static function getIdPoli($id)
    {
        $data = DB::connection('mysqlkhanza')->table('fhir_poliklinik')
            ->select(
                'fhir_poliklinik.kd_poli',
                'fhir_poliklinik.id_ihs'
            )
            ->where('fhir_poliklinik.kd_poli', $id)
            ->first();
        // dd($data);
        if (!empty($data)) {
            return $data->id_ihs;
        } else {
            $cekdata = LogErrorSatuSehat::where('subject', "Lokasi")
                ->where('keterangan', 'like', "%$id")
                ->whereDate('created_at', Carbon::now())
                ->get();

            if (empty($cekdata)) {
                $error = new LogErrorSatuSehat();
                $error->subject = 'Lokasi';
                $error->keterangan = $id . ' tidak ditemukan di Satu Sehat';
                $error->save();
            }

            return null;
        }
    }

    public function getDiagnosisPrimerRalan($id)
    {
        $data = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
            ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            ->select(
                'diagnosa_pasien.no_rawat',
                'diagnosa_pasien.kd_penyakit',
                'diagnosa_pasien.status',
                'diagnosa_pasien.prioritas',
                'penyakit.nm_penyakit'
            )
            ->where('diagnosa_pasien.no_rawat', $id)
            ->where('diagnosa_pasien.status', 'Ralan')
            ->where('diagnosa_pasien.prioritas', '1')
            ->first();
        // dd($data->kd_penyakit);

        if (!empty($data)) {
            //cek jika kd pengakit = kontrol /z09.8
            // dd($data->kd_penyakit);
            if (stripos($data->kd_penyakit, "Z09") !== false) {
                $data2 = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
                    ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
                    ->select(
                        'diagnosa_pasien.no_rawat',
                        'diagnosa_pasien.kd_penyakit',
                        'diagnosa_pasien.status',
                        'diagnosa_pasien.prioritas',
                        'penyakit.nm_penyakit'
                    )
                    ->where('diagnosa_pasien.no_rawat', $id)
                    ->where('diagnosa_pasien.status', 'Ralan')
                    ->where('diagnosa_pasien.prioritas', '2')
                    ->first();

                // dd($data, $id);
            }

            $cek = LogErrorSatuSehat::where('subject', 'Diagnosa Primer Pasien')
                ->where('keterangan', 'like', "%$id%")
                ->delete();

            if (!empty($data2))
                return $data2;
            else {
                return $data;
            }
        } else {
            $cek = LogErrorSatuSehat::where('subject', 'Diagnosa Primer Pasien')
                ->where('keterangan', 'like', "%$id%")
                ->whereDate('created_at', Carbon::now())
                ->count();

            if ($cek < 1) {
                $error = new LogErrorSatuSehat();
                $error->subject = 'Diagnosa Primer Pasien';
                $error->keterangan = $id . ' tidak ditemukan di database';
                $error->save();
            }

            return null;
        }

        // return $data->id_ihs;
    }

    public function getDiagnosisSekunderRalan($id)
    {
        $data = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
            ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            ->select(
                'diagnosa_pasien.no_rawat',
                'diagnosa_pasien.kd_penyakit',
                'diagnosa_pasien.status',
                'diagnosa_pasien.prioritas',
                'penyakit.nm_penyakit'
            )
            ->where('diagnosa_pasien.no_rawat', $id)
            ->where('diagnosa_pasien.status', 'Ralan')
            ->where('diagnosa_pasien.prioritas', '1')
            ->first();

        $diagnosaExclude = ['z88.8'];

        if (!empty($data)) {
            if ($data->kd_penyakit != "Z09.8") {
                // dd('masuk', $data->kd_penyakit);
                $data2 = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
                    ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
                    ->select(
                        'diagnosa_pasien.no_rawat',
                        'diagnosa_pasien.kd_penyakit',
                        'diagnosa_pasien.status',
                        'diagnosa_pasien.prioritas',
                        'penyakit.nm_penyakit'
                    )
                    ->where('diagnosa_pasien.no_rawat', $id)
                    ->where('diagnosa_pasien.status', 'Ralan')
                    // ->where('diagnosa_pasien.prioritas', '2')
                    ->orderBy('prioritas', 'ASC')
                    ->skip(1)  // Melewatkan 5 baris pertama
                    ->limit(10)
                    ->get();

                // dd($data2);
                foreach ($data2 as $list) {
                    if (in_array($list->kd_penyakit, $diagnosaExclude)) {
                        goto next;
                    } else {
                        return $list;
                    }
                    next:
                }

                // if (!empty($data2)) {
                //     return $data2;
                // } else {
                return null;
                // }
            } else {
                $data2 = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
                    ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
                    ->select(
                        'diagnosa_pasien.no_rawat',
                        'diagnosa_pasien.kd_penyakit',
                        'diagnosa_pasien.status',
                        'diagnosa_pasien.prioritas',
                        'penyakit.nm_penyakit'
                    )
                    ->where('diagnosa_pasien.no_rawat', $id)
                    ->where('diagnosa_pasien.status', 'Ralan')
                    // ->where('diagnosa_pasien.prioritas', '3')
                    ->orderBy('prioritas', 'ASC')
                    ->skip(2)  // Melewatkan 5 baris pertama
                    ->limit(10)
                    ->get();

                // dd($data2);
                foreach ($data2 as $list) {
                    if (in_array($list->kd_penyakit, $diagnosaExclude)) {
                        goto next2;
                    } else {
                        return $list;
                    }
                    next2:
                }

                // if (!empty($data2)) {
                //     return $data2;
                // } else {
                return null;
                // }
            }
        } else {
            return null;
        }
    }

    public function getVital($id)
    {
        $data = DB::connection('mysqlkhanza')->table('pemeriksaan_ralan')
            ->select(
                'pemeriksaan_ralan.no_rawat',
                'pemeriksaan_ralan.suhu_tubuh',
                'pemeriksaan_ralan.tgl_perawatan',
                'pemeriksaan_ralan.jam_rawat',
                'pemeriksaan_ralan.tensi',
                'pemeriksaan_ralan.nadi',
                'pemeriksaan_ralan.respirasi'
            )
            ->where('pemeriksaan_ralan.no_rawat', $id)
            ->first();

        return $data;
    }

    public function getVitalMcu($id)
    {
        $data = DB::connection('mysqlkhanza')->table('penilaian_mcu')
            ->select(
                'penilaian_mcu.no_rawat',
                'penilaian_mcu.suhu',
                'penilaian_mcu.tanggal',
                'penilaian_mcu.td',
                'penilaian_mcu.nadi',
                'penilaian_mcu.rr'
            )
            ->where('pemeriksaan_ralan.no_rawat', $id)
            ->first();

        return $data;
    }

    public function getProcedureRalan($id)
    {
        // $id = '2022/09/05/000013';
        $data = DB::connection('mysqlkhanza')->table('prosedur_pasien')
            ->join('icd9', 'icd9.kode', '=', 'prosedur_pasien.kode')
            ->select(
                'prosedur_pasien.no_rawat',
                'prosedur_pasien.kode',
                'prosedur_pasien.status',
                'prosedur_pasien.prioritas',
                'icd9.deskripsi_panjang'
            )
            ->where('prosedur_pasien.no_rawat', $id)
            ->where('prosedur_pasien.status', 'Ralan')
            ->where('prosedur_pasien.prioritas', '1')
            ->first();
        // dd($data);

        if (!empty($data)) {
            //Kode yang diexclude
            $dikecualikan = ['89.071'];

            if (in_array($data->kode, $dikecualikan)) {
                $data = DB::connection('mysqlkhanza')->table('prosedur_pasien')
                    ->join('icd9', 'icd9.kode', '=', 'prosedur_pasien.kode')
                    ->select(
                        'prosedur_pasien.no_rawat',
                        'prosedur_pasien.kode',
                        'prosedur_pasien.status',
                        'prosedur_pasien.prioritas',
                        'icd9.deskripsi_panjang'
                    )
                    ->where('prosedur_pasien.no_rawat', $id)
                    ->where('prosedur_pasien.status', 'Ralan')
                    ->where('prosedur_pasien.prioritas', '2')
                    ->first();
            }

            return $data;
        } else {
            return null;
        }
    }

    public function getDiet($id, $idt)
    {
        $data = DB::connection('mysqlkhanza')->table('asuhan_gizi')
            ->select(
                'asuhan_gizi.no_rawat',
                'asuhan_gizi.tanggal',
                'asuhan_gizi.monitoring_evaluasi'
            )
            ->where('asuhan_gizi.no_rawat', $id)
            ->where('asuhan_gizi.tanggal', $idt)
            ->first();

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }

    public function getResepObat($id)
    {
        $data = DB::connection('mysqlkhanza')->table('resep_obat')
            ->select(
                'resep_obat.no_resep',
                'resep_obat.no_rawat',
                'resep_obat.kd_dokter',
                'resep_obat.status',
                'resep_obat.tgl_perawatan as tgl_permintaan',
                'resep_obat.jam as jam_permintaan',
                'resep_obat.tgl_penyerahan',
                'resep_obat.jam_penyerahan'
            )
            ->where('resep_obat.no_rawat', $id)
            ->where('resep_obat.status', 'ralan')
            ->where('resep_obat.tgl_penyerahan', '!=', '0000-00-00')
            ->first();

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }

    public function getListObat($resep)
    {
        $data = DB::connection('mysqlkhanza')->table('resep_dokter')
            ->join('databarang', 'databarang.kode_brng', '=', 'resep_dokter.kode_brng')
            ->select(
                'resep_dokter.no_resep',
                'resep_dokter.kode_brng',
                'databarang.nama_brng',
                'databarang.expire',
                'resep_dokter.jml',
                'resep_dokter.aturan_pakai'
            )
            ->where('resep_dokter.no_resep', $resep)
            ->get();

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }

    public function getListObatRacik($resep)
    {
        $data = DB::connection('mysqlkhanza')->table('resep_dokter_racikan')
            ->join('metode_racik', 'metode_racik.kd_racik', '=', 'resep_dokter_racikan.kd_racik')
            ->select(
                'resep_dokter_racikan.no_resep',
                'resep_dokter_racikan.no_racik',
                'resep_dokter_racikan.jml_dr',
                'resep_dokter_racikan.aturan_pakai',
                'resep_dokter_racikan.keterangan',
                'metode_racik.kd_racik',
                'metode_racik.nm_racik'
            )
            ->where('resep_dokter_racikan.no_resep', $resep)
            ->get();

        // dd($data);

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }

    public function getIdObat($kd_obat)
    {
        $data = DB::connection('mysqlkhanza')->table('fhir_farmasi')
            ->join('fhir_master_medicationform', 'fhir_master_medicationform.kd_medication', '=', 'fhir_farmasi.kode_medication')
            ->join('fhir_master_ucum', 'fhir_master_ucum.kd_ucum', '=', 'fhir_farmasi.kode_ucum')
            ->join('fhir_master_ingredient', 'fhir_master_ingredient.kd_ingredient', '=', 'fhir_farmasi.kode_ingredient')
            ->join('fhir_master_route', 'fhir_master_route.kd_route', '=', 'fhir_farmasi.kode_route')
            ->select(
                'fhir_farmasi.kode_brng',
                'fhir_farmasi.id_ihs',
                'fhir_farmasi.kode_medication',
                'fhir_master_medicationform.display as form_display',
                'fhir_master_medicationform.coding_system as form_coding_system',
                'fhir_farmasi.kode_ucum',
                'fhir_master_ucum.system as ucum_system',
                'fhir_master_ucum.name as ucum_name',
                'fhir_farmasi.kode_ingredient',
                'fhir_master_ingredient.display as ingredient_display',
                'fhir_master_ingredient.system as ingredient_system',
                'fhir_farmasi.kode_route',
                'fhir_master_route.display as route_display',
                'fhir_master_route.keterangan as route_keterangan',
                'fhir_master_route.system as route_system'
            )
            ->where('fhir_farmasi.kode_brng', $kd_obat)
            ->first();

        if (!empty($data)) {
            return $data;
            // dd($kd_obat, 'dapat');
        } else {
            // dd($kd_obat, 'tidak');
            $cari = LogErrorSatuSehat::where('Subject', 'Farmasi')
                ->where('keterangan', 'like', "%$kd_obat%")
                ->whereDate('created_at', Carbon::now())
                ->count();
            // dd($cari);
            if ($cari < 1) {
                $error = new LogErrorSatuSehat();
                $error->subject = 'Farmasi';
                $error->keterangan = $kd_obat . ' Kode Obat tidak ditemukan di Mapping Satu Sehat';
                $error->save();
            }

            return null;
        }
    }

    public function getDetailRacikan($noResep)
    {
        $data = DB::connection('mysqlkhanza')->table('resep_dokter_racikan_detail')
            ->join('databarang', 'databarang.kode_brng', '=', 'resep_dokter_racikan_detail.kode_brng')
            ->select(
                'resep_dokter_racikan_detail.no_resep',
                'resep_dokter_racikan_detail.no_racik',
                'resep_dokter_racikan_detail.kode_brng',
                'resep_dokter_racikan_detail.kandungan',
                'resep_dokter_racikan_detail.jml',
                'databarang.nama_brng'
            )
            ->where('resep_dokter_racikan_detail.no_resep', $noResep)
            ->get();

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }

    public function getRouteRacik($kd_racik)
    {
        $data = DB::connection('mysqlkhanza')->table('fhir_racik')
            ->join('fhir_master_route', 'fhir_master_route.kd_route', '=', 'fhir_racik.kd_route')
            ->select(
                'fhir_racik.metode',
                'fhir_racik.kd_route',
                'fhir_master_route.display',
                'fhir_master_route.keterangan',
                'fhir_master_route.system'
            )
            ->where('fhir_racik.metode', $kd_racik)
            ->first();

        // dd($data, $id);

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }

    public function obatDiberikan($idRawat, $idObat)
    {
        $data = DB::connection('mysqlkhanza')->table('detail_pemberian_obat')
            ->select(
                'detail_pemberian_obat.no_rawat',
                'detail_pemberian_obat.kode_brng',
                'detail_pemberian_obat.jml',
                'detail_pemberian_obat.tgl_perawatan',
                'detail_pemberian_obat.jam'
            )
            ->where('detail_pemberian_obat.no_rawat', $idRawat)
            ->where('detail_pemberian_obat.kode_brng', $idObat)
            ->first();

        $aturan = DB::connection('mysqlkhanza')->table('aturan_pakai')
            ->select(
                'aturan_pakai.no_rawat',
                'aturan_pakai.kode_brng',
                'aturan_pakai.aturan'
            )
            ->where('aturan_pakai.no_rawat', $idRawat)
            ->where('aturan_pakai.kode_brng', $idObat)
            ->first();

        // if (empty($data)) {
        //     return 0;
        // }

        // if (empty($aturan)) {
        //     return 0;
        // }

        return array($data, $aturan);
    }

    public function getMedicationForm($id)
    {
        $data = DB::connection('mysqlkhanza')->table('fhir_racik')
            ->join('fhir_master_medicationform', 'fhir_master_medicationform.kd_medication', '=', 'fhir_racik.kode_medication')
            ->select(
                'fhir_racik.metode',
                'fhir_racik.kd_ingredient',
                'fhir_racik.kd_ucum',
                'fhir_racik.kode_medication',
                'fhir_master_medicationform.display',
                'fhir_master_medicationform.coding_system'
            )
            ->where('fhir_racik.metode', $id)
            ->first();

        // dd($data, $id);

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }

    public static function getEncounterId($no_rawat)
    {
        $data = ResponseSatuSehat::where('noRawat', $no_rawat)
            ->first();

        // dd($data);

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }

    public function getLoinc($id)
    {
        $data = DB::connection('mysqlkhanza')->table('fhir_lab')
            // ->join('fhir_master_loinc', 'fhir_master_loinc.kd_loinc', '=', 'fhir_lab.kd_loinc')
            ->select(
                'fhir_lab.kd_loinc',
                'fhir_lab.kd_snomed',
                'fhir_lab.kd_jenis_prw'
            )
            ->where('fhir_lab.kd_jenis_prw', "$id")
            ->first();

        if (!empty($data)) {
            $getData = MasterLoinc::where('kd_loinc', $data->kd_loinc)
                ->first();
        } else {
            $getData = null;
        }

        // dd($data, $id, $getData);

        if (!empty($getData)) {
            return $getData;
        } else {
            $cek = LogErrorSatuSehat::where('subject', 'Lab')
                ->where('keterangan', 'like', '%' . $id . '%')
                ->whereDate('created_at', Carbon::now())
                ->get();
            if ($cek->count() < 1) {
                $error = new LogErrorSatuSehat();
                $error->subject = 'Lab';
                $error->keterangan = $id . ' Kode perawatan Lab belum ditemukan dimapping';
                $error->save();
            }

            return null;
        }
    }

    public function getTemplateLoinc($id)
    {
        $data = DB::connection('mysqlkhanza')->table('fhir_lab_template')
            // ->join('fhir_master_loinc', 'fhir_master_loinc.kd_loinc', '=', 'fhir_lab.kd_loinc')
            ->select(
                'fhir_lab_template.kd_loinc',
                'fhir_lab_template.kd_snomed',
                'fhir_lab_template.kd_template'
            )
            ->where('fhir_lab_template.kd_template', "$id")
            ->first();

        if (!empty($data)) {
            $getData = MasterLoinc::where('kd_loinc', $data->kd_loinc)
                ->first();
        } else {
            $getData = null;
        }


        // dd($data, $id, $getData);

        if (!empty($getData)) {
            return $getData;
        } else {
            $error = new LogErrorSatuSehat();
            $error->subject = 'Lab';
            $error->keterangan = $id . ' Kode template loinc Lab belum ditemukan dimapping';
            $error->save();

            return null;
        }
    }

    public function getAnswerLoinc($id, $hasil)
    {
        $data = MasterAnswerLoinc::where('loinc_number', $id)
            ->where('answer_list_link_type', 'like', "%$hasil%")
            ->first();

        // dd($data);

        if (!empty($data)) {
            return $data;
        } else {
            $cek = LogErrorSatuSehat::where('subject', 'Lab Answer Loinc')
                ->where('keterangan', 'like', $id)
                ->whereDate('created_at', Carbon::now())
                ->get();
            if ($cek->count() < 1) {
                $error = new LogErrorSatuSehat();
                $error->subject = 'Lab Answer Loinc';
                $error->keterangan = $id . ' Kode loinc tidak memiliki answer';
                $error->save();
            }

            return null;
        }
    }

    public function getSpecimen($id)
    {
        $data = DB::connection('mysqlkhanza')->table('fhir_lab')
            ->join('fhir_master_specimen', 'fhir_master_specimen.kd_snomed', '=', 'fhir_lab.kd_snomed')
            ->select(
                'fhir_lab.kd_loinc',
                'fhir_lab.kd_snomed',
                'fhir_lab.kd_jenis_prw',
                'fhir_master_specimen.kd_snomed',
                'fhir_master_specimen.display',
                'fhir_master_specimen.coding_system'
            )
            ->where('fhir_lab.kd_loinc', "$id")
            ->first();

        if (!empty($data)) {
            return $data;
        } else {
            $error = new LogErrorSatuSehat();
            $error->subject = 'Lab Kode Spesimen';
            $error->keterangan = 'kode loinc ' . $id . ' tidak memiliki mapping Specimen';
            $error->save();

            return null;
        }
    }

    public function getWaktuKeperawatan($id)
    {
        $data = DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan')
            ->select(
                'penilaian_awal_keperawatan_ralan.no_rawat',
                'penilaian_awal_keperawatan_ralan.tanggal',
                'penilaian_awal_keperawatan_ralan.nip'
            )
            ->where('penilaian_awal_keperawatan_ralan.no_rawat', "$id")
            ->first();

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }

    public function getWaktuPemeriksaan($id)
    {
        $data = DB::connection('mysqlkhanza')->table('pemeriksaan_ralan')
            ->select(
                'pemeriksaan_ralan.no_rawat',
                'pemeriksaan_ralan.tgl_perawatan',
                'pemeriksaan_ralan.jam_rawat',
                'pemeriksaan_ralan.nip'
            )
            ->where('pemeriksaan_ralan.no_rawat', "$id")
            ->first();

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }

    public function getWaktuLab($id)
    {
        $data = DB::connection('mysqlkhanza')->table('permintaan_lab')
            ->select(
                'permintaan_lab.no_rawat',
                'permintaan_lab.tgl_permintaan',
                'permintaan_lab.jam_permintaan',
                'permintaan_lab.tgl_sampel',
                'permintaan_lab.jam_sampel',
                'permintaan_lab.tgl_hasil',
                'permintaan_lab.jam_hasil',
                'permintaan_lab.dokter_perujuk',
                'permintaan_lab.status'
            )
            ->where('permintaan_lab.no_rawat', "$id")
            ->where('permintaan_lab.status', "ralan")
            ->first();

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }
}
