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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


class SatuSehatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function sendEncounter(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Rajal Satu Sehat');
        session()->put('cucu', 'Send Encounter');
        set_time_limit(0);

        if (!empty($request->get('tanggal'))) {
            $pasien_tanggal = Carbon::parse($request->get('tanggal'))->format('Y-m-d');
            $yesterday = Carbon::parse($request->get('tanggal'))->subDay()->format('Y-m-d');
        } else {
            $pasien_tanggal = Carbon::now()->format('Y-m-d');
            $yesterday = Carbon::yesterday()->format('Y-m-d');
        }

        $sentIds = ResponseSatuSehat::whereNotNull('noRawat')
            ->whereBetween('tgl_registrasi', [$yesterday, $pasien_tanggal])
            ->pluck('noRawat');

        $query = DB::connection('mysqlkhanza')
            ->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select([
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli as kd_poli_reg',
                'reg_periksa.kd_pj',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli',
                'poliklinik.kd_poli'
            ])
            ->selectRaw("
                            IF(poliklinik.kd_poli = 'u0041', 'IGD', poliklinik.nm_poli) as alias_nm_poli
                        ")
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->whereIn('reg_periksa.stts', ['Sudah', 'Dirujuk', 'Berkas Lengkap'])
            ->where(function ($query) use ($pasien_tanggal, $yesterday) {
                $query->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
                    ->orWhere('reg_periksa.tgl_registrasi', $yesterday);
            })
            ->orderBy('reg_periksa.tgl_registrasi');

        //exclude poli IGD
        $query->whereNotIn('poliklinik.nm_poli', ['IGD', 'Farmasi', 'Farmasi 2', 'Radiologi', 'LABORATORIUM', 'POLI HOME CARE']);

        $dataPasien = $query->get();

        if ($sentIds->count() > 0) {
            $query->whereNotIn('reg_periksa.no_rawat', $sentIds);
        }

        $data = $query->limit(50)->get();

        //Kirim encounter
        foreach ($data as $key => $dataPengunjung) {
            $idRS = env('IDRS');
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

                //Jika tidak ditemukan simpan log di response satu sehat kemudian dikirim manual
                $cekRespon = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)
                    ->first();
                if (empty($cekRespon) || $cekRespon->count() == 0) {
                    $simpan = new ResponseSatuSehat();
                    $simpan->noRawat = $dataPengunjung->no_rawat;
                    $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                    $simpan->save();
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
            // $waktuKeperawatan = SatuSehatController::getWaktuKeperawatan($dataPengunjung->no_rawat);
            $ping = SatuSehatController::pingSatuSehat();

            if (!empty($idPasien) && !empty($idDokter) && !empty($idLokasi) && $ping) {
                $waktuMulai = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $dataPengunjung->tgl_registrasi . ' ' . $dataPengunjung->jam_reg,
                    'Asia/Jakarta' // timezone ASAL
                );

                $formatMulai = $waktuMulai->setTimezone('UTC')->toW3cString();

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
                        "start" => "$formatMulai"
                    ],
                    "location" => [
                        [
                            "location" => [
                                "reference" => "Location/$idLokasi",
                                "display" => "$dataPengunjung->alias_nm_poli"
                            ],
                            "period" => [
                                "start" => "$formatMulai"
                            ],
                            "extension" => [
                                [
                                    "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/ServiceClass",
                                    "extension" => [
                                        [
                                            "url" => "value",
                                            "valueCodeableConcept" => [
                                                "coding" => [
                                                    [
                                                        "system" => "http://terminology.kemkes.go.id/CodeSystem/locationServiceClass-Outpatient",
                                                        "code" => "reguler",
                                                        "display" => "Kelas Reguler"
                                                    ]
                                                ]
                                            ]
                                        ],
                                        [
                                            "url" => "upgradeClassIndicator",
                                            "valueCodeableConcept" => [
                                                "coding" => [
                                                    [
                                                        "system" => "http://terminology.kemkes.go.id/CodeSystem/locationUpgradeClass",
                                                        "code" => "kelas-tetap",
                                                        "display" => "Kelas Tetap Perawatan"
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "statusHistory" => [
                        [
                            "status" => "arrived",
                            "period" => [
                                "start" => "$formatMulai"
                                // ,
                                // "end" => "$formatProses"
                            ]
                        ]
                        // ,
                        // [
                        //     "status" => "in-progress",
                        //     "period" => [
                        //         "start" => "$formatProses"
                        //     ]
                        // ]
                    ],
                    "serviceProvider" => [
                        "reference" => "Organization/$idRS"
                    ]
                ];

                //Send data
                $access_token = SatuSehatController::getTokenSehat();
                $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                try {
                    $response = $client->request('POST', 'fhir-r4/v1/Encounter', [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}"
                        ],
                        'json' => $dataEncounter
                    ]);
                } catch (RequestException $e) {
                    // Handle request exception
                    $pesan = json_decode($e->getResponse()->getBody());
                    if (!empty($pesan->issue) && ($pesan->issue[0]->code == 'duplicate')) {
                        $check = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)
                            ->first();

                        if (empty($check) || $check->count() == 0) {
                            $simpan = new ResponseSatuSehat();
                            $simpan->noRawat = $dataPengunjung->no_rawat;
                            $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                            $simpan->encounter_id = 'duplicate';
                            $simpan->save();
                        }
                    }
                    LogErrorSatuSehat::create([
                        'subject' => 'Encounter Rajal',
                        'keterangan' => "RequestException saat mengirim encounter Rajal untuk no rawat $dataPengunjung->no_rawat: " . json_decode($e->getMessage(), true),
                    ]);

                    goto KirimPasienLain;
                } catch (ClientException $e) {
                    LogErrorSatuSehat::create([
                        'subject' => 'Encounter Rajal',
                        'keterangan' => "ClientException saat mengirim encounter Rajal untuk no rawat $dataPengunjung->no_rawat: " . json_decode($e->getMessage(), true),
                    ]);

                    goto KirimPasienLain;
                } catch (ConnectException $e) {
                    LogErrorSatuSehat::create([
                        'subject' => 'Encounter Rajal',
                        'keterangan' => "ConnectException saat mengirim encounter Rajal untuk no rawat $dataPengunjung->no_rawat: " . json_decode($e->getMessage(), true),
                    ]);

                    goto KirimPasienLain;
                } catch (BadResponseException $e) {
                    LogErrorSatuSehat::create([
                        'subject' => 'Encounter Rajal',
                        'keterangan' => "BadResponseException saat mengirim encounter Rajal untuk no rawat $dataPengunjung->no_rawat: " . json_decode($e->getMessage(), true),
                    ]);

                    goto KirimPasienLain;
                }

                $responseData = json_decode($response->getBody());

                if (!empty($responseData->id)) {
                    $check = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)
                        ->first();

                    if (empty($check) || $check->count() == 0) {
                        $simpan = new ResponseSatuSehat();
                        $simpan->noRawat = $dataPengunjung->no_rawat;
                        $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                        $simpan->encounter_id = $responseData->id;
                        $simpan->save();
                    }
                }
            }
            KirimPasienLain:
        }

        $dataLog = ResponseSatuSehat::whereBetween('tgl_registrasi', [$yesterday, $pasien_tanggal])
            ->get();

        $errorLog = LogErrorSatuSehat::whereDate('created_at', $pasien_tanggal)->get();

        return view('satu_sehat.summary', compact('dataLog', 'errorLog', 'dataPasien'));
    }

    public function closeEncounter(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Rajal Satu Sehat');
        session()->put('cucu', 'Close Encounter');
        set_time_limit(0);

        if (!empty($request->get('tanggal'))) {
            $pasien_tanggal = Carbon::parse($request->get('tanggal'))->format('Y-m-d');
            $yesterday = Carbon::parse($request->get('tanggal'))->subDay()->format('Y-m-d');

            // $dataEncounter = ResponseSatuSehat::whereBetween('tgl_registrasi', [$yesterday, $pasien_tanggal])
            $dataEncounter = ResponseSatuSehat::where('tgl_registrasi', $pasien_tanggal)
                ->whereNotNull('encounter_id')
                ->where(function ($query) {
                    $query->whereNull('temperature_id')
                        // ->orWhereNull('careplan_id')
                        // ->orWhereNull('procedure_id')
                        ->orWhereNull('condition_id');
                })
                ->orderBy('tgl_registrasi', 'ASC')
                ->limit(100)
                ->get();

            foreach ($dataEncounter as $dataTerkirim) {
                $cekPulang = DB::connection('mysqlkhanza')->table('reg_periksa')
                    ->select(
                        'reg_periksa.no_rawat',
                        'reg_periksa.status_lanjut',
                        'reg_periksa.stts',
                        'reg_periksa.status_bayar'
                    )
                    ->where('reg_periksa.no_rawat', $dataTerkirim->noRawat)
                    ->first();

                if ($cekPulang) {
                    if ($cekPulang->status_lanjut == 'Ralan') {
                        if ($cekPulang->status_bayar == 'Sudah Bayar') {
                            if ($dataTerkirim->temperature_id == null && $dataTerkirim->encounter_id != 'duplicate') {
                                SatuSehatController::sendVitalSign($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                            }
                            if ($dataTerkirim->careplan_id == null && $dataTerkirim->encounter_id != 'duplicate') {
                                SatuSehatController::sendCarePlan2($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                            }

                            if ($dataTerkirim->condition_id == null && $dataTerkirim->encounter_id != 'duplicate') {
                                // dd('masuk kirim condition', $dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                                SatuSehatController::sendCondition($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                            }
                            if ($dataTerkirim->procedure_id == null && $dataTerkirim->encounter_id != 'duplicate' && $dataTerkirim->condition_id != null) {
                                SatuSehatController::sendProcedure($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                            }
                            // if ($dataTerkirim->kondisi_stabil != null) {
                            //     $update = ResponseIgdSatuSehat::where('noRawat', $dataTerkirim->noRawat)->first();
                            //     $update->cara_keluar = 'IGD Pulang';
                            //     $update->save();
                            // };
                        }
                    }
                }
            }
        } else {
            $pasien_tanggal = Carbon::now()->format('Y-m-d');
            $yesterday = Carbon::yesterday()->format('Y-m-d');
        }

        $query = DB::connection('mysqlkhanza')
            ->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select([
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli as kd_poli_reg',
                'reg_periksa.kd_pj',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli',
                'poliklinik.kd_poli'
            ])
            ->selectRaw("
                            IF(poliklinik.kd_poli = 'u0041', 'IGD', poliklinik.nm_poli) as alias_nm_poli
                        ")
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->whereIn('reg_periksa.stts', ['Sudah', 'Dirujuk', 'Berkas Lengkap'])
            ->where(function ($query) use ($pasien_tanggal, $yesterday) {
                $query->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
                    ->orWhere('reg_periksa.tgl_registrasi', $yesterday);
            })
            ->orderBy('reg_periksa.tgl_registrasi');

        //exclude poli IGD
        $query->whereNotIn('poliklinik.nm_poli', ['IGD', 'Farmasi', 'Farmasi 2', 'Radiologi', 'LABORATORIUM', 'POLI HOME CARE']);

        $dataPasien = $query->get();

        $dataLog = ResponseSatuSehat::whereBetween('tgl_registrasi', [$yesterday, $pasien_tanggal])
            ->get();

        $errorLog = LogErrorSatuSehat::whereDate('created_at', $pasien_tanggal)
            ->orWhereDate('created_at', Carbon::now()->format('Y-m-d'))
            ->get();

        return view('satu_sehat.summary', compact('dataLog', 'errorLog', 'dataPasien'));
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
        set_time_limit(0);

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

        $ktpList = $dataLog->pluck('ktp_pasien')->unique();
        $idSehatMap = \App\PasienSehat::whereIn('nik', $ktpList)->pluck('satu_sehat_id', 'nik');

        $noRawatList = $dataLog->pluck('no_rawat')->unique();

        $encounters = \App\ResponseSatuSehat::whereIn('noRawat', $noRawatList)
            ->get()
            ->keyBy('noRawat');

        foreach ($dataLog as $list) {
            $list->idSehat = $idSehatMap[$list->ktp_pasien] ?? null;
            $list->dataEncounter = $encounters[$list->no_rawat] ?? null;
        }

        return view('satu_sehat.summary_rajal', compact('dataLog'));
    }

    public function checkRajalDetail($id)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Rajal Satu Sehat');
        session()->put('cucu', 'Summary Check');

        $id = Crypt::decrypt($id);

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

        $dataKiriman = ResponseSatuSehat::where('noRawat', $id)
            ->first();

        // $cekDiagnosa = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
        //     ->leftJoin('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
        //     ->select(
        //         'diagnosa_pasien.no_rawat',
        //         'diagnosa_pasien.kd_penyakit',
        //         'diagnosa_pasien.status',
        //         'diagnosa_pasien.prioritas',
        //         'penyakit.nm_penyakit',
        //         'penyakit.im'
        //     )
        //     ->where('diagnosa_pasien.status', 'Ralan')
        //     ->where('diagnosa_pasien.no_rawat', $id)
        //     ->get();

        $cekDiagnosa = DB::connection('mysqlkhanza')->table(DB::raw("(
                        SELECT
                            dp.no_rawat,
                            dp.kd_penyakit,
                            dp.status,
                            dp.prioritas,
                            p.nm_penyakit,
                            p.im,
                            'IDRG' as sumber,
                            1 as urutan_sumber
                        FROM diagnosa_pasien dp
                        LEFT JOIN penyakit p ON p.kd_penyakit = dp.kd_penyakit
                        WHERE dp.status = 'Ralan' AND dp.no_rawat = '$id'

                        UNION ALL

                        SELECT
                            dpi.no_rawat,
                            dpi.kd_penyakit,
                            dpi.status,
                            dpi.prioritas,
                            p.nm_penyakit,
                            p.im,
                            'INACBG' as sumber,
                            2 as urutan_sumber
                        FROM diagnosa_pasien_inacbg dpi
                        LEFT JOIN penyakit p ON p.kd_penyakit = dpi.kd_penyakit
                        WHERE dpi.status = 'Ralan' AND dpi.no_rawat = '$id'
                    ) as diagnosa_all"))
            ->orderBy('urutan_sumber')
            ->orderBy('prioritas')
            ->get();

        $cekProsedur = $data = DB::connection('mysqlkhanza')
            ->table(DB::raw("(
                SELECT no_rawat, kode, status, prioritas, 'IDRG' as sumber
                FROM prosedur_pasien
                UNION
                SELECT no_rawat, kode, status, prioritas, 'INACBG' as sumber
                FROM prosedur_pasien_inacbg
            ) as prosedur"))
            ->join('icd9', 'icd9.kode', '=', 'prosedur.kode')
            ->select(
                'prosedur.no_rawat',
                'prosedur.kode',
                'prosedur.status',
                'prosedur.prioritas',
                'prosedur.sumber',
                'icd9.deskripsi_panjang',
                'icd9.im'
            )
            ->where('prosedur.no_rawat', $id)
            ->where('prosedur.status', 'Ralan')
            ->where('prosedur.prioritas', '1')
            ->distinct()
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
            ->limit(3)
            ->get();

        $logPraktisi = LogErrorSatuSehat::where('subject', 'Praktitioner')
            ->where(function ($query)  use ($id, $dataPasien) {
                $query->where('keterangan', 'like', "%$id%")
                    ->orWhere('keterangan', 'like', "%$dataPasien->nama_dokter%")
                    ->orWhere('keterangan', 'like', "%$dataPasien->ktp_dokter%");
            })
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->get();

        $logDiagnosa = LogErrorSatuSehat::where('subject', 'like', '%Diagnosa%')
            ->where(function ($query)  use ($id, $dataPasien) {
                $query->where('keterangan', 'like', "%$id%")
                    ->orWhere('keterangan', 'like', "%$dataPasien->no_rkm_medis%")
                    ->orWhere('keterangan', 'like', "%$dataPasien->ktp_pasien%");
            })
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->get();

        $logPoliklinik = LogErrorSatuSehat::where('subject', 'Lokasi')
            ->where('keterangan', 'like', "%$dataPasien->kd_poli%")
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->get();

        $logOther = LogErrorSatuSehat::where('keterangan', 'like', "%$dataPasien->no_rkm_medis%")
            ->orWhere('keterangan', 'like', "%$dataPasien->no_rawat%")
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->get();

        return view('satu_sehat.check_error', compact(
            'dataPasien',
            'dataKiriman',
            'idSatu',
            'idSatuPraktisi',
            'logPraktisi',
            'logDiagnosa',
            'cekDiagnosa',
            'cekProsedur',
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

        if (empty($request->tanggal)) {
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
        } else {
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
        }

        $loop = 0;

        foreach ($data as $key => $dataPengunjung) {
            $cekLog = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->count();
            if (($cekLog == 0) && ($dataPengunjung->nm_poli != 'IGD')) {

                $idRS = env('IDRS');
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

                if ((!empty($idPasien)) && (!empty($idDokter))  && (!empty($idLokasi)) && (!empty($diagnosaPrimer))) { //

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
                    $formatWaktuMulai = $waktu_mulai->setTimezone('UTC')->toW3cString();
                    if ((!empty($waktuKeperawatan->tanggal))) {
                        $waktuInprogress = Carbon::parse($waktuKeperawatan->tanggal);
                        if ($waktu_mulai > $waktuInprogress) {
                            goto WaktuProses2;
                        }
                    } else {
                        WaktuProses2:
                        $waktuInprogress = Carbon::parse($waktuAwal)->addMinute(10);
                    }
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
                    }
                    $formatWaktuSelesai = $waktuSelesai->setTimezone('UTC')->toW3cString();

                    $day = Carbon::parse($waktuAwal)->dayName;
                    $day2 = Carbon::parse($waktuAwal)->format('d F Y');
                    $formatDay = $day . ', ' . $day2;

                    $dataBundle = [];

                    //UUID
                    $uuidEncounter = Str::uuid();
                    $uuidDiagnosaPrimer = Str::uuid();

                    $uuidHeart = Str::uuid();
                    $uuidRespiratory = Str::uuid();
                    $uuidSistol = Str::uuid();
                    $uuidDiastol = Str::uuid();
                    $uuidTemperature = Str::uuid();

                    //Off kan dulu proses kirim encounter dan vital saja
                    if ($diagnosaSekunder != null) {
                        $uuidDiagnosaSekunder = Str::uuid();
                        //encounter 2 diagnosa
                        $json_encounter = [
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
                        $json_encounter = [
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

                    $json_encounter = [
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

                    array_push($dataBundle, $json_encounter);

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

                    if (isset($uuidDiagnosaSekunder)) {
                        array_push($dataBundle, $diagnosis2);
                    }

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
                    array_push($dataBundle, $vital1);

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
                    array_push($dataBundle, $vital2);

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
                    array_push($dataBundle, $vital3);

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
                    array_push($dataBundle, $vital4);

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
                    array_push($dataBundle, $vital5);

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

                        array_push($dataBundle, $procedure);
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

                        array_push($dataBundle, $compositionData);
                    }

                    //Off dulu ganti proses kirimnya
                    // if ((!empty($diagnosaSekunder)) && (!empty($procedurePasien)) && (!empty($cekDiet))) {
                    //     $dataBundle = [$Encounter1, $diagnosis1, $diagnosis2, $vital1, $vital2, $vital3, $vital4, $vital5, $procedure, $compositionData];
                    // } elseif ((!empty($diagnosaSekunder)) && (!empty($procedurePasien)) && (empty($cekDiet))) {
                    //     $dataBundle = [$Encounter1, $diagnosis1, $diagnosis2, $vital1, $vital2, $vital3, $vital4, $vital5, $procedure];
                    // } elseif ((!empty($diagnosaSekunder)) && (empty($procedurePasien)) && (!empty($cekDiet))) {
                    //     $dataBundle = [$Encounter1, $diagnosis1, $diagnosis2, $vital1, $vital2, $vital3, $vital4, $vital5, $compositionData];
                    // } elseif ((!empty($diagnosaSekunder)) && (empty($procedurePasien)) && (empty($cekDiet))) {
                    //     $dataBundle = [$Encounter1, $diagnosis1, $diagnosis2, $vital1, $vital2, $vital3, $vital4, $vital5];
                    // } elseif ((empty($diagnosaSekunder)) && (!empty($procedurePasien)) && (!empty($cekDiet))) {
                    //     $dataBundle = [$Encounter2, $diagnosis1, $vital1, $vital2, $vital3, $vital4, $vital5, $procedure, $compositionData];
                    // } elseif ((empty($diagnosaSekunder)) && (!empty($procedurePasien)) && (empty($cekDiet))) {
                    //     $dataBundle = [$Encounter2, $diagnosis1, $vital1, $vital2, $vital3, $vital4, $vital5, $procedure];
                    // } elseif ((empty($diagnosaSekunder)) && (empty($procedurePasien)) && (!empty($cekDiet))) {
                    //     $dataBundle = [$Encounter2, $diagnosis1, $vital1, $vital2, $vital3, $vital4, $vital5, $compositionData];
                    // } else {
                    //     $dataBundle = [$Encounter2, $diagnosis1, $vital1, $vital2, $vital3, $vital4, $vital5];
                    // }

                    // SatuSehatController::getTokenSehat();
                    $access_token = SatuSehatController::getTokenSehat();
                    try {
                        $baseUrl = cache()->get('base_url');
                        $client = new Client(['base_uri' => $baseUrl, 'timeout' => 5]);

                        // 2️⃣ Cek konektivitas endpoint dulu (HEAD / ping)
                        // try {
                        //     $checkResponse = $client->request('GET', 'fhir-r4/v1/metadata', [
                        //         'headers' => [
                        //             'Authorization' => "Bearer {$access_token}"
                        //         ],
                        //     ]);

                        //     if ($checkResponse->getStatusCode() !== 200) {
                        //         Session::flash('error', 'Server Satu Sehat tidak dapat dijangkau (status != 200).');
                        //         return;
                        //     }
                        // } catch (RequestException $pingError) {
                        //     // Jika gagal koneksi, log & batalkan
                        //     $error = new LogErrorSatuSehat();
                        //     $error->subject = 'Bundle Ralan';
                        //     $error->keterangan = "Server Satu Sehat tidak dapat dijangkau (" . $pingError->getMessage() . ")";
                        //     $error->save();

                        //     Session::flash('error', 'Server Satu Sehat tidak dapat dijangkau. Pengiriman dibatalkan.');
                        //     return;
                        // } catch (ConnectException $connectError) {
                        //     // Jika gagal koneksi, log & batalkan
                        //     // dd($connectError->getMessage());
                        //     $error = new LogErrorSatuSehat();
                        //     $error->subject = 'Bundle Ralan';
                        //     $error->keterangan = "Server Satu Sehat tidak dapat dijangkau (" . $connectError->getMessage() . ")";
                        //     $error->save();

                        //     Session::flash('error', 'Server Satu Sehat tidak dapat dijangkau. Pengiriman dibatalkan.');
                        //     return;
                        // }

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
                        // dd(
                        //     $e->getResponse()->getStatusCode(),
                        //     (string) $e->getResponse()->getBody()
                        // );
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();
                            $body = (string) $response->getBody();
                            $test = json_decode($body);
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

                                //Kirim CarePlan
                                SatuSehatController::sendCarePlan($dataPengunjung, $idPasien, $idDokter);

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

                    $data = json_decode($response->getBody());

                    if (!empty($data->entry)) {
                        foreach ($data->entry as $index => $dataRespone) {
                            foreach ($dataRespone as $dataPoint) {
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
        }

        Selesai:
        if (empty($request->get('tanggal'))) {
            $dataLog = ResponseSatuSehat::whereDate('created_at', $pasien_tanggal)->get();
        } else {
            $dataLog = ResponseSatuSehat::whereDate('tgl_registrasi', $pasien_tanggal)
                ->get();
        }

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

        $cekLog = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
        if (($cekLog == null || $cekLog->encounter_id == null) && ($dataPengunjung->nm_poli != 'IGD')) {
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
                // dd($diagnosaPrimer, $diagnosaSekunder);
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
                // SatuSehatController::getTokenSehat();
                // $access_token = SatuSehatController::getTokenSehat();
                // $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                $access_token = SatuSehatController::getTokenSehat();
                // dd($access_token);
                try {
                    $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                    // try {
                    //     $baseUrl = session('base_url');
                    //     $client = new Client(['base_uri' => $baseUrl, 'timeout' => 5]);

                    // 2️⃣ Cek konektivitas endpoint dulu (HEAD / ping)
                    try {
                        $checkResponse = $client->request('GET', 'fhir-r4/v1/metadata', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                        ]);

                        if ($checkResponse->getStatusCode() !== 200) {
                            Session::flash('error', 'Server Satu Sehat tidak dapat dijangkau (status != 200).');
                            return;
                        }
                    } catch (RequestException $pingError) {
                        // Jika gagal koneksi, log & batalkan
                        $error = new LogErrorSatuSehat();
                        $error->subject = 'Bundle Ralan';
                        $error->keterangan = "Server Satu Sehat tidak dapat dijangkau (" . $pingError->getMessage() . ")";
                        $error->save();

                        Session::flash('error', 'Server Satu Sehat tidak dapat dijangkau. Pengiriman dibatalkan.');
                        return;
                    }

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
                        $body = (string) $response->getBody();
                        $test = json_decode($body);
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

                            // $cek = LogErrorSatuSehat::where('subject', 'Bundle Ralan')
                            //     ->where('keterangan', 'like', '%' . $dataPengunjung->no_rawat . '%')
                            //     ->whereDate('created_at', Carbon::now())
                            //     ->get();
                            // if ($cek->count() < 1) {
                            $error = new LogErrorSatuSehat();
                            $error->subject = 'Bundle Ralan';
                            $error->keterangan = $dataPengunjung->no_rawat . ' error kirim "' . $pesan . '"';
                            $error->save();
                            // }

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

                    //Kirim CarePlan
                    SatuSehatController::sendCarePlan($dataPengunjung, $idPasien, $idDokter);

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

    // public function sendDiagnosis($norawat)
    // {
    //     $norawat = Crypt::decrypt($norawat);
    //     $dataDiagnosa = SatuSehatController::getDiagnosaPasien($norawat);

    //     return redirect()->back();
    // }

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
                    $waktuAwal = $dataPengunjung->tgl_registrasi . ' ' . $dataPengunjung->jam_reg;
                    $waktu_mulai = new Carbon($waktuAwal);
                    $waktuKeperawatan = SatuSehatController::getWaktuKeperawatan($dataPengunjung->no_rawat);

                    //Definisi Vital
                    $vital = SatuSehatController::getVital($dataPengunjung->no_rawat);
                    $formatWaktuMulai = $waktu_mulai->setTimezone('UTC')->toW3cString();
                    if ((!empty($waktuKeperawatan->tanggal))) {
                        $waktuInprogress = Carbon::parse($waktuKeperawatan->tanggal);
                        if ($waktu_mulai > $waktuInprogress) {
                            goto WaktuProses2;
                        }
                    } else {
                        WaktuProses2:
                        $waktuInprogress = Carbon::parse($waktuAwal)->addMinute(10);
                    }
                    $formatWaktuProgress = $waktuInprogress->setTimezone('UTC')->toW3cString();
                    if ((!empty($vital->tgl_perawatan))) {
                        $waktuSelesai = Carbon::parse($vital->tgl_perawatan . ' ' . $vital->jam_rawat);
                        if ($waktuInprogress > $waktuSelesai) {
                            goto WaktuSelesai2;
                        }
                    } else {
                        WaktuSelesai2:
                        $waktuSelesai = Carbon::parse($waktuAwal)->addMinute(30);
                    }
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

                    //Send data
                    // SatuSehatController::getTokenSehat();
                    $access_token = SatuSehatController::getTokenSehat();
                    $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Encounter', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataEncounter
                        ]);
                    } catch (BadResponseException $e) {
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            $body = (string) $response->getBody();
                            $test = json_decode($body);
                            // dd($test->issue[0]->code, 'error2');
                            if (!empty($test->issue[0]->code == 'duplicate')) {
                                // $simpan = new ResponseSatuSehat();
                                // $simpan->noRawat = $dataPengunjung->no_rawat;
                                // $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                                // $simpan->encounter_id = 'duplicate encounter';
                                // $simpan->save();

                            }
                        }

                        $message = $test->issue[0]->code;

                        Session::flash('error', $message);

                        return redirect()->back()->withInput();
                    }

                    $data = json_decode($response->getBody());

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
                // SatuSehatController::getTokenSehat();
                $access_token = SatuSehatController::getTokenSehat();
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
                        $body = (string) $response->getBody();
                        $test = json_decode($body);
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
            $idLokasi = SatuSehatController::getIdPoli($dataPengunjung->kd_poli);

            $getResep = SatuSehatController::getResepObat($dataPengunjung->no_rawat);
            $idCounter = SatuSehatController::getEncounterId($dataPengunjung->no_rawat);
            //Cek List Obat di Response Medication apakah sudah ada
            $cekResponse = ResponseMedicationSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
            if ((!empty($getResep)) && (!empty($idCounter)) && (empty($cekResponse))) {
                //Resep Obat Jadi di table resep_dokter
                $listObat = SatuSehatController::getListObat($getResep->no_resep);
                //Resep Obat Racikan tabel resep_dokter_racikan
                $listObatRacik = SatuSehatController::getListObatRacik($getResep->no_resep);
                $detailRacikan = SatuSehatController::getDetailRacikan($getResep->no_resep);

                $noresep = $noUrutResep = '';
                //Obat Jadi di Kirim dl
                if ($listObat->count() > 0) {
                    foreach ($listObat as $index => $dataListObat) {
                        $noUrutResep = $index + 1;
                        $noresep = $dataListObat->no_resep . '-' . $noUrutResep;

                        //Get Id Obat
                        $mappingObat = SatuSehatController::getIdObat($dataListObat->kode_brng);

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

                            $access_token = SatuSehatController::getTokenSehat();
                            $baseUrl = cache()->get('base_url');
                            $client = new \GuzzleHttp\Client(['base_uri' => $baseUrl]);
                            try {
                                $response = $client->request('POST', 'fhir-r4/v1/Medication', [
                                    'headers' => [
                                        'Authorization' => "Bearer {$access_token}"
                                    ],
                                    'json' => $medication1
                                ]);
                            } catch (BadResponseException $e) {
                                if ($e->hasResponse()) {
                                    $response = $e->getResponse();
                                    $body = (string) $response->getBody();
                                    $test = json_decode($body);
                                    $errorCode = (array) $test;

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
                                    }
                                    goto NextObatJadi;
                                }
                            }

                            $data = json_decode($response->getBody());

                            if (!empty($data->id) && $data->resourceType == "Medication") {

                                $simpan = new ResponseMedicationSatuSehat();
                                $simpan->noRawat = $dataPengunjung->no_rawat;
                                $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                                $simpan->noResep = $noresep;
                                $simpan->medication1 = $data->id;
                                $simpan->save();

                                //Off ini dulu buat pakai langsung dari inisialisasi idMedication1 saja
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
                                // SatuSehatController::getTokenSehat();
                                $access_token = SatuSehatController::getTokenSehat();
                                $baseUrl = cache()->get('base_url');
                                $client = new \GuzzleHttp\Client(['base_uri' => $baseUrl]);
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
                                        $body = (string) $response->getBody();
                                        $test = json_decode($body);

                                        dd($test, 'medicationRequest');
                                        $errorCode = (array) $test;
                                        if (!empty($errorCode['issue'][0])) {
                                            $pesan = $errorCode['issue'][0]->details->text;

                                            $message = "Medication Request error $pesan";

                                            Session::flash('error', $message);
                                        } else {
                                            Session::flash('error', $errorCode['fault']->faultstring);
                                        }
                                        goto NextObatJadi;
                                    }
                                }

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
                                    // SatuSehatController::getTokenSehat();
                                    $access_token = SatuSehatController::getTokenSehat();
                                    $baseUrl = cache()->get('base_url');
                                    $client = new \GuzzleHttp\Client(['base_uri' => $baseUrl]);
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
                                            $body = (string) $response->getBody();
                                            $test = json_decode($body);
                                            $errorCode = (array) $test;

                                            dd($test, 'medication2');
                                            if (!empty($errorCode['issue'][0])) {
                                                $pesan = $errorCode['issue'][0]->details->text;

                                                $message = "Medication2 error $pesan";

                                                Session::flash('error', $message);
                                            } else {
                                                Session::flash('error', $errorCode['fault']->faultstring);
                                            }
                                            goto NextObatJadi;
                                        }
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
                                    $formatWaktuMulai = $waktu_mulai->setTimezone('UTC')->toW3cString();
                                    $waktuSelesai = $getResep->tgl_penyerahan . ' ' . $getResep->jam_penyerahan;
                                    $waktu_selesai = new Carbon($waktuSelesai);
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
                                        $access_token = SatuSehatController::getTokenSehat();
                                        $client = new \GuzzleHttp\Client(['base_uri' => $baseUrl]);
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
                                                $body = (string) $response->getBody();
                                                $test = json_decode($body);
                                                $errorCode = (array) $test;

                                                dd($test, 'medication dispance', $medicationDispense);
                                                if (!empty($errorCode['issue'][0])) {
                                                    $pesan = $errorCode['issue'][0]->details->text;

                                                    $message = "Medication Dispance error $pesan";

                                                    Session::flash('error', $message);
                                                } else {
                                                    Session::flash('error', $errorCode['fault']->faultstring);
                                                }
                                            }

                                            goto NextObatJadi;
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

        return view('satu_sehat.client_apotek', compact('dataLog'));
    }

    public function sendQuestionnariesResponse()
    {
        $no_rawat = '';
        $patientId = '';
        $getData = DB::connection('mysqlkhanza')->table('resep_obat')
            ->join('telaah_farmasi', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->where('resep_obat.no_rawat', $no_rawat)
            ->first();

        if ($getData) {
            $data_json = [
                "resourceType" => "QuestionnaireResponse",
                "questionnaire" => "https://fhir.kemkes.go.id/Questionnaire/Q0007",
                "status" => "completed",
                "subject" => [
                    "reference" => "Patient/{{Patient_id}}",
                    "display" => "{{Patient_Name}}"
                ],
                "encounter" => [
                    "reference" => "Encounter/{{Encounter_id}}"
                ],
                "authored" => "2023-06-04T10:00:00+00:00",
                "author" => [
                    "reference" => "Practitioner/N10000003",
                    "display" => "Apoteker Miller"
                ],
                "source" => [
                    "reference" => "Patient/{{Patient_id}}"
                ],
                "item" => [
                    [
                        "linkId" => "1",
                        "text" => "Persyaratan Administrasi",
                        "item" => [
                            [
                                "linkId" => "1.1",
                                "text" => "Tepat Identifikasi Pasien?",
                                "answer" => [
                                    [
                                        "valueCoding" => [
                                            "system" => "http://terminology.kemkes.go.id/CodeSystem/clinical-term",
                                            "code" => "OV000052",
                                            "display" => "Sesuai"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "linkId" => "2",
                        "text" => "Persyaratan Farmasetik",
                        "item" => [
                            [
                                "linkId" => "2.1",
                                "text" => "Tepat Obat?",
                                "answer" => [
                                    [
                                        "valueCoding" => [
                                            "system" => "http://terminology.kemkes.go.id/CodeSystem/clinical-term",
                                            "code" => "OV000052",
                                            "display" => "Sesuai"
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "linkId" => "2.2",
                                "text" => "Tepat Dosis?",
                                "answer" => [
                                    [
                                        "valueCoding" => [
                                            "system" => "http://terminology.kemkes.go.id/CodeSystem/clinical-term",
                                            "code" => "OV000052",
                                            "display" => "Sesuai"
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "linkId" => "2.3",
                                "text" => "Tepat Cara Pemberian?",
                                "answer" => [
                                    [
                                        "valueCoding" => [
                                            "system" => "http://terminology.kemkes.go.id/CodeSystem/clinical-term",
                                            "code" => "OV000052",
                                            "display" => "Sesuai"
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "linkId" => "2.4",
                                "text" => "Tepat Waktu Pemberian?",
                                "answer" => [
                                    [
                                        "valueCoding" => [
                                            "system" => "http://terminology.kemkes.go.id/CodeSystem/clinical-term",
                                            "code" => "OV000052",
                                            "display" => "Sesuai"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "linkId" => "3",
                        "text" => "Persyaratan Klinis",
                        "item" => [
                            [
                                "linkId" => "3.1",
                                "text" => "Ada Tidak Duplikasi Obat?",
                                "answer" => [
                                    [
                                        "valueCoding" => [
                                            "system" => "http://terminology.kemkes.go.id/CodeSystem/clinical-term",
                                            "code" => "OV000052",
                                            "display" => "Sesuai"
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "linkId" => "3.2",
                                "text" => "Interaksi Obat?",
                                "answer" => [
                                    [
                                        "valueBoolean" => false
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "linkId" => "4",
                        "text" => "Resep yang dilakukan pengkajian resep",
                        "answer" => [
                            [
                                "valueReference" => [
                                    "reference" => "MedicationRequest/{{MedicationRequest_id1}}"
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }
    }

    public function sendLab(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Rajal Satu Sehat');
        session()->put('cucu', 'API Lab');
        set_time_limit(0);

        $pasien_tanggal = Carbon::now()->format('Y-m-d');
        $kemarin = Carbon::yesterday();
        $idRS = Env('IDRS');

        if (empty($request->get('tanggal'))) {
            $pasien_tanggal = Carbon::now()->format('Y-m-d');
            $kemarin = Carbon::yesterday()->format('Y-m-d');
        } else {
            $pasien_tanggal = $request->get('tanggal');
            $kemarin = Carbon::parse($request->get('tanggal'))->subDays(1)->format('Y-m-d');
        }

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
            $dokterPerujuk = SatuSehatController::practitioner($pasienLab->ktp_dokter);
            $idPasien = SatuSehatController::patientSehat($pasienLab->ktp_pasien);

            if ((!empty($cekLab)) && (!empty($idCounter)) && (!empty($dokterPerujuk)) && (!empty($idPasien))) {
                //Cek apakah sudah pernah kirim data
                $cekResponseLab = ResponseLabSatuSehat::where('noOrder', $cekLab->noorder)->first();

                if (empty($cekResponseLab)) {

                    //cek data periksa lab
                    $periksaLab = DB::connection('mysqlkhanza')->table('periksa_lab')
                        ->join('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'periksa_lab.kd_jenis_prw')
                        ->leftJoin('pegawai', 'pegawai.nik', '=', 'periksa_lab.nip')
                        ->select(
                            'periksa_lab.no_rawat',
                            'periksa_lab.kd_jenis_prw',
                            'periksa_lab.tgl_periksa',
                            'periksa_lab.jam',
                            'periksa_lab.nip as petugas',
                            'periksa_lab.dokter_perujuk',
                            'pegawai.no_ktp as ktp_petugas_lab',
                            'pegawai.nama as nama_petugas_lab',
                            'jns_perawatan_lab.nm_perawatan'
                        )
                        ->where('no_rawat', $pasienLab->no_rawat)
                        ->get();

                    foreach ($periksaLab as $PeriksaLab) {
                        //ambil data mapping Loinc
                        $mappingLoinc = SatuSehatController::getLoinc($PeriksaLab->kd_jenis_prw);
                        $waktuPerawatan = $PeriksaLab->tgl_periksa . ' ' . $PeriksaLab->jam;
                        $waktu_perawatan = new Carbon($waktuPerawatan);
                        $formatWaktuPerawatan = $waktu_perawatan->setTimezone('UTC')->toW3cString();
                        $petugasLab = SatuSehatController::practitioner($PeriksaLab->ktp_petugas_lab);

                        //Cek apakah sudah ada mapping belum
                        if (!empty($mappingLoinc) && !empty($petugasLab)) {
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
                                ],
                                "performer" => [
                                    [
                                        "reference" => "Practitioner/$petugasLab",
                                        "display" => "$PeriksaLab->nama_petugas_lab"
                                    ]
                                ]
                                // ,
                                // "reasonCode" => [
                                //     [
                                //         "text" => "Periksa Keseimbangan Elektrolit"
                                //     ]
                                // ]
                            ];

                            //Kirim/Create Service Request
                            // SatuSehatController::getTokenSehat();
                            $access_token = SatuSehatController::getTokenSehat();
                            $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
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
                                    $body = (string) $response->getBody();
                                    $test = json_decode($body);

                                    dd($test, 'Service Request');
                                }

                                $message = "Error Kirim Service Request $PeriksaLab->kd_jenis_prw $PeriksaLab->no_rawat";

                                LogErrorSatuSehat::create([
                                    'subject' => 'Kirim Service Request Lab',
                                    'keterangan' => $message
                                ]);

                                goto nextPasienLab;
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
                                // SatuSehatController::getTokenSehat();
                                $access_token = SatuSehatController::getTokenSehat();
                                $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
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
                                        $body = (string) $response->getBody();
                                        $test = json_decode($body);

                                        dd($test, 'Specimen Lab');
                                    }

                                    $message = "Error Kirim Specimen dengan id service " . $idServiceRequest;

                                    LogErrorSatuSehat::create([
                                        'subject' => 'Kirim Specimen Lab',
                                        'keterangan' => $message
                                    ]);

                                    goto nextPasienLab;
                                }

                                $responseSpecimen = json_decode($response->getBody());

                                if (!empty($responseSpecimen->id) && $responseSpecimen->resourceType == "Specimen") {
                                    //Update data di table respone medication request
                                    $update = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                                    $update->specimen_id = $responseSpecimen->id;
                                    $update->save();

                                    //cek detail data hasil lab
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

                                    if ($detailLab->count() > 1) {
                                        foreach ($detailLab as $DetailLab) {
                                            //cek nilai hasil lab kosong atau masih dalam proses jika tidak lanjut
                                            if ((!empty($DetailLab->nilai)) && (strpos($DetailLab->nilai, "proses") === false)) {

                                                $dataHasil = SatuSehatController::getTemplateLoinc($DetailLab->id_template);
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
                                                    $access_token = SatuSehatController::getTokenSehat();
                                                    $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                                                    try {
                                                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                                                            'headers' => [
                                                                'Authorization' => "Bearer {$access_token}"
                                                            ],
                                                            'json' => $Observation
                                                        ]);
                                                    } catch (BadResponseException $e) {
                                                        if ($e->hasResponse()) {
                                                            $response = $e->getResponse();

                                                            $body = (string) $response->getBody();
                                                            $test = json_decode($body);

                                                            dd($test, 'kirim observation lab error');
                                                        }

                                                        $message = "Error Kirim Observation Lab id service " . $idServiceRequest;

                                                        LogErrorSatuSehat::create([
                                                            'subject' => 'Kirim Observation Lab',
                                                            'keterangan' => $message
                                                        ]);

                                                        goto nextPasienLab;
                                                    }

                                                    $responseObservation = json_decode($response->getBody());
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
                                                                "effectiveDateTime" => "$formatWaktuHasil",
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
                                                                "effectiveDateTime" => "$formatWaktuHasil",
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
                                                                "effectiveDateTime" => "$formatWaktuHasil",
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
                                                                "effectiveDateTime" => "$formatWaktuHasil",
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
                                                            "effectiveDateTime" => "$formatWaktuHasil",
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
                                                            "effectiveDateTime" => "$formatWaktuHasil",
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
                                                    // SatuSehatController::getTokenSehat();
                                                    $access_token = SatuSehatController::getTokenSehat();
                                                    // dd($access_token);
                                                    $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
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
                                                            $body = (string) $response->getBody();
                                                            $test = json_decode($body);

                                                            dd($test, 'Kirim Observation Lab Error');
                                                        }

                                                        $message = "Error Kirim Observation Lab id service " . $idServiceRequest;

                                                        LogErrorSatuSehat::create([
                                                            'subject' => 'Kirim Observation Lab',
                                                            'keterangan' => $message
                                                        ]);

                                                        goto nextPasienLab;
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
                                } else {
                                    dd($responseSpecimen);
                                }

                                //Diagnostic Report
                                //Cek dulu Observasinya ada berapa hasil
                                $cekID = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                                $cekObservation = ResponseObservationLab::where('response_lab_satu_sehat_id', $cekID->id)->get();

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
                                    //Kirim/Create Diagnostic Report
                                    // SatuSehatController::getTokenSehat();
                                    $access_token = SatuSehatController::getTokenSehat();
                                    $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
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
                                            $body = (string) $response->getBody();
                                            $test = json_decode($body);
                                            dd($test);
                                        }

                                        $message = "Error Kirim Report lab id service request " . $idServiceRequest;

                                        LogErrorSatuSehat::create([
                                            'subject' => 'Kirim Diagnostic Report Lab',
                                            'keterangan' => $message
                                        ]);

                                        goto nextPasienLab;
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

                            nextPasienLab:
                        }
                    }
                }
            }
        }

        $dataPermintaanLab = DB::connection('mysqlkhanza')->table('permintaan_lab')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_lab.no_rawat')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('permintaan_pemeriksaan_lab', 'permintaan_pemeriksaan_lab.noorder', '=', 'permintaan_lab.noorder')
            ->leftJoin('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'permintaan_pemeriksaan_lab.kd_jenis_prw')
            ->select(
                'permintaan_lab.no_rawat',
                'permintaan_lab.noorder',
                'permintaan_lab.status',
                'pasien.nm_pasien',
                'jns_perawatan_lab.kd_jenis_prw',
                'jns_perawatan_lab.nm_perawatan'
            )
            ->where('permintaan_lab.status', 'ralan')
            ->whereDate('tgl_permintaan', $pasien_tanggal)
            ->get();

        $dataRawat = $dataPermintaanLab->pluck('no_rawat')->toArray();
        $dataLog = collect();

        if (!empty($dataRawat)) {
            $dataLog = ResponseLabSatuSehat::whereIn('noRawat', $dataRawat)->get();
        }

        return view('satu_sehat.client_rujuklab', compact('dataLog', 'dataPermintaanLab'));
    }

    public function sendCarePlan($dataKunjungan, $idPasien, $idDokter)
    {
        $data = DB::connection('mysqlkhanza')->table('pemeriksaan_ralan')
            ->where('pemeriksaan_ralan.no_rawat', $dataKunjungan->no_rawat)
            ->first();

        $idEncounter = SatuSehatController::getEncounterId($dataKunjungan->no_rawat);

        // dd($data, $idEncounter);

        if ($data && $idEncounter != null) {
            $waktuPerawatan = new Carbon("$data->tgl_perawatan $data->jam_rawat");
            $formatWaktuPerawatan = $waktuPerawatan->setTimezone('UTC')->toW3cString();

            if ($data && ($data->instruksi != null || $data->intruksi != '-')) {

                $data_json = [
                    "resourceType" => "CarePlan",
                    "status" => "active",
                    "intent" => "plan",
                    "category" => [
                        [
                            "coding" => [
                                [
                                    "system" => "http://snomed.info/sct",
                                    "code" => "736271009",
                                    "display" => "Outpatient care plan"
                                ]
                            ]
                        ]
                    ],
                    "title" => "Instruksi Medik dan Keperawatan Pasien",
                    "description" => "$data->instruksi",
                    "subject" => [
                        "reference" => "Patient/$idPasien",
                        "display" => "$dataKunjungan->nm_pasien"
                    ],
                    "encounter" => [
                        "reference" => "Encounter/$idEncounter->encounter_id"
                    ],
                    "created" => "$formatWaktuPerawatan",
                    "author" => [
                        "reference" => "Practitioner/$idDokter",
                        "display" => "$dataKunjungan->nama_dokter"
                    ]
                    // ,
                    // "goal" => [
                    //     [
                    //         "reference" => "Goal/{{Goal_TujuanPerawatan}}"
                    //     ]
                    // ]
                ];

                //Kirim/Create Service Request
                // SatuSehatController::getTokenSehat();
                $access_token = SatuSehatController::getTokenSehat();
                $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                try {
                    $response = $client->request('POST', 'fhir-r4/v1/CarePlan', [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}"
                        ],
                        'json' => $data_json
                    ]);
                } catch (BadResponseException $e) {
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();
                        $body = (string) $response->getBody();
                        $test = json_decode($body);
                        // $pesan = $test->issue->details ? $test->issue->details->text : 'pola baru error';
                        dd($test, 'Kirim Care Plan Error');
                        $error = new LogErrorSatuSehat();
                        $error->subject = 'Care Plan';
                        $error->keterangan = $dataPengunjung->no_rawat . ' error kirim "' . $pesan . '"';
                        $error->save();
                    }

                    return;
                }

                $bodyResponse = json_decode($response->getBody());


                if ($bodyResponse && !empty($bodyResponse->id)) {

                    $update = ResponseSatuSehat::where('encounter_id', $idEncounter->encounter_id)
                        ->first();
                    $update->careplan_id = $bodyResponse->id;
                    $update->save();
                }
            }
        }
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

        $idRS = env('IDRS');

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
                    // SatuSehatController::getTokenSehat();
                    $access_token = SatuSehatController::getTokenSehat();
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
                            $body = (string) $response->getBody();
                            $test = json_decode($body);
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
                            // SatuSehatController::getTokenSehat();
                            $access_token = SatuSehatController::getTokenSehat();
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
                                    $body = (string) $response->getBody();
                                    $test = json_decode($body);

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
                                // SatuSehatController::getTokenSehat();
                                $access_token = SatuSehatController::getTokenSehat();
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
                                        $body = (string) $response->getBody();
                                        $test = json_decode($body);
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
                                                    // SatuSehatController::getTokenSehat();
                                                    $access_token = SatuSehatController::getTokenSehat();
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
                                                            $body = (string) $response->getBody();
                                                            $test = json_decode($body);
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
                                                    // SatuSehatController::getTokenSehat();
                                                    $access_token = SatuSehatController::getTokenSehat();
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
                                                            $body = (string) $response->getBody();
                                                            $test = json_decode($body);
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
                                    // SatuSehatController::getTokenSehat();
                                    $access_token = SatuSehatController::getTokenSehat();
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
                                            $body = (string) $response->getBody();
                                            $test = json_decode($body);
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

    // public static function tokenSehat()
    // {
    //     $setting = Setting::where('nama', 'satusehat')->first();
    //     // dd($setting);
    //     session()->put('base_url', $setting->base_url);
    //     try {
    //         $client = new \GuzzleHttp\Client(['base_uri' => $setting->base_url]);
    //         $response = $client->request('POST', 'oauth2/v1/accesstoken?grant_type=client_credentials', [
    //             'headers' => [
    //                 'Content-Type' => "application/x-www-form-urlencoded"
    //             ],
    //             'form_params' => [
    //                 'client_id' => $setting->satker,
    //                 'client_secret' => $setting->key,
    //             ]
    //         ]);
    //     } catch (ConnectException $e) {
    //         // Gagal konek ke server (timeout, DNS gagal, dll)
    //         $simpan = new LogErrorSatuSehat();
    //         $simpan->subject = 'Token Satu Sehat';
    //         $simpan->keterangan = "Koneksi ke server gagal atau waktu habis (" . $e->getMessage() . ")";
    //         $simpan->save();

    //         return redirect()->back()->with('error', 'Koneksi ke server gagal atau waktu habis.');
    //     } catch (ClientException $e) {
    //         if ($e->hasResponse()) {
    //             $response = $e->getResponse();
    //             $test = json_decode((string) $response->getBody());
    //         }

    //         $simpan = new LogErrorSatuSehat();
    //         $simpan->subject = 'Token Satu Sehat';
    //         $simpan->keterangan = "Pesan error: (" . $test . ")";
    //         $simpan->save();

    //         Session::flash('error', $test->message);

    //         return redirect()->back()->withInput();
    //     } catch (\Throwable $e) {
    //         // Menangkap semua error lainnya (lebih luas dari Exception)
    //         Log::error('Throwable: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Terjadi kesalahan internal: ' . $e->getMessage());
    //     }

    //     $data = json_decode($response->getBody());

    //     if ($data->status == "approved") {
    //         // session()->put('tokenSatuSehat', $data->access_token);

    //         // $expired = Carbon::now()->addHour();
    //         // session()->put('expiredSatuSehat', $expired);

    //         Cache::put(
    //             'tokenSatuSehat',
    //             $data->access_token,
    //             now()->addSeconds($data->expires_in - 60) // buffer 1 menit
    //         );
    //     }
    // }

    public static function tokenSehat(): string
    {
        $setting = Setting::where('nama', 'satusehat')->firstOrFail();

        try {
            $client = new \GuzzleHttp\Client([
                'base_uri' => $setting->base_url,
                'timeout'  => 10,
            ]);

            $response = $client->request('POST', 'oauth2/v1/accesstoken?grant_type=client_credentials', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'client_id'     => $setting->satker,
                    'client_secret' => $setting->key,
                ],
            ]);
        } catch (ConnectException $e) {
            LogErrorSatuSehat::create([
                'subject' => 'Token Satu Sehat',
                'keterangan' => $e->getMessage(),
            ]);

            throw new \Exception('Koneksi ke server SatuSehat gagal');
        } catch (ClientException $e) {
            $body = $e->getResponse()->getBody()->getContents();

            LogErrorSatuSehat::create([
                'subject' => 'Token Satu Sehat',
                'keterangan' => $body,
            ]);

            throw new \Exception('Client error saat request token SatuSehat');
        } catch (\Throwable $e) {
            Log::error('SatuSehat Token Error: ' . $e->getMessage());
            throw $e;
        }

        $data = json_decode($response->getBody(), true);

        if (($data['status'] ?? null) !== 'approved') {
            throw new \Exception('Token SatuSehat tidak approved');
        }

        Cache::put(
            'tokenSatuSehat',
            $data['access_token'],
            now()->addSeconds($data['expires_in'] - 60)
        );

        if (!cache()->has('base_url')) {
            $setting = Setting::where('nama', 'satusehat')->first();
            cache()->put('base_url', $setting->base_url, now()->addDay());
        }

        return $data['access_token'];
    }

    public static function getTokenSehat()
    {
        $token = Cache::get('tokenSatuSehat');

        if (!$token) {
            $token = SatuSehatController::tokenSehat();
        }

        if (!cache()->has('base_url')) {
            $setting = Setting::where('nama', 'satusehat')->first();
            cache()->put('base_url', $setting->base_url, now()->addDay());
        }

        return $token;
    }

    public static function patientSehat($id)
    {
        if (is_numeric($id)) {
            $cek_lokal = PasienSehat::where('nik', $id)->first();

            if (!empty($cek_lokal)) {
                return $cek_lokal->satu_sehat_id;
            } else {
                $access_token = SatuSehatController::getTokenSehat();
                try {
                    $baseUrl = cache()->get('base_url');
                    $client = new Client(['base_uri' => $baseUrl, 'timeout' => 5]);

                    $ping = SatuSehatController::pingSatuSehat();
                    if ($ping) {
                        $client = new \GuzzleHttp\Client(['base_uri' => $baseUrl]);
                        $response = $client->request('GET', 'fhir-r4/v1/Patient?identifier=https://fhir.kemkes.go.id/id/nik|' . $id, [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ]
                        ]);
                    } else {
                        Session::flash('error', 'Server Satu Sehat tidak merespon!');
                        return null;
                    }
                } catch (RequestException $e) {
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();
                        $test = json_decode((string) $response->getBody());
                        Session::flash('error', 'Ambil id pasien gagal!');

                        goto SimpanError;
                    } else {
                        Session::flash('error', 'Server tidak respon!');
                        return null;
                    }
                }

                $data = json_decode($response->getBody());

                if ($data->total == 1) {
                    if (!empty($data->entry)) {
                        foreach ($data->entry as $responseData) {
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

                    // Buat request create pasien baru di Satu Sehat
                    // self::createPatient($id);

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

    public static function createPatient($id)
    {
        $access_token = SatuSehatController::getTokenSehat();
        $baseUrl = cache()->get('base_url');

        try {
            $pasienData = DB::connection('mysqlkhanza')->table('pasien')
                ->leftJoin('kabupaten', 'kabupaten.kd_kab', '=', 'pasien.kd_kab')
                ->leftJoin('kecamatan', 'kecamatan.kd_kec', '=', 'pasien.kd_kec')
                ->leftJoin('kelurahan', 'kelurahan.kd_kel', '=', 'pasien.kd_kel')
                ->leftJoin('propinsi', 'propinsi.kd_prop', '=', 'pasien.kd_prop')
                ->where('pasien.no_ktp', $id)
                ->first();

            $pecahKota = explode(' ', trim($pasienData->nm_kab));
            if (count($pecahKota) > 1) {
                if (in_array(strtoupper($pecahKota[0]), ['KOTA', 'KABUPATEN', 'KAB.', 'KAB'])) {
                    array_shift($pecahKota);
                }
                $pasienData->nm_kab = implode(' ', $pecahKota);
            }

            // dd($pasienData);

            $data_json = [
                "resourceType" => "Patient",
                "meta" => [
                    "profile" => [
                        "https://fhir.kemkes.go.id/r4/StructureDefinition/Patient"
                    ]
                ],
                "identifier" => [
                    [
                        "use" => "official",
                        "system" => "https://fhir.kemkes.go.id/id/nik",
                        "value" => "$pasienData->no_ktp"
                    ]
                ],
                "active" => true,
                "name" => [
                    [
                        "use" => "official",
                        "text" => "$pasienData->nm_pasien"
                    ]
                ],
                "gender" => $pasienData->jk == "L" ? "male" : "female",
                "birthDate" => "$pasienData->tgl_lahir",
                "deceasedBoolean" => false,
                "address" => [
                    [
                        "use" => "home",
                        "line" => [
                            "$pasienData->alamat Kalurahan $pasienData->nm_kel Kecamatan $pasienData->nm_kec Kabupaten $pasienData->nm_kab Provinsi $pasienData->nm_prop"
                        ],
                        "city" => "$pasienData->nm_kab",
                        // "postalCode" => "57375",
                        "country" => "ID",
                        "extension" => [
                            [
                                "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                                "extension" => [
                                    [
                                        "url" => "province",
                                        "valueCode" => "$pasienData->kd_prop"
                                    ],
                                    [
                                        "url" => "city",
                                        "valueCode" => "$pasienData->kd_kab"
                                    ],
                                    [
                                        "url" => "district",
                                        "valueCode" => "$pasienData->kd_kec"
                                    ],
                                    [
                                        "url" => "village",
                                        "valueCode" => "$pasienData->kd_kel"
                                    ]
                                    // ,
                                    // [
                                    //     "url" => "rt",
                                    //     "valueCode" => "2"
                                    // ],
                                    // [
                                    //     "url" => "rw",
                                    //     "valueCode" => "5"
                                    // ]
                                ]
                            ]
                        ]
                    ]
                ],
                "maritalStatus" => [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/v3-MaritalStatus",
                            "code" => $pasienData->stts_nikah == "MENIKAH" ? "M" : "U",
                            "display" => $pasienData->stts_nikah == "MENIKAH" ? "Married" : "Unmarried"
                        ]
                    ],
                    "text" => $pasienData->stts_nikah == "MENIKAH" ? "Married" : "Unmarried"
                ],
                "multipleBirthInteger" => 0,
                // "contact" => [
                //     [
                //         "relationship" => [
                //             [
                //                 "coding" => [
                //                     [
                //                         "system" => "http://terminology.hl7.org/CodeSystem/v2-0131",
                //                         "code" => "C"
                //                     ]
                //                 ]
                //             ]
                //         ],
                //         "name" => [
                //             "use" => "official",
                //             "text" => "Jane Smith"
                //         ],
                //         "telecom" => [
                //             [
                //                 "system" => "phone",
                //                 "value" => "0690383372",
                //                 "use" => "mobile"
                //             ]
                //         ]
                //     ]
                // ],
                "communication" => [
                    [
                        "language" => [
                            "coding" => [
                                [
                                    "system" => "urn:ietf:bcp:47",
                                    "code" => "id-ID",
                                    "display" => "Indonesian"
                                ]
                            ],
                            "text" => "Indonesian"
                        ],
                        "preferred" => true
                    ]
                ]
            ];

            $client = new \GuzzleHttp\Client(['base_uri' => $baseUrl]);
            $response = $client->request('POST', 'fhir-r4/v1/Patient', [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ],
                'json' => $data_json
            ]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
                dd($test, $data_json);

                $simpan = new LogErrorSatuSehat();
                $simpan->subject = 'Create Patient Satu Sehat';
                $simpan->keterangan = "Error Create Patient Satu Sehat: (" . $test . ")";
                $simpan->save();
                return;
            } else {
                return json_encode(['error' => 'Server tidak respon!']);
            }
        }

        dd($response);
    }

    public static function practitioner($id)
    {
        if (is_numeric($id)) {
            $cek_lokal = PraktisiSehat::where('nik', $id)->first();

            if (!empty($cek_lokal)) {
                return $cek_lokal->satu_sehat_id;
            } else {
                $access_token = SatuSehatController::getTokenSehat();
                try {
                    $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
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

                        return null;
                    }
                    Session::flash('error', 'Ambil respon practitioner gagal!');

                    goto ErrorPractitioner;
                }

                $data = json_decode($response->getBody());

                if ($data->total == 1) {

                    foreach ($data->entry as $responseData) {
                        $data = $responseData->resource->id;
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
        // $data = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
        //     ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
        //     ->select(
        //         'diagnosa_pasien.no_rawat',
        //         'diagnosa_pasien.kd_penyakit',
        //         'diagnosa_pasien.status',
        //         'diagnosa_pasien.prioritas',
        //         'penyakit.nm_penyakit'
        //     )
        //     ->where('diagnosa_pasien.no_rawat', $id)
        //     ->where('diagnosa_pasien.status', 'Ralan')
        //     ->orderBy('prioritas', 'ASC')
        //     ->limit(10)
        //     ->get();

        // $data = DB::connection('mysqlkhanza')->table(DB::raw("(
        //                 SELECT
        //                     dp.no_rawat,
        //                     dp.kd_penyakit,
        //                     dp.status,
        //                     dp.prioritas,
        //                     p.nm_penyakit,
        //                     p.im,
        //                     'IDRG' as sumber,
        //                     1 as urutan_sumber
        //                 FROM diagnosa_pasien dp
        //                 LEFT JOIN penyakit p ON p.kd_penyakit = dp.kd_penyakit
        //                 WHERE dp.status = 'Ralan' AND dp.no_rawat = '$id'

        //                 UNION ALL

        //                 SELECT
        //                     dpi.no_rawat,
        //                     dpi.kd_penyakit,
        //                     dpi.status,
        //                     dpi.prioritas,
        //                     p.nm_penyakit,
        //                     p.im,
        //                     'INACBG' as sumber,
        //                     2 as urutan_sumber
        //                 FROM diagnosa_pasien_inacbg dpi
        //                 LEFT JOIN penyakit p ON p.kd_penyakit = dpi.kd_penyakit
        //                 WHERE dpi.status = 'Ralan' AND dpi.no_rawat = '$id'
        //             ) as diagnosa_all"))
        //     ->where('im', '0')
        //     ->orderBy('urutan_sumber')
        //     ->orderBy('prioritas')
        //     ->get();

        $data = DB::connection('mysqlkhanza')->table(DB::raw("(
                    SELECT * FROM (
                        SELECT
                            dp.no_rawat,
                            dp.kd_penyakit,
                            dp.status,
                            p.nm_penyakit,
                            p.im,
                            'IDRG' AS sumber,
                            dp.prioritas
                        FROM diagnosa_pasien dp
                        LEFT JOIN penyakit p ON p.kd_penyakit = dp.kd_penyakit
                        WHERE dp.status = 'Ralan' AND dp.no_rawat = '$id'

                        UNION ALL

                        SELECT
                            dpi.no_rawat,
                            dpi.kd_penyakit,
                            dpi.status,
                            p.nm_penyakit,
                            p.im,
                            'INACBG' AS sumber,
                            dpi.prioritas
                        FROM diagnosa_pasien_inacbg dpi
                        LEFT JOIN penyakit p ON p.kd_penyakit = dpi.kd_penyakit
                        WHERE dpi.status = 'Ralan' AND dpi.no_rawat = '$id'
                    ) AS gabungan_semua
                    ORDER BY FIELD(sumber, 'IDRG', 'INACBG')
                ) AS gabungan_urut"))
            ->selectRaw('no_rawat, kd_penyakit, status, nm_penyakit, im,sumber, MIN(prioritas) as prioritas')
            ->where('im', '0')
            ->groupBy('kd_penyakit')
            ->orderBy('prioritas')
            ->get();

        // dd($data);

        if (!empty($data)) {
            // if (stripos($data->kd_penyakit, "Z09") !== false) {
            //     $data2 = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
            //         ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            //         ->select(
            //             'diagnosa_pasien.no_rawat',
            //             'diagnosa_pasien.kd_penyakit',
            //             'diagnosa_pasien.status',
            //             'diagnosa_pasien.prioritas',
            //             'penyakit.nm_penyakit'
            //         )
            //         ->where('diagnosa_pasien.no_rawat', $id)
            //         ->where('diagnosa_pasien.status', 'Ralan')
            //         ->where('diagnosa_pasien.prioritas', '2')
            //         ->first();
            // } else {
            //     $data2 = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
            //         ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            //         ->select(
            //             'diagnosa_pasien.no_rawat',
            //             'diagnosa_pasien.kd_penyakit',
            //             'diagnosa_pasien.status',
            //             'diagnosa_pasien.prioritas',
            //             'penyakit.nm_penyakit'
            //         )
            //         ->where('diagnosa_pasien.no_rawat', $id)
            //         ->where('diagnosa_pasien.status', 'Ralan')
            //         ->orderBy('prioritas', 'ASC')
            //         ->skip(1)
            //         ->limit(10)
            //         ->get();

            //     $diagnosaExclude = ['z88.8', 'J44.3', 'F31.31'];

            //     foreach ($data2 as $list) {
            //         if (in_array($list->kd_penyakit, $diagnosaExclude)) {
            //             goto next;
            //         } else {
            //             return $list;
            //         }
            //         next:
            //     }
            // }
            $diagnosaExclude = explode(',', ENV('EXCLUDE_DIAGNOSA'));

            $filtered = $data
                ->filter(fn($item) => !in_array($item->kd_penyakit, $diagnosaExclude)) // step 1: exclude
                ->sortBy('prioritas')
                ->values() // reset index agar dimulai dari 0
                ->take(2); // step 2: ambil 2 pertama

            // dd($filtered[0]);

            $cek = LogErrorSatuSehat::where('subject', 'Diagnosa Primer Pasien')
                ->where('keterangan', 'like', "%$id%")
                ->delete();

            if (!empty($filtered[0]))
                return $filtered[0];
            else {
                return null;
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
    }

    public function getDiagnosisSekunderRalan($id)
    {
        // $data = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
        //     ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
        //     ->select(
        //         'diagnosa_pasien.no_rawat',
        //         'diagnosa_pasien.kd_penyakit',
        //         'diagnosa_pasien.status',
        //         'diagnosa_pasien.prioritas',
        //         'penyakit.nm_penyakit'
        //     )
        //     ->where('diagnosa_pasien.no_rawat', $id)
        //     ->where('diagnosa_pasien.status', 'Ralan')
        //     // ->where('diagnosa_pasien.prioritas', '1')
        //     ->where('diagnosa_pasien.status', 'Ralan')
        //     ->orderBy('prioritas', 'ASC')
        //     ->limit(10)
        //     ->get();

        // $data = DB::connection('mysqlkhanza')->table(DB::raw("(
        //                 SELECT
        //                     dp.no_rawat,
        //                     dp.kd_penyakit,
        //                     dp.status,
        //                     dp.prioritas,
        //                     p.nm_penyakit,
        //                     p.im,
        //                     'IDRG' as sumber,
        //                     1 as urutan_sumber
        //                 FROM diagnosa_pasien dp
        //                 LEFT JOIN penyakit p ON p.kd_penyakit = dp.kd_penyakit
        //                 WHERE dp.status = 'Ralan' AND dp.no_rawat = '$id'

        //                 UNION ALL

        //                 SELECT
        //                     dpi.no_rawat,
        //                     dpi.kd_penyakit,
        //                     dpi.status,
        //                     dpi.prioritas,
        //                     p.nm_penyakit,
        //                     p.im,
        //                     'INACBG' as sumber,
        //                     2 as urutan_sumber
        //                 FROM diagnosa_pasien_inacbg dpi
        //                 LEFT JOIN penyakit p ON p.kd_penyakit = dpi.kd_penyakit
        //                 WHERE dpi.status = 'Ralan' AND dpi.no_rawat = '$id'
        //             ) as diagnosa_all"))
        //     ->where('im', '0')
        //     ->where('status', 'Ralan')
        //     ->orderBy('urutan_sumber')
        //     ->orderBy('prioritas')
        //     ->get();

        $data = DB::connection('mysqlkhanza')->table(DB::raw("(
                    SELECT * FROM (
                        SELECT
                            dp.no_rawat,
                            dp.kd_penyakit,
                            dp.status,
                            p.nm_penyakit,
                            p.im,
                            'IDRG' AS sumber,
                            dp.prioritas
                        FROM diagnosa_pasien dp
                        LEFT JOIN penyakit p ON p.kd_penyakit = dp.kd_penyakit
                        WHERE dp.status = 'Ralan' AND dp.no_rawat = '$id'

                        UNION ALL

                        SELECT
                            dpi.no_rawat,
                            dpi.kd_penyakit,
                            dpi.status,
                            p.nm_penyakit,
                            p.im,
                            'INACBG' AS sumber,
                            dpi.prioritas
                        FROM diagnosa_pasien_inacbg dpi
                        LEFT JOIN penyakit p ON p.kd_penyakit = dpi.kd_penyakit
                        WHERE dpi.status = 'Ralan' AND dpi.no_rawat = '$id'
                    ) AS gabungan_semua
                    ORDER BY FIELD(sumber, 'IDRG', 'INACBG')
                ) AS gabungan_urut"))
            ->selectRaw('no_rawat, kd_penyakit, status, nm_penyakit, im,sumber, MIN(prioritas) as prioritas')
            ->where('im', '0')
            ->groupBy('kd_penyakit')
            ->orderBy('prioritas')
            ->get();

        if (!empty($data)) {
            // if ($data->kd_penyakit != "Z09.8") {
            //     $data2 = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
            //         ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            //         ->select(
            //             'diagnosa_pasien.no_rawat',
            //             'diagnosa_pasien.kd_penyakit',
            //             'diagnosa_pasien.status',
            //             'diagnosa_pasien.prioritas',
            //             'penyakit.nm_penyakit'
            //         )
            //         ->where('diagnosa_pasien.no_rawat', $id)
            //         ->where('diagnosa_pasien.status', 'Ralan')
            //         ->orderBy('prioritas', 'ASC')
            //         ->skip(1)  // Melewatkan 5 baris pertama
            //         ->limit(10)
            //         ->get();


            //     return null;
            // } else {
            //     $data2 = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
            //         ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            //         ->select(
            //             'diagnosa_pasien.no_rawat',
            //             'diagnosa_pasien.kd_penyakit',
            //             'diagnosa_pasien.status',
            //             'diagnosa_pasien.prioritas',
            //             'penyakit.nm_penyakit'
            //         )
            //         ->where('diagnosa_pasien.no_rawat', $id)
            //         ->where('diagnosa_pasien.status', 'Ralan')
            //         ->orderBy('prioritas', 'ASC')
            //         ->skip(2)  // Melewatkan 5 baris pertama
            //         ->limit(10)
            //         ->get();
            //     foreach ($data2 as $list) {
            //         if (in_array($list->kd_penyakit, $diagnosaExclude)) {
            //             goto next2;
            //         } else {
            //             return $list;
            //         }
            //         next2:
            //     }
            //     return null;
            // }
            $diagnosaExclude = explode(',', ENV('EXCLUDE_DIAGNOSA'));

            $filtered = $data
                ->filter(fn($item) => !in_array($item->kd_penyakit, $diagnosaExclude)) // step 1: exclude
                ->sortBy('prioritas')
                ->values() // reset index agar dimulai dari 0
                ->take(2); // step 2: ambil 2 pertama

            // dd($filtered, $diagnosaExclude);

            if (!empty($filtered[1]))
                return $filtered[1];
            else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function getVital($id)
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
        // $data = DB::connection('mysqlkhanza')->table('prosedur_pasien')
        //     ->join('icd9', 'icd9.kode', '=', 'prosedur_pasien.kode')
        //     ->select(
        //         'prosedur_pasien.no_rawat',
        //         'prosedur_pasien.kode',
        //         'prosedur_pasien.status',
        //         'prosedur_pasien.prioritas',
        //         'icd9.deskripsi_panjang',
        //         'icd9.im'
        //     )
        //     ->where('prosedur_pasien.no_rawat', $id)
        //     ->where('prosedur_pasien.status', 'Ralan')
        //     ->where('prosedur_pasien.prioritas', '1')
        //     ->where('icd9.im', false)
        //     ->first();

        $data = DB::connection('mysqlkhanza')
            ->table(DB::raw("(
                    SELECT no_rawat, kode, status, prioritas
                    FROM prosedur_pasien
                    UNION
                    SELECT no_rawat, kode, status, prioritas
                    FROM prosedur_pasien_inacbg
                ) as prosedur"))
            ->join('icd9', 'icd9.kode', '=', 'prosedur.kode')
            ->select(
                'prosedur.no_rawat',
                'prosedur.kode',
                'prosedur.status',
                'prosedur.prioritas',
                'icd9.deskripsi_panjang',
                'icd9.im'
            )
            ->where('prosedur.no_rawat', $id)
            ->where('prosedur.status', 'Ralan')
            ->where('prosedur.prioritas', '1')
            ->where('icd9.im', '0')
            ->distinct() // supaya kode yang sama tidak dobel
            ->first();

        // dd($id, $data);

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

    public static function getListObat($resep)
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

    public static function getListObatRacik($resep)
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

    public static function getIdObat($kd_obat)
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

    public static function getDetailRacikan($noResep)
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

    public static function obatDiberikan($idRawat, $idObat)
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

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }

    public static function getLoinc($id)
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

    public static function getTemplateLoinc($id)
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

    public static function getAnswerLoinc($id, $hasil)
    {
        $data = MasterAnswerLoinc::where('loinc_number', $id)
            ->where('answer_list_link_type', 'like', "%$hasil%")
            ->first();

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

    public static function getSpecimen($id)
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

    public static function getWaktuLab($id)
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

    public static function pingSatuSehat()
    {
        // 1️⃣ Ambil access token dari cache
        $access_token = SatuSehatController::getTokenSehat();
        $baseUrl = cache()->get('base_url');
        $client = new Client(['base_uri' => $baseUrl, 'timeout' => 5]);

        // 2️⃣ Cek konektivitas endpoint dulu (HEAD / ping)
        try {
            $checkResponse = $client->request('GET', 'fhir-r4/v1/metadata', [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ],
            ]);

            if ($checkResponse->getStatusCode() !== 200) {
                return false;
            } else {
                return true;
            }
        } catch (RequestException $pingError) {
            // Jika gagal koneksi, log & batalkan
            if ($pingError->getMessage()) {
                LogErrorSatuSehat::create([
                    'subject' => 'Bundle Ralan',
                    'keterangan' => "Server Satu Sehat tidak dapat dijangkau (" . $pingError->getMessage() . ")"
                ]);
            }

            return false;
        } catch (ConnectException $connectError) {
            // Jika gagal koneksi, log & batalkan
            if ($connectError->getMessage()) {
                LogErrorSatuSehat::create([
                    'subject' => 'Bundle Ralan',
                    'keterangan' => "Server Satu Sehat tidak dapat dijangkau (" . $connectError->getMessage() . ")"
                ]);
            }

            Session::flash('error', 'Server Satu Sehat tidak dapat dijangkau. Pengiriman dibatalkan.');
            return false;
        }
    }

    public static function sendVitalSign($no_rawat, $encounter)
    {
        $vital = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pemeriksaan_ralan', 'pemeriksaan_ralan.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->select(
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'pemeriksaan_ralan.*'
            )
            ->where('pemeriksaan_ralan.no_rawat', $no_rawat)
            ->first();

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
                $temperature = floatval(36.8);
            }
        } else {
            $heartRate   = 75.0;
            $sistole     = 110.0;
            $diastole    = 70.0;
            $respiratory = 16.0;
            $temperature = 36.8;
        }

        if ($vital) {
            $idPasien = SatuSehatController::patientSehat($vital->ktp_pasien);
            $idPractition = SatuSehatController::practitioner($vital->ktp_dokter);
            $waktuPerawatan = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $vital->tgl_perawatan . ' ' . $vital->jam_rawat,
                'Asia/Jakarta' // timezone ASAL
            );

            $formatWaktu = $waktuPerawatan
                ->setTimezone('UTC')
                ->toW3cString();

            if (!empty($idPasien) && (!empty($idPractition)) && (!empty($formatWaktu))) {
                if (!empty($heartRate)) {
                    $dataNadi = [
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
                        "performer" => [
                            [
                                "reference" => "Practitioner/$idPractition"
                            ]
                        ],
                        "subject" => [
                            "reference" => "Patient/$idPasien",
                            "display" => "$vital->nm_pasien"
                        ],
                        "encounter" => [
                            "reference" => "Encounter/$encounter"
                        ],
                        "effectiveDateTime" => "$formatWaktu",
                        "issued" => "$formatWaktu",
                        "valueQuantity" => [
                            "value" => intval($heartRate),
                            "unit" => "beats/minute",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "/min"
                        ]
                    ];

                    //Send data
                    $access_token = SatuSehatController::getTokenSehat();
                    $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataNadi
                        ]);
                    } catch (ClientException $e) {
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            $body = (string) $response->getBody();
                            $test = json_decode($body);

                            if ($test && $test->issue[0]->code == 'duplicate') {
                                $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                                try {
                                    $response = $client->request('GET', 'fhir-r4/v1/Observation/encounter=' . $encounter, [
                                        'headers' => [
                                            'Authorization' => "Bearer {$access_token}"
                                        ]
                                    ]);

                                    $responseData = json_decode($response->getBody());
                                    if (!empty($responseData->entry)) {
                                        foreach ($responseData->entry as $entry) {
                                            if ($entry->resource->code->coding[0]->code == '8867-4') {
                                                // Temukan resource Observation dengan code 8867-4
                                                $heartObservationId = $entry->resource->id;

                                                // Lakukan update pada resource Observation tersebut
                                                if (!empty($heartObservationId)) {
                                                    $update = ResponseSatuSehat::where('noRawat', $no_rawat)->first();
                                                    $update->heart_id = $heartObservationId;
                                                    $update->save();
                                                };
                                            }
                                        }
                                    }

                                    goto KirimRespiratory;
                                } catch (ClientException $e) {
                                }
                            } else {
                                $message = 'error other';

                                LogErrorSatuSehat::create([
                                    'subject' => 'Update Vital Sign',
                                    'keterangan' => "Pengiriman data heartRate pasien no rawat : $no_rawat (" . $message . ")"
                                ]);
                            }

                            goto KirimRespiratory;
                        }
                    } catch (RequestException $e) {

                        $body = null;
                        if ($e->hasResponse()) {
                            $body = (string) $e->getResponse()->getBody();
                        }

                        Log::error('HTTP Error SatuSehat', [
                            'response' => $body
                        ]);

                        goto KirimRespiratory;
                    } catch (Throwable $e) {

                        Log::error('API Error', [
                            'message' => $e->getMessage()
                        ]);

                        goto KirimRespiratory;
                    }

                    $dataResponse = json_decode($response->getBody());

                    if (!empty($dataResponse->id)) {
                        $update = ResponseSatuSehat::where('noRawat', $no_rawat)->first();
                        $update->heart_id = $dataResponse->id;
                        $update->save();
                    };
                }

                KirimRespiratory:
                if (!empty($respiratory)) {
                    $dataPernafasan = [
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
                            "reference" => "Patient/$idPasien",
                            "display" => "$vital->nm_pasien"
                        ],
                        "performer" => [
                            [
                                "reference" => "Practitioner/$idPractition"
                            ]
                        ],
                        "encounter" => [
                            "reference" => "Encounter/$encounter"
                        ],
                        "effectiveDateTime" => "$formatWaktu",
                        "issued" => "$formatWaktu",
                        "valueQuantity" => [
                            "value" => intval($respiratory),
                            "unit" => "breaths/minute",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "/min"
                        ]
                    ];

                    //kirim data pernafasan
                    $access_token = SatuSehatController::getTokenSehat();
                    $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataPernafasan
                        ]);
                    } catch (ClientException $e) {
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();
                            $body = (string) $response->getBody();
                            $test = json_decode($body);
                            if ($test && $test->issue[0]) {
                                $message = $test->issue[0]->details->text;
                            } else {
                                $message = 'error other';
                            }

                            LogErrorSatuSehat::create([
                                'subject' => 'Update Vital Sign',
                                'keterangan' => "Pengiriman data Respiratory pasien no rawat : $no_rawat (" . $message . ")"
                            ]);

                            goto KirimSistole;
                        }
                    } catch (RequestException $e) {

                        $body = null;
                        if ($e->hasResponse()) {
                            $body = (string) $e->getResponse()->getBody();
                        }

                        Log::error('HTTP Error SatuSehat', [
                            'response' => $body
                        ]);

                        goto KirimSistole;
                    } catch (Throwable $e) {

                        Log::error('API Error', [
                            'message' => $e->getMessage()
                        ]);

                        goto KirimSistole;
                    }

                    $dataResponse = json_decode($response->getBody());

                    if (!empty($dataResponse->id)) {
                        $update = ResponseSatuSehat::where('noRawat', $no_rawat)->first();
                        $update->respiratory_id = $dataResponse->id;
                        $update->save();
                    };
                }

                KirimSistole:
                if (!empty($sistole)) {
                    $dataSistole = [
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
                            "reference" => "Patient/$idPasien",
                            "display" => "$vital->nm_pasien"
                        ],
                        "performer" => [
                            [
                                "reference" => "Practitioner/$idPractition"
                            ]
                        ],
                        "encounter" => [
                            "reference" => "Encounter/$encounter"
                        ],
                        "effectiveDateTime" => "$formatWaktu",
                        "issued" => "$formatWaktu",
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
                            "value" => intval($sistole),
                            "unit" => "mm[Hg]",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "mm[Hg]"
                        ]
                    ];

                    //kirim data sistole
                    $access_token = SatuSehatController::getTokenSehat();
                    $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataSistole
                        ]);
                    } catch (ClientException $e) {
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            // dd($response);
                            $body = (string) $response->getBody();
                            $test = json_decode($body);
                            if ($test && $test->issue[0]) {
                                $message = $test->issue[0]->details->text;
                            } else {
                                $message = 'error other';
                            }

                            LogErrorSatuSehat::create([
                                'subject' => 'Update Vital Sign',
                                'keterangan' => "Pengiriman data Sistole pasien no rawat : $no_rawat (" . $message . ")"
                            ]);

                            goto KirimTemperature;
                        }
                    }
                    // catch (RequestException $e) {

                    //     $body = null;
                    //     if ($e->hasResponse()) {
                    //         $body = (string) $e->getResponse()->getBody();
                    //     }

                    //     Log::error('HTTP Error SatuSehat', [
                    //         'response' => $body
                    //     ]);

                    //     goto KirimTemperature;
                    // } catch (Throwable $e) {

                    //     Log::error('API Error', [
                    //         'message' => $e->getMessage()
                    //     ]);

                    //     goto KirimTemperature;
                    // }

                    $dataResponse = json_decode($response->getBody());

                    if (!empty($dataResponse->id)) {
                        $update = ResponseSatuSehat::where('noRawat', $no_rawat)->first();
                        $update->systol_id = $dataResponse->id;
                        $update->save();
                    };
                }

                if (!empty($diastole)) {
                    $dataDiastol = [
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
                            "display" => "$vital->nm_pasien"
                        ],
                        "performer" => [
                            [
                                "reference" => "Practitioner/$idPractition"
                            ]
                        ],
                        "encounter" => [
                            "reference" => "Encounter/$encounter"
                        ],
                        "effectiveDateTime" => "$formatWaktu",
                        "issued" => "$formatWaktu",
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
                            "value" => intval($diastole),
                            "unit" => "mm[Hg]",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "mm[Hg]"
                        ]
                    ];

                    //kirim data diastole
                    $access_token = SatuSehatController::getTokenSehat();
                    $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataDiastol
                        ]);
                    } catch (ClientException $e) {
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();
                            $body = (string) $response->getBody();
                            $test = json_decode($body);

                            if ($test && $test->issue[0]->code == 'duplicate') {
                                $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                                try {
                                    $response = $client->request('GET', 'fhir-r4/v1/Observation?encounter=' . $encounter, [
                                        'headers' => [
                                            'Authorization' => "Bearer {$access_token}"
                                        ]
                                    ]);

                                    $responseData = json_decode($response->getBody());

                                    if (!empty($responseData->entry)) {
                                        foreach ($responseData->entry as $entry) {
                                            if ($entry->resource->code->coding[0]->code == '8462-4') {
                                                // Temukan resource Observation dengan code 8867-4
                                                $diastolObservationId = $entry->resource->id;

                                                // Lakukan update pada resource Observation tersebut
                                                if (!empty($diastolObservationId)) {
                                                    $update = ResponseSatuSehat::where('noRawat', $no_rawat)->first();
                                                    $update->diastol_id = $diastolObservationId;
                                                    $update->save();
                                                };
                                            }
                                        }
                                    }

                                    goto KirimTemperature;
                                } catch (ClientException $e) {
                                    dd($e->getMessage(), 'gagal ambil data duplicate diastole');
                                }
                            } else {
                                if ($test && $test->issue[0]) {
                                    $message = $test->issue[0]->details->text;
                                } else {
                                    $message = 'error other';
                                }

                                LogErrorSatuSehat::create([
                                    'subject' => 'Update Vital Sign',
                                    'keterangan' => "Pengiriman data Diastole pasien no rawat : $no_rawat (" . $message . ")"
                                ]);

                                goto KirimTemperature;
                            }
                        }
                    }

                    $dataResponse = json_decode($response->getBody());

                    if (!empty($dataResponse->id)) {
                        $update = ResponseSatuSehat::where('noRawat', $no_rawat)->first();
                        $update->diastol_id = $dataResponse->id;
                        $update->save();
                    };
                }

                KirimTemperature:
                if (!empty($temperature)) {
                    $dataSuhu = [
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
                            "reference" => "Patient/$idPasien",
                            "display" => "$vital->nm_pasien"
                        ],
                        "performer" => [
                            [
                                "reference" => "Practitioner/$idPractition"
                            ]
                        ],
                        "encounter" => [
                            "reference" => "Encounter/$encounter"
                        ],
                        "effectiveDateTime" => "$formatWaktu",
                        "issued" => "$formatWaktu",
                        "valueQuantity" => [
                            "value" => floatval($temperature),
                            "unit" => "C",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "Cel"
                        ]
                    ];

                    //kirim data suhu
                    $access_token = SatuSehatController::getTokenSehat();
                    $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataSuhu
                        ]);
                    } catch (ClientException $e) {
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();
                            $body = (string) $response->getBody();
                            $test = json_decode($body);
                            if ($test && $test->issue[0]) {
                                $message = $test->issue[0]->details->text;
                            } else {
                                $message = 'error other';
                            }

                            LogErrorSatuSehat::create([
                                'subject' => 'Update Vital Sign',
                                'keterangan' => "Pengiriman data Diastole pasien no rawat : $no_rawat (" . $message . ")"
                            ]);
                        }
                    } catch (RequestException $e) {

                        $body = null;
                        if ($e->hasResponse()) {
                            $body = (string) $e->getResponse()->getBody();
                        }

                        Log::error('HTTP Error SatuSehat', [
                            'response' => $body
                        ]);

                        return;
                    } catch (Throwable $e) {

                        Log::error('API Error', [
                            'message' => $e->getMessage()
                        ]);

                        return;
                    }

                    $dataResponse = json_decode($response->getBody());

                    if (!empty($dataResponse->id)) {
                        $update = ResponseSatuSehat::where('noRawat', $no_rawat)->first();
                        $update->temperature_id = $dataResponse->id;
                        $update->save();
                    };
                }
            }
        }
    }

    public function sendCarePlan2($no_rawat, $encounter)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pemeriksaan_ralan', 'pemeriksaan_ralan.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->select(
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'pemeriksaan_ralan.*'
            )
            ->where('pemeriksaan_ralan.no_rawat', $no_rawat)
            ->first();

        if ($data && $encounter != null && $encounter != 'duplicate') {
            $idPasien = SatuSehatController::patientSehat($data->ktp_pasien);
            $idPractition = SatuSehatController::practitioner($data->ktp_dokter);
            $waktuPerawatan = new Carbon("$data->tgl_perawatan $data->jam_rawat");
            $formatWaktuPerawatan = $waktuPerawatan->setTimezone('UTC')->toW3cString();

            if ($data && ($data->instruksi != null || $data->instruksi != '-')) {

                $data_json = [
                    "resourceType" => "CarePlan",
                    "status" => "active",
                    "intent" => "plan",
                    "category" => [
                        [
                            "coding" => [
                                [
                                    "system" => "http://snomed.info/sct",
                                    "code" => "736271009",
                                    "display" => "Outpatient care plan"
                                ]
                            ]
                        ]
                    ],
                    "title" => "Instruksi Medik dan Keperawatan Pasien",
                    "description" => "$data->instruksi",
                    "subject" => [
                        "reference" => "Patient/$idPasien",
                        "display" => "$data->nm_pasien"
                    ],
                    "encounter" => [
                        "reference" => "Encounter/$encounter"
                    ],
                    "created" => "$formatWaktuPerawatan",
                    "author" => [
                        "reference" => "Practitioner/$idPractition",
                        "display" => "$data->nama_dokter"
                    ]
                    // ,
                    // "goal" => [
                    //     [
                    //         "reference" => "Goal/{{Goal_TujuanPerawatan}}"
                    //     ]
                    // ]
                ];

                //Kirim/Create Service Request
                // SatuSehatController::getTokenSehat();
                $access_token = SatuSehatController::getTokenSehat();
                $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);

                try {
                    $response = $client->request('POST', 'fhir-r4/v1/CarePlan', [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}",
                            'Accept'        => 'application/json',
                        ],
                        'json' => $data_json
                    ]);
                } catch (BadResponseException $e) {

                    if ($e->hasResponse()) {
                        $response = $e->getResponse();
                        $body = (string) $response->getBody();
                        $test = json_decode($body);

                        // default pesan
                        $pesan = 'Unknown error';

                        if (isset($test->issue[0]->details->text)) {
                            $pesan = $test->issue[0]->details->text;
                        }

                        // 🔁 HANDLE DUPLICATE
                        if (isset($test->issue[0]->code) && $test->issue[0]->code === 'duplicate') {
                            $check = ResponseSatuSehat::where('encounter_id', $encounter)->first();
                            if ($check) {
                                $check->careplan_id = 'duplicate';
                                $check->save();
                            }
                        }

                        // 🧾 SIMPAN LOG
                        $error = new LogErrorSatuSehat();
                        $error->subject = 'Care Plan';
                        $error->keterangan = $no_rawat . ' error kirim "' . $pesan . '"';
                        $error->save();
                    }

                    return;
                } catch (RequestException $e) {

                    $body = null;
                    if ($e->hasResponse()) {
                        $body = (string) $e->getResponse()->getBody();
                    }

                    Log::error('HTTP Error SatuSehat', [
                        'response' => $body
                    ]);

                    return;
                } catch (Throwable $e) {

                    Log::error('API Error', [
                        'message' => $e->getMessage()
                    ]);

                    return;
                }

                $bodyResponse = json_decode($response->getBody());

                if ($bodyResponse && !empty($bodyResponse->id)) {
                    $update = ResponseSatuSehat::where('encounter_id', $encounter)
                        ->first();
                    $update->careplan_id = $bodyResponse->id;
                    $update->save();
                }
            }
        }
    }

    public function sendCondition($no_rawat, $encounter)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pemeriksaan_ralan', 'pemeriksaan_ralan.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->select(
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'pemeriksaan_ralan.*'
            )
            ->where('pemeriksaan_ralan.no_rawat', $no_rawat)
            ->first();

        $diagnosaPrimer = SatuSehatController::getDiagnosisPrimerRalan($no_rawat);
        $diagnosaSekunder = SatuSehatController::getDiagnosisSekunderRalan($no_rawat);

        if ($data && $encounter != null && $encounter != 'duplicate') {
            $idPasien = SatuSehatController::patientSehat($data->ktp_pasien);
            $idPractition = SatuSehatController::practitioner($data->ktp_dokter);
            $waktuPerawatan = new Carbon("$data->tgl_perawatan $data->jam_rawat");
            $formatWaktuPerawatan = $waktuPerawatan->setTimezone('UTC')->toW3cString();

            if ($diagnosaPrimer != null) {
                $diagnosis1 = [
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
                        "display" => "$data->nm_pasien"
                    ],
                    "encounter" => [
                        "reference" => "Encounter/$encounter"
                    ],
                    "onsetDateTime" => "$formatWaktuPerawatan",
                    "recordedDate" => "$formatWaktuPerawatan"
                ];

                $access_token = SatuSehatController::getTokenSehat();
                $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                try {
                    $response = $client->request('POST', 'fhir-r4/v1/Condition', [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}"
                        ],
                        'json' => $diagnosis1
                    ]);
                } catch (BadResponseException $e) {
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();
                        $body = (string) $response->getBody();
                        $test = json_decode($body);

                        if ($test && $test->issue[0]->code) {
                            $pesan = $test->issue[0];

                            if ($pesan->code == 'duplicate') {
                                $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                                try {
                                    $response = $client->request('GET', 'fhir-r4/v1/Condition?encounter=' . $encounter, [
                                        'headers' => [
                                            'Authorization' => "Bearer {$access_token}"
                                        ]
                                    ]);
                                } catch (BadResponseException $e) {
                                    if ($e->hasResponse()) {
                                        $response = $e->getResponse();
                                        $body = (string) $response->getBody();
                                        $test = json_decode($body);

                                        if ($test && $test->issue[0]) {
                                            $message = $test->issue[0]->details->text;
                                        } else {
                                            $message = 'error other';
                                        }

                                        LogErrorSatuSehat::create([
                                            'subject' => 'Condition Duplicate',
                                            'keterangan' => "Pengiriman data Condition pasien no rawat : $no_rawat (" . $message . ")"
                                        ]);

                                        return;
                                    }
                                }

                                $bodyResponse = json_decode($response->getBody());

                                if ($bodyResponse && !empty($bodyResponse->entry[0]->resource->id)) {
                                    $update = ResponseSatuSehat::where('encounter_id', $encounter)
                                        ->first();
                                    if ($update && $update->condition_id == null) {
                                        $update->condition_id = $bodyResponse->entry[0]->resource->id;
                                    } else {
                                        $update->condition2_id = $bodyResponse->entry[0]->resource->id;
                                    }
                                    $update->save();
                                }

                                return;
                            }

                            $pesan = $test->issue[0]->details->text;
                        } else {
                            $pesan = 'pola baru error';
                        }

                        $error = new LogErrorSatuSehat();
                        $error->subject = 'Diagnosis Primer';
                        $error->keterangan = $data->no_rawat . ' error kirim "' . $pesan . '"';
                        $error->save();
                    }

                    return;
                } catch (RequestException $e) {

                    $body = null;
                    if ($e->hasResponse()) {
                        $body = (string) $e->getResponse()->getBody();
                    }

                    dd($body);

                    LogErrorSatuSehat::create([
                        'subject' => 'Diagnosis Primer',
                        'keterangan' => $data->no_rawat . ' error kirim "' . $body . '"'
                    ]);

                    return;
                } catch (Throwable $e) {

                    LogErrorSatuSehat::create([
                        'subject' => 'Diagnosis Primer',
                        'keterangan' => $data->no_rawat . ' error kirim "' . $e->getMessage() . '"'
                    ]);

                    return;
                }

                $bodyResponse = json_decode($response->getBody());

                if ($bodyResponse && !empty($bodyResponse->id)) {
                    $update = ResponseSatuSehat::where('encounter_id', $encounter)
                        ->first();
                    $update->condition_id = $bodyResponse->id;
                    $update->save();

                    // dd('Sukses kirim diagnosa primer dengan id Condition : ' . $bodyResponse->id);
                }
            }

            if ($diagnosaSekunder != null) {
                $diagnosis = [
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
                        "display" => "$data->nm_pasien"
                    ],
                    "encounter" => [
                        "reference" => "Encounter/$encounter"
                    ],
                    "onsetDateTime" => "$formatWaktuPerawatan",
                    "recordedDate" => "$formatWaktuPerawatan"
                ];

                $access_token = SatuSehatController::getTokenSehat();
                $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                try {
                    $response = $client->request('POST', 'fhir-r4/v1/Condition', [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}"
                        ],
                        'json' => $diagnosis
                    ]);
                } catch (BadResponseException $e) {
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();
                        $body = (string) $response->getBody();
                        $test = json_decode($body);
                        if ($test && $test->issue[0]->code) {
                            $pesan = $test->issue[0];

                            if ($pesan->code == 'duplicate') {
                                $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                                try {
                                    $response = $client->request('GET', 'fhir-r4/v1/Condition?encounter=' . $encounter, [
                                        'headers' => [
                                            'Authorization' => "Bearer {$access_token}"
                                        ]
                                    ]);
                                } catch (BadResponseException $e) {
                                    if ($e->hasResponse()) {
                                        $response = $e->getResponse();
                                        $body = (string) $response->getBody();
                                        $test = json_decode($body);

                                        // dd($test);
                                        if ($test && $test->issue[0]) {
                                            $message = $test->issue[0]->details->text;
                                        } else {
                                            $message = 'error other';
                                        }

                                        LogErrorSatuSehat::create([
                                            'subject' => '2nd Condition Duplicate',
                                            'keterangan' => "Pengiriman data 2nd Condition pasien no rawat : $no_rawat (" . $message . ")"
                                        ]);

                                        return;
                                    }
                                }

                                $bodyResponse = json_decode($response->getBody());

                                if ($bodyResponse && !empty($bodyResponse->id)) {
                                    $update = ResponseSatuSehat::where('encounter_id', $encounter)
                                        ->first();
                                    $update->condition2_id = $bodyResponse->entry[0]->resource->id;
                                    $update->save();
                                }

                                return;
                            }

                            if ($test && $test->issue[0]) {
                                $pesan = $test->issue[0]->details->text;
                            }
                        } else {
                            $pesan = 'pola baru error';
                        }

                        $error = new LogErrorSatuSehat();
                        $error->subject = 'Diagnosis Sekunder';
                        $error->keterangan = $data->no_rawat . ' error kirim "' . $pesan . '"';
                        $error->save();
                    }

                    return;
                } catch (RequestException $e) {

                    $body = null;
                    if ($e->hasResponse()) {
                        $body = (string) $e->getResponse()->getBody();
                    }

                    LogErrorSatuSehat::create([
                        'subject' => 'Diagnosis Sekunder',
                        'keterangan' => $data->no_rawat . ' error kirim "' . $body . '"'
                    ]);

                    return;
                } catch (Throwable $e) {

                    LogErrorSatuSehat::create([
                        'subject' => 'Diagnosis Sekunder',
                        'keterangan' => $data->no_rawat . ' error kirim "' . $e->getMessage() . '"'
                    ]);

                    return;
                }

                $bodyResponse = json_decode($response->getBody());

                if ($bodyResponse && !empty($bodyResponse->id)) {
                    $update = ResponseSatuSehat::where('encounter_id', $encounter)
                        ->first();
                    $update->condition2_id = $bodyResponse->id;
                    $update->save();

                    // dd('Sukses kirim diagnosa sekunder dengan id Condition : ' . $bodyResponse->id);
                }
            }
        }
    }

    public function sendSingleDiagnosa($id)
    {
        $no_rawat = Crypt::decrypt($id);
        $dataKiriman = ResponseSatuSehat::where('noRawat', $no_rawat)->first();
        if ($dataKiriman && $dataKiriman->encounter_id) {
            SatuSehatController::sendCondition($no_rawat, $dataKiriman->encounter_id);
            return redirect()->route('satuSehat.checkRajalDetail', Crypt::encrypt($no_rawat))
                ->with('success', 'Pengiriman Data Diagnosa Satu Sehat Berhasil');
        } else {
            return redirect()->route('satuSehat.checkRajalDetail', Crypt::encrypt($no_rawat))
                ->with('error', 'Data Encounter Belum Dikirim ke Satu Sehat');
        }
    }

    public function sendProcedure($no_rawat, $encounter)
    {
        $procedurePasien = SatuSehatController::getProcedureRalan($no_rawat);
        $diagnosaPrimer = SatuSehatController::getDiagnosisPrimerRalan($no_rawat);

        if ($procedurePasien != null && $diagnosaPrimer != null) {

            $dataPasien = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('pemeriksaan_ralan', 'pemeriksaan_ralan.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
                ->select(
                    'reg_periksa.no_rawat',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.jam_reg',
                    'pasien.nm_pasien',
                    'pasien.no_ktp as ktp_pasien',
                    'pegawai.no_ktp as ktp_dokter',
                    'pegawai.nama as nama_dokter',
                    'pemeriksaan_ralan.tgl_perawatan',
                    'pemeriksaan_ralan.jam_rawat'
                )
                ->where('reg_periksa.no_rawat', $no_rawat)
                ->first();

            $idPasien = SatuSehatController::patientSehat($dataPasien->ktp_pasien);
            $idPractition = SatuSehatController::practitioner($dataPasien->ktp_dokter);
            // $waktu_registrasi = new Carbon("$dataPasien->tgl_registrasi $dataPasien->jam_reg");
            // $formatWaktuMulai = $waktu_registrasi->setTimezone('UTC')->toW3cString();
            // $waktuPerawatan = new Carbon("$dataPasien->tgl_perawatan $dataPasien->jam_rawat");
            // $formatWaktuPerawatan = $waktuPerawatan->setTimezone('UTC')->toW3cString();
            // $formatDay = $waktuPerawatan->format('Y-m-d');

            $waktuRegistrasi = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                "{$dataPasien->tgl_registrasi} {$dataPasien->jam_reg}",
                'Asia/Jakarta'
            )->setTimezone('UTC');

            $waktuPerawatan = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                "{$dataPasien->tgl_perawatan} {$dataPasien->jam_rawat}",
                'Asia/Jakarta'
            )->setTimezone('UTC');

            // 🔒 validasi waktu
            if ($waktuPerawatan->lessThanOrEqualTo($waktuRegistrasi)) {
                $waktuPerawatan = $waktuRegistrasi->copy()->addMinutes(30);
            }

            // format untuk kirim ke API
            $formatWaktuMulai     = $waktuRegistrasi->toW3cString();
            $formatWaktuPerawatan = $waktuPerawatan->toW3cString();
            $formatDay            = $waktuPerawatan->format('Y-m-d');


            $procedure = [
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
                    "display" => "$dataPasien->nm_pasien"
                ],
                "encounter" => [
                    "reference" => "Encounter/$encounter",
                    "display" => "Tindakan yang dilakukan kepada pasien $dataPasien->nm_pasien pada $formatDay"
                ],
                "performedPeriod" => [
                    "start" => "$formatWaktuMulai",
                    "end" => "$formatWaktuPerawatan"
                ],
                "performer" => [
                    [
                        "actor" => [
                            "reference" => "Practitioner/$idPractition",
                            "display" => "$dataPasien->nama_dokter"
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

            ];

            //Kirim/Create Procedure
            $access_token = SatuSehatController::getTokenSehat();
            $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
            try {
                $response = $client->request('POST', 'fhir-r4/v1/Procedure', [
                    'headers' => [
                        'Authorization' => "Bearer {$access_token}"
                    ],
                    'json' => $procedure
                ]);
            } catch (BadResponseException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $body = (string) $response->getBody();
                    $test = json_decode($body);

                    if ($test && $test->issue[0]->details) {
                        $pesan = $test->issue[0];

                        if ($pesan->code == 'duplicate') {
                            $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                            try {
                                $response = $client->request('GET', 'fhir-r4/v1/Procedure?encounter=' . $encounter, [
                                    'headers' => [
                                        'Authorization' => "Bearer {$access_token}"
                                    ]
                                ]);
                            } catch (BadResponseException $e) {
                                if ($e->hasResponse()) {
                                    $response = $e->getResponse();
                                    $body = (string) $response->getBody();
                                    $test = json_decode($body);

                                    if ($test && $test->issue[0]) {
                                        $message = $test->issue[0]->details->text;
                                    } else {
                                        $message = 'error other';
                                    }

                                    LogErrorSatuSehat::create([
                                        'subject' => 'Procedure Duplicate',
                                        'keterangan' => "Pengiriman data Procedure pasien no rawat : $no_rawat (" . $message . ")"
                                    ]);

                                    return;
                                }
                            }

                            $bodyResponse = json_decode($response->getBody());

                            if ($bodyResponse && !empty($bodyResponse->id)) {
                                $update = ResponseSatuSehat::where('encounter_id', $encounter)
                                    ->first();
                                $update->procedure_id = $bodyResponse->entry[0]->resource->id;
                                $update->save();
                            }

                            return;
                        }
                    } else {
                        $pesan = 'pola baru error';
                    }

                    $error = new LogErrorSatuSehat();
                    $error->subject = 'Procedure Rawal Jalan';
                    $error->keterangan = $dataPasien->no_rawat . ' error kirim "' . $pesan . '"';
                    $error->save();
                }

                return;
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    $body = (string) $e->getResponse()->getBody();

                    dd($body, 'RequestException');

                    LogErrorSatuSehat::create([
                        'subject' => 'Procedure Duplicate',
                        'keterangan' => $body && $body->issue[0]->details ? $body->issue[0]->details->text : "Pengiriman data Procedure pasien no rawat : $no_rawat (" . $message . ")"
                    ]);
                }

                return;
            } catch (Throwable $e) {
                // dd($e->getMessage(), 'trowable');
                LogErrorSatuSehat::create([
                    'subject' => 'Procedure Rawal Jalan',
                    'keterangan' => $dataPasien->no_rawat . ' error kirim "' . $e->getMessage() . '"'
                ]);

                return;
            }

            $bodyResponse = json_decode($response->getBody());

            if ($bodyResponse && !empty($bodyResponse->id)) {
                $update = ResponseSatuSehat::where('encounter_id', $encounter)
                    ->first();
                $update->procedure_id = $bodyResponse->id;
                $update->save();
            }
        } else {
            return;
        }
    }
}
