<?php

namespace App\Http\Controllers;

use App\LogErrorSatuSehat;
use App\ResponseIgdSatuSehat;
use App\ResponseSatuSehat;
use App\TindakanIgdSatuSehat;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class IgdSehatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'IGD Satu Sehat');
        session()->put('cucu', 'Summary IGD');

        if (empty($request->get('awal'))) {
            $awal = Carbon::now();
            $akhir = Carbon::now();
        } else {
            $awal = new Carbon($request->get('awal'));
            $akhir = new Carbon($request->get('akhir'));
        }

        $dataPasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.status_bayar',
                'reg_periksa.kd_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
            )
            ->whereIn('reg_periksa.kd_poli', ['IGDK'])
            ->whereDate('tgl_registrasi', '>=', $awal)
            ->whereDate('tgl_registrasi', '<=', $akhir)
            ->get();

        $ktpList = $dataPasien->pluck('ktp_pasien')->unique();
        $idSehatMap = \App\PasienSehat::whereIn('nik', $ktpList)->pluck('satu_sehat_id', 'nik');

        $noRawatList = $dataPasien->pluck('no_rawat')->unique();
        $encounters = \App\ResponseIgdSatuSehat::whereIn('noRawat', $noRawatList)
            ->get()
            ->keyBy('noRawat');

        // $errorLog = LogErrorSatuSehat::whereDate('created_at', '>=', $awal)
        //     ->whereDate('created_at', '<=', $akhir)
        //     ->get();

        foreach ($dataPasien as $list) {
            $list->idSehat = $idSehatMap[$list->ktp_pasien] ?? null;
            $list->dataEncounter = $encounters[$list->no_rawat] ?? null;
        }

        return view('satu_sehat.summaryIgd', compact('dataPasien'));
    }

    public function sendEncounter(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'IGD Satu Sehat');
        session()->put('cucu', 'Client Kirim Encounter IGD');

        if (empty($request->get('tanggal'))) {
            $pasien_tanggal = Carbon::now()->format('Y-m-d');
            $kemarin = Carbon::yesterday()->format('Y-m-d');

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
                ->whereBetween('reg_periksa.tgl_registrasi', [$kemarin, $pasien_tanggal])
                ->where('poliklinik.nm_poli', 'like', '%IGD%')

                ->orderBy('reg_periksa.tgl_registrasi', 'ASC')
                ->get();
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
            $pasien_tanggal = Carbon::parse($tanggal)->format('Y-m-d');

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
                ->where('poliklinik.nm_poli', 'like', '%IGD%')
                ->whereDate('reg_periksa.tgl_registrasi', $pasien_tanggal)
                ->orderBy('reg_periksa.tgl_registrasi', 'ASC')
                ->get();
        }

        $idRs = env('IDRS');
        foreach ($data as $dataPengunjung) {
            $idPasien = SatuSehatController::patientSehat($dataPengunjung->ktp_pasien);
            $idPoli = SatuSehatController::getIdPoli($dataPengunjung->kd_poli);
            $idPractition = SatuSehatController::practitioner($dataPengunjung->ktp_dokter);
            $waktuMulai = Carbon::parse($dataPengunjung->tgl_registrasi . ' ' . $dataPengunjung->jam_reg)->locale('id');
            $waktuMulai->setTimezone('UTC');
            $formatMulai = Carbon::parse($waktuMulai)->format('Y-m-d') . 'T' . Carbon::parse($waktuMulai)->format('H:i:s') . '+00:00';

            $cekStatus = ResponseIgdSatuSehat::where('noRawat', $dataPengunjung->no_rawat)
                ->count();
            if (($cekStatus == 0) && (!empty($idPasien)) && (!empty($idPractition)) && (!empty($idPoli))) {

                $dataEncounter = [
                    "resourceType" => "Encounter",
                    "identifier" => [
                        [
                            "system" => "http://sys-ids.kemkes.go.id/encounter/$idRs",
                            "value" => "$dataPengunjung->no_rawat"
                        ]
                    ],
                    "status" => "in-progress",
                    "class" => [
                        "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                        "code" => "EMER",
                        "display" => "emergency"
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
                                "reference" => "Practitioner/$idPractition",
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
                                "reference" => "Location/$idPoli",
                                "display" => "Bed, Instalasi Gawat Darurat"
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
                            "status" => "in-progress",
                            "period" => [
                                "start" => "$formatMulai"
                            ]
                        ]
                    ],
                    "serviceProvider" => [
                        "reference" => "Organization/$idRs"
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
                } catch (ClientException $e) {
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();

                        $test = json_decode($response->getBody());

                        if ($test->issue[0]->code == 'duplicate') {
                            try {
                                $response = $client->request('GET', "fhir-r4/v1/Encounter?identifier=http://sys-ids.kemkes.go.id/encounter/$idRs|$dataPengunjung->no_rawat", [
                                    'headers' => [
                                        'Authorization' => "Bearer {$access_token}"
                                    ]
                                ]);
                            } catch (ClientException $e) {
                                if ($e->hasResponse()) {
                                    $response = $e->getResponse();

                                    $test = json_decode($response->getBody());

                                    dd($test, 'get encounter SALAH');
                                    //Simpan hasil get encounter untuk update data selanjutnya
                                    $encounterData = $test;
                                }
                            }

                            $responseData = json_decode($response->getBody());

                            if (!empty($responseData->entry[0]->resource->id)) {
                                $encounterId = $responseData->entry[0]->resource->id;
                                $cekSimpan = ResponseIgdSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->count();
                                if ($cekSimpan == 0) {
                                    $simpan = new ResponseIgdSatuSehat();
                                    $simpan->noRawat = $dataPengunjung->no_rawat;
                                    $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                                    $simpan->encounter_id = $encounterId;
                                    $simpan->save();
                                }
                            }
                        } else {
                            $log = new LogErrorSatuSehat();
                            $log->subject = 'IGD Encounter';
                            $log->keterangan = "pasien IGD no rawat $dataPengunjung->no_rawat, pesan error : " . $test->message ?? "pasien IGD no rawat $dataPengunjung->no_rawat Unknown error";
                            $log->save();
                        }
                    }

                    // Session::flash('error', $message);

                    goto KirimPasienLain;
                }

                $data = json_decode($response->getBody());

                if (!empty($data->id)) {
                    $simpan = new ResponseIgdSatuSehat();
                    $simpan->noRawat = $dataPengunjung->no_rawat;
                    $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                    $simpan->encounter_id = $data->id;
                    $simpan->save();
                }
            }

            KirimPasienLain:
        }

        $dataLog = ResponseIgdSatuSehat::whereDate('tgl_registrasi', $pasien_tanggal)->get();

        $action = route('satuSehatIgd.sendEncounter');

        return view('satu_sehat.client_igd', compact('dataLog', 'action'));
    }

    public function closeEncounter(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'IGD Satu Sehat');
        session()->put('cucu', 'Client Update Encounter IGD');
        set_time_limit(0);

        if (empty($request->get('tanggal'))) {
            $pasien_tanggal = Carbon::now()->format('Y-m-d');
            $kemarin = Carbon::yesterday()->format('Y-m-d');
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
            $pasien_tanggal = Carbon::parse($tanggal)->format('Y-m-d');
            $kemarin = Carbon::parse($tanggal)->subDay()->format('Y-m-d');
        }

        $dataEncounter = ResponseIgdSatuSehat::where('cara_keluar', null)
            ->whereBetween('tgl_registrasi', [$kemarin, $pasien_tanggal])
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
                        if ($dataTerkirim->triase_transportasi == null) {
                            IgdSehatController::sendTransportasiKedatangan($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                        }
                        if ($dataTerkirim->triase_kondisi == null) {
                            IgdSehatController::sendTiaseKondisi($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                        }
                        if ($dataTerkirim->asesmen_nyeri == null) {
                            IgdSehatController::sendAssesmenNyeri($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                        }
                        if ($dataTerkirim->asesmen_nadi == null) {
                            IgdSehatController::sendVitalSign($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                        }
                        if ($dataTerkirim->diagnosis_awal == null) {
                            IgdSehatController::sendDiagnosisAwal($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                        }
                        if ($dataTerkirim->diagnosis_awal != null && $dataTerkirim->tindakanIgdSatuSehat->count() == 0) {
                            IgdSehatController::sendTindakanIgd($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                        }
                        if ($dataTerkirim->kondisi_stabil == null) {
                            IgdSehatController::sendUpdateKepulangan($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                        }
                        if ($dataTerkirim->kondisi_stabil != null) {
                            $update = ResponseIgdSatuSehat::where('noRawat', $dataTerkirim->noRawat)->first();
                            $update->cara_keluar = 'IGD Pulang';
                            $update->save();
                        };
                    }
                } elseif ($cekPulang->status_lanjut == 'Ranap') {

                    if ($dataTerkirim->triase_transportasi == null) {
                        IgdSehatController::sendTransportasiKedatangan($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                    }
                    if ($dataTerkirim->triase_kondisi == null) {
                        IgdSehatController::sendTiaseKondisi($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                    }
                    if ($dataTerkirim->asesmen_nyeri == null) {
                        IgdSehatController::sendAssesmenNyeri($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                    }
                    if ($dataTerkirim->asesmen_nadi == null) {
                        IgdSehatController::sendVitalSign($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                    }
                    if ($dataTerkirim->service_request == null) {
                        IgdSehatController::sendServiceRequest($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                    }
                    if ($dataTerkirim->service_request != null) {
                        $update = ResponseIgdSatuSehat::where('noRawat', $dataTerkirim->noRawat)->first();
                        $update->cara_keluar = 'Rujuk Ranap';
                        $update->save();
                    };
                }
            }
        }

        $dataLog = ResponseIgdSatuSehat::whereDate('tgl_registrasi', $pasien_tanggal)
            ->orderBy('updated_at', 'DESC')
            ->get();

        $action = route('satuSehatIgd.closeEncounter');

        return view('satu_sehat.client_igd', compact('dataLog', 'action'));
    }

    public function sendTransportasiKedatangan($noRawat, $encounter)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('data_triase_igd', 'data_triase_igd.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->select(
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'data_triase_igd.no_rawat',
                'data_triase_igd.tgl_kunjungan',
                'data_triase_igd.cara_masuk',
                'data_triase_igd.alat_transportasi',
                'data_triase_igd.alasan_kedatangan',
                'data_triase_igd.kode_kasus',
                'data_triase_igd.tekanan_darah',
                'data_triase_igd.nadi',
                'data_triase_igd.pernapasan',
                'data_triase_igd.suhu',
                'data_triase_igd.saturasi_o2',
                'data_triase_igd.nyeri',
                'data_triase_igd.tb',
                'data_triase_igd.bb'
            )
            ->where('data_triase_igd.no_rawat', $noRawat)
            ->first();

        if (!empty($data)) {
            $idPasien = SatuSehatController::patientSehat($data->ktp_pasien);
            $idPractition = SatuSehatController::practitioner($data->ktp_dokter);
            $formatWaktu = Carbon::parse($data->tgl_kunjungan)->setTimezone('UTC')->toW3cString();

            if ($data->alat_transportasi == 'Sendiri') {
                $modeTransportasi = [
                    "system" => "http://snomed.info/sct",
                    "code" => "90748009",
                    "display" => "Motorcycle"
                ];
            } elseif ($data->alat_transportasi == 'AGD') {
                $modeTransportasi = [
                    "system" => "http://loinc.org",
                    "code" => "LA9315-8",
                    "display" => "Ground ambulance"
                ];
            } elseif ($data->alat_transportasi == 'Swasta') {
                $modeTransportasi = [
                    "system" => "http://snomed.info/sct",
                    "code" => "71783008",
                    "display" => "Car"
                ];
            } else {
                $modeTransportasi = [
                    "system" => "http://loinc.org",
                    "code" => "LA46-8",
                    "display" => "Other"
                ];
            }
        }

        if (!empty($idPasien) && (!empty($idPractition)) && (!empty($formatWaktu))) {
            $transportasi = [
                "resourceType" => "Observation",
                "status" => "final",
                "category" => [
                    [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                "code" => "survey",
                                "display" => "Survey"
                            ]
                        ]
                    ]
                ],
                "code" => [
                    "coding" => [
                        [
                            "system" => "http://loinc.org",
                            "code" => "74286-6",
                            "display" => "Transport mode to hospital"
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
                "effectiveDateTime" => "$formatWaktu",
                "issued" => "$formatWaktu",
                "performer" => [
                    [
                        "reference" => "Practitioner/$idPractition"
                    ]
                ],
                "valueCodeableConcept" => [
                    "coding" => [
                        $modeTransportasi
                    ]
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
                    'json' => $transportasi
                ]);
            } catch (ClientException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $test = json_decode($response->getBody());
                    if ($test->issue[0]->code == 'duplicate') {
                        $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                        $update->triase_transportasi = 'duplicate';
                        $update->save();
                    } else {
                        $message = $test->issue[0]->details->text;

                        LogErrorSatuSehat::create([
                            'subject' => 'Kirim status Transport IGD',
                            'keterangan' => "Pengiriman data transport pasien no rawat : $noRawat (" . $message . ")"
                        ]);
                    }
                }

                $message = "Gagal kirim observasi transportasi pasien IGD " . $noRawat;

                Session::flash('error', $message);

                return $message;
            }

            $dataResponse = json_decode($response->getBody());

            if (!empty($dataResponse->id)) {
                $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                $update->triase_transportasi = $dataResponse->id;
                $update->save();
            };
        }
    }

    public function sendTiaseKondisi($noRawat, $encounter)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('data_triase_igd', 'data_triase_igd.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->select(
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'data_triase_igd.no_rawat',
                'data_triase_igd.tgl_kunjungan',
                'data_triase_igd.cara_masuk',
                'data_triase_igd.alat_transportasi',
                'data_triase_igd.alasan_kedatangan',
                'data_triase_igd.kode_kasus',
                'data_triase_igd.tekanan_darah',
                'data_triase_igd.nadi',
                'data_triase_igd.pernapasan',
                'data_triase_igd.suhu',
                'data_triase_igd.saturasi_o2',
                'data_triase_igd.nyeri',
                'data_triase_igd.tb',
                'data_triase_igd.bb'
            )
            ->where('data_triase_igd.no_rawat', $noRawat)
            ->first();

        if (!empty($data)) {
            $idPasien = SatuSehatController::patientSehat($data->ktp_pasien);
            $idPractition = SatuSehatController::practitioner($data->ktp_dokter);
            $formatWaktu = Carbon::parse($data->tgl_kunjungan)->setTimezone('UTC')->toW3cString();

            if ($data->kode_kasus == '001') {
                $dataKondisi = [
                    "system" => "http://loinc.org",
                    "code" => "LA6112-2",
                    "display" => "1"
                ];
            } elseif ($data->kode_kasus == '002') {
                $dataKondisi = [
                    "system" => "http://loinc.org",
                    "code" => "LA6113-0",
                    "display" => "2"
                ];
            } elseif ($data->kode_kasus == '003') {
                $dataKondisi = [
                    "system" => "http://loinc.org",
                    "code" => "LA6114-8",
                    "display" => "3"
                ];
            } elseif ($data->kode_kasus == '004') {
                $dataKondisi = [
                    "system" => "http://loinc.org",
                    "code" => "LA6115-5",
                    "display" => "4"
                ];
            } elseif ($data->kode_kasus == '005') {
                $dataKondisi = [
                    "system" => "http://loinc.org",
                    "code" => "LA10137-0",
                    "display" => "5"
                ];
            } else {
                $dataKondisi = [
                    "system" => "http://loinc.org",
                    "code" => "LA6113-0",
                    "display" => "2"
                ];
            }

            if (!empty($idPasien) && (!empty($idPractition)) && (!empty($formatWaktu))) {
                $kondisi = [
                    "resourceType" => "Observation",
                    "status" => "final",
                    "category" => [
                        [
                            "coding" => [
                                [
                                    "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                    "code" => "survey",
                                    "display" => "Survey"
                                ]
                            ]
                        ]
                    ],
                    "code" => [
                        "coding" => [
                            [
                                "system" => "http://loinc.org",
                                "code" => "75910-0",
                                "display" => "Canadian triage and acuity scale [CTAS]"
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
                    "effectiveDateTime" => "$formatWaktu",
                    "issued" => "$formatWaktu",
                    "performer" => [
                        [
                            "reference" => "Practitioner/$idPractition"
                        ]
                    ],
                    "valueCodeableConcept" => [
                        "coding" => [
                            $dataKondisi
                        ]
                    ]
                ];

                //Send data
                // SatuSehatController::getTokenSehat();
                $access_token = SatuSehatController::getTokenSehat();
                $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                try {
                    $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}"
                        ],
                        'json' => $kondisi
                    ]);
                } catch (ClientException $e) {
                    // echo $e->getRequest();
                    // echo $e->getResponse();
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();

                        // dd($response);
                        $test = json_decode($response->getBody());
                        // dd($test, $kondisi);
                    }

                    $message = "Gagal kirim observasi kondisi pasien IGD " . $noRawat;

                    Session::flash('error', $message);

                    // return $message;
                }

                // dd($response);

                $dataResponse = json_decode($response->getBody());

                // dd($data);

                if (!empty($dataResponse->id)) {
                    $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                    $update->triase_kondisi = $dataResponse->id;
                    $update->save();
                };
            }
        }
    }

    public function sendAssesmenNyeri($noRawat, $encounter)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('data_triase_igd', 'data_triase_igd.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('penilaian_awal_keperawatan_igd', 'penilaian_awal_keperawatan_igd.no_rawat', '=', 'reg_periksa.no_rawat')
            ->select(
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'data_triase_igd.no_rawat',
                'data_triase_igd.tgl_kunjungan',
                'data_triase_igd.cara_masuk',
                'data_triase_igd.alat_transportasi',
                'data_triase_igd.alasan_kedatangan',
                'data_triase_igd.kode_kasus',
                'data_triase_igd.tekanan_darah',
                'data_triase_igd.nadi',
                'data_triase_igd.pernapasan',
                'data_triase_igd.suhu',
                'data_triase_igd.saturasi_o2',
                'penilaian_awal_keperawatan_igd.status_psiko',
                'penilaian_awal_keperawatan_igd.hasil',
                'penilaian_awal_keperawatan_igd.nyeri',
                'penilaian_awal_keperawatan_igd.lokasi',
                'penilaian_awal_keperawatan_igd.skala_nyeri',
                'penilaian_awal_keperawatan_igd.durasi'
            )
            ->where('data_triase_igd.no_rawat', $noRawat)
            ->first();

        if (!empty($data)) {
            $idPasien = SatuSehatController::patientSehat($data->ktp_pasien);
            $idPractition = SatuSehatController::practitioner($data->ktp_dokter);
            $formatWaktu = Carbon::parse($data->tgl_kunjungan)->setTimezone('UTC')->toW3cString();

            if (intval($data->nyeri) == 'Tidak Ada Nyeri') {
                $statusNyeri = false;
            } else {
                $statusNyeri = true;
            }

            if (!empty($idPasien) && (!empty($idPractition)) && (!empty($formatWaktu))) {
                $dataNyeri = [
                    "resourceType" => "Observation",
                    "status" => "final",
                    "category" => [
                        [
                            "coding" => [
                                [
                                    "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                    "code" => "survey",
                                    "display" => "Survey"
                                ]
                            ]
                        ]
                    ],
                    "code" => [
                        "coding" => [
                            [
                                "system" => "http://snomed.info/sct",
                                "code" => "22253000",
                                "display" => "Pain"
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
                        "display" => "$data->nm_pasien"
                    ],
                    "encounter" => [
                        "reference" => "Encounter/$encounter"
                    ],
                    "effectiveDateTime" => "$formatWaktu",
                    "issued" => "$formatWaktu",
                    "valueBoolean" => $statusNyeri
                ];

                //Send data
                $access_token = SatuSehatController::getTokenSehat();
                $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                try {
                    $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}"
                        ],
                        'json' => $dataNyeri
                    ]);
                } catch (ClientException $e) {
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();

                        // dd($response);
                        $test = json_decode($response->getBody());
                        if ($test->issue[0]->code == 'duplicate') {
                            try {
                                $responseObservation = $client->request('GET', 'fhir-r4/v1/Observation?encounter=' . $encounter, [
                                    'headers' => [
                                        'Authorization' => "Bearer {$access_token}"
                                    ]
                                ]);
                            } catch (ClientException $e) {
                                if ($e->hasResponse()) {
                                    $response = $e->getResponse();

                                    // dd($response);
                                    $test = json_decode($response->getBody());
                                    dd($test, 'fetch duplicate nyeri');
                                }
                            }
                            $dataResponseObservation = json_decode($responseObservation->getBody());

                            foreach ($dataResponseObservation->entry as $observation) {
                                foreach ($observation->resource->code->coding as $coding) {
                                    if ($coding->code == '22253000') {
                                        $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                                        $update->asesmen_nyeri = $observation->resource->id;
                                        $update->save();
                                    }
                                }
                            }
                        }
                    }
                }

                $dataResponse = json_decode($response->getBody());

                if (!empty($dataResponse->id)) {
                    $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                    $update->asesmen_nyeri = $dataResponse->id;
                    $update->save();
                };

                if ($statusNyeri == true) {
                    $skala = intval($data->skala_nyeri);
                    $dataSkalaNyeri = [
                        "resourceType" => "Observation",
                        "status" => "final",
                        "category" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                        "code" => "survey",
                                        "display" => "Survey"
                                    ]
                                ]
                            ]
                        ],
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://snomed.info/sct",
                                    "code" => "1172399009",
                                    "display" => "Numeric rating scale score"
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
                        "effectiveDateTime" => "$formatWaktu",
                        "issued" => "$formatWaktu",
                        "performer" => [
                            [
                                "reference" => "Practitioner/$idPractition"
                            ]
                        ],
                        "valueInteger" => $skala
                    ];
                    //Send data skala nyeri
                    $access_token = SatuSehatController::getTokenSehat();
                    $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataSkalaNyeri
                        ]);
                    } catch (ClientException $e) {
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            $test = json_decode($response->getBody());
                            dd($test, 'skala nyeri');
                        }

                        $message = "Gagal kirim observasi transportasi pasien IGD " . $noRawat;

                        Session::flash('error', $message);
                    }

                    $dataResponse = json_decode($response->getBody());

                    if (!empty($dataResponse->id)) {
                        $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                        $update->asesmen_skala_nyeri = $dataResponse->id;
                        $update->save();
                    };

                    //Kirim lokasi nyeri jika ada
                    if (($data->lokasi != '-') || (!empty($data->lokasi))) {
                        $dataLokasiNyeri = [
                            "resourceType" => "Observation",
                            "status" => "final",
                            "category" => [
                                [
                                    "coding" => [
                                        [
                                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                            "code" => "survey",
                                            "display" => "Survey"
                                        ]
                                    ]
                                ]
                            ],
                            "code" => [
                                "coding" => [
                                    [
                                        "system" => "http://loinc.org",
                                        "code" => "38204-4",
                                        "display" => "Pain primary location"
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
                            "effectiveDateTime" => "$formatWaktu",
                            "issued" => "$formatWaktu",
                            "performer" => [
                                [
                                    "reference" => "Practitioner/$idPractition"
                                ]
                            ],
                            "valueString" => "$data->lokasi"
                        ];

                        $access_token = SatuSehatController::getTokenSehat();
                        $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                        try {
                            $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                                'headers' => [
                                    'Authorization' => "Bearer {$access_token}"
                                ],
                                'json' => $dataLokasiNyeri
                            ]);
                        } catch (ClientException $e) {
                            if ($e->hasResponse()) {
                                $response = $e->getResponse();
                                $test = json_decode($response->getBody());
                                dd($test, 'status nyeri', $dataNyeri);
                            }

                            $message = "Gagal kirim asesment lokasi nyeri pasien IGD " . $noRawat;

                            Session::flash('error', $message);
                        }

                        $dataResponse = json_decode($response->getBody());

                        if (!empty($dataResponse->id)) {
                            $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                            $update->asesmen_lokasi_nyeri = $dataResponse->id;
                            $update->save();
                        };
                    }
                }
            }
        }
    }

    public function sendVitalSign($noRawat, $encounter)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('data_triase_igd', 'data_triase_igd.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->select(
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'data_triase_igd.no_rawat',
                'data_triase_igd.tgl_kunjungan',
                'data_triase_igd.cara_masuk',
                'data_triase_igd.alat_transportasi',
                'data_triase_igd.alasan_kedatangan',
                'data_triase_igd.kode_kasus',
                'data_triase_igd.tekanan_darah',
                'data_triase_igd.nadi',
                'data_triase_igd.pernapasan',
                'data_triase_igd.suhu',
                'data_triase_igd.saturasi_o2',
                'data_triase_igd.nyeri',
                'data_triase_igd.tb',
                'data_triase_igd.bb'
            )
            ->where('data_triase_igd.no_rawat', $noRawat)
            ->first();

        if (!empty($data)) {
            $idPasien = SatuSehatController::patientSehat($data->ktp_pasien);
            $idPractition = SatuSehatController::practitioner($data->ktp_dokter);
            $formatWaktu = Carbon::parse($data->tgl_kunjungan)->setTimezone('UTC')->toW3cString();
            $tekanan = explode('/', $data->tekanan_darah);
            if (!empty($idPasien) && (!empty($idPractition)) && (!empty($formatWaktu))) {
                if (!empty($data->nadi)) {
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
                            "display" => "$data->nm_pasien"
                        ],
                        "encounter" => [
                            "reference" => "Encounter/$encounter"
                        ],
                        "effectiveDateTime" => "$formatWaktu",
                        "issued" => "$formatWaktu",
                        "valueQuantity" => [
                            "value" => intval($data->nadi),
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

                            $test = json_decode($response->getBody());
                            if ($test && $test->issue[0]->code == 'duplicate') {
                                try {
                                    $checkObservation = $client->request('GET', 'fhir-r4/v1/Observation?encounter=' . $encounter, [
                                        'headers' => [
                                            'Authorization' => "Bearer {$access_token}"
                                        ]
                                    ]);
                                } catch (ClientException $e) {
                                    $dataObsError = $e->getResponse();
                                    dd($dataObsError, 'data obs error');
                                }
                                if ($checkObservation) {
                                    $dataObs = json_decode($checkObservation->getBody());
                                    if (!empty($dataObs->entry)) {
                                        foreach ($dataObs->entry as $entry) {
                                            if ($entry->resource->code->coding[0]->code == '8867-4') {
                                                $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                                                $update->asesmen_nadi = $entry->resource->id;
                                                $update->save();
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $message = "Gagal kirim vital sign Nadi pasien IGD " . $noRawat;
                            LogErrorSatuSehat::create([
                                'subject' => 'Kirim vital sign Nadi IGD',
                                'keterangan' => "Pengiriman data vital sign Nadi pasien no rawat : $noRawat (" . $message . ")"

                            ]);

                            return;
                        }
                    }

                    $dataResponse = json_decode($response->getBody());

                    if (!empty($dataResponse->id)) {
                        $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                        $update->asesmen_nadi = $dataResponse->id;
                        $update->save();
                    };
                }

                if (!empty($data->pernapasan)) {
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
                            "display" => "$data->nm_pasien"
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
                            "value" => intval($data->pernapasan),
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
                            $test = json_decode($response->getBody());
                            dd($test, 'pernafasan');
                        }

                        $message = "Gagal kirim vital sign Pernafasan pasien IGD " . $noRawat;
                        LogErrorSatuSehat::create([
                            'subject' => 'Kirim vital sign Pernafasan IGD',
                            'keterangan' => "Pengiriman data vital sign Pernafasan pasien no rawat : $noRawat (" . $message . ")"

                        ]);

                        return;
                    }

                    $dataResponse = json_decode($response->getBody());

                    if (!empty($dataResponse->id)) {
                        $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                        $update->asesmen_pernapasan = $dataResponse->id;
                        $update->save();
                    };
                }

                if (!empty($tekanan[0])) {
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
                            "display" => "$data->nm_pasien"
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
                            "value" => intval($tekanan[0]),
                            "unit" => "mm[Hg]",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "mm[Hg]"
                        ]
                    ];

                    //kirim data sistole
                    // SatuSehatController::getTokenSehat();
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
                            $test = json_decode($response->getBody());
                            // dd($test, 'sistol');
                            if ($test->issue[0]->code == 'duplicate') {
                                try {
                                    $responseObservation = $client->request('GET', 'fhir-r4/v1/Observation?encounter=' . $encounter, [
                                        'headers' => [
                                            'Authorization' => "Bearer {$access_token}"
                                        ]
                                    ]);
                                } catch (ClientException $e) {
                                    if ($e->hasResponse()) {
                                        $response = $e->getResponse();

                                        // dd($response);
                                        $test = json_decode($response->getBody());
                                        dd($test, 'fetch duplicate sistol');
                                    }
                                }
                                $dataResponseObservation = json_decode($responseObservation->getBody());

                                foreach ($dataResponseObservation->entry as $observation) {
                                    foreach ($observation->resource->code->coding as $coding) {
                                        if ($coding->code == '8480-6') {
                                            $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                                            $update->asesmen_sistol = $observation->resource->id;
                                            $update->save();
                                        }
                                    }
                                }
                            }

                            goto KirimDataSistol;
                        }

                        $message = "Gagal kirim vital sign Sistole pasien IGD " . $noRawat;
                        LogErrorSatuSehat::create([
                            'subject' => 'Kirim vital sign Sistole IGD',
                            'keterangan' => "Pengiriman data vital sign Sistole pasien no rawat : $noRawat (" . $message . ")"

                        ]);

                        return;
                    }

                    $dataResponse = json_decode($response->getBody());

                    if (!empty($dataResponse->id)) {
                        $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                        $update->asesmen_sistol = $dataResponse->id;
                        $update->save();
                    };
                }

                KirimDataSistol:

                if (!empty($tekanan[1])) {
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
                            "display" => "$data->nm_pasien"
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
                            "value" => intval($tekanan[1]),
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

                            $test = json_decode($response->getBody());
                            dd($test, 'diastol');
                        }

                        $message = "Gagal kirim vital sign Diastol pasien IGD " . $noRawat;
                        LogErrorSatuSehat::create([
                            'subject' => 'Kirim vital sign Diastol IGD',
                            'keterangan' => "Pengiriman data vital sign Diastol pasien no rawat : $noRawat (" . $message . ")"

                        ]);

                        return;
                    }

                    $dataResponse = json_decode($response->getBody());

                    if (!empty($dataResponse->id)) {
                        $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                        $update->asesmen_diastol = $dataResponse->id;
                        $update->save();
                    };
                }

                if (!empty($data->suhu)) {
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
                            "display" => "$data->nm_pasien"
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
                            "value" => floatval($data->suhu),
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
                            $test = json_decode($response->getBody());
                        }

                        $message = "Gagal kirim vital sign Suhu pasien IGD " . $noRawat;
                        LogErrorSatuSehat::create([
                            'subject' => 'Kirim vital sign Suhu IGD',
                            'keterangan' => "Pengiriman data vital sign Suhu pasien no rawat : $noRawat (" . $message . ")"

                        ]);

                        return;
                    }

                    $dataResponse = json_decode($response->getBody());

                    if (!empty($dataResponse->id)) {
                        $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                        $update->asesmen_suhu = $dataResponse->id;
                        $update->save();
                    };
                }
            }
        }
    }

    public function sendStatusPsico($noRawat, $encounter)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('data_triase_igd', 'data_triase_igd.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('penilaian_awal_keperawatan_igd', 'penilaian_awal_keperawatan_igd.no_rawat', '=', 'reg_periksa.no_rawat')
            ->select(
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'data_triase_igd.no_rawat',
                'data_triase_igd.tgl_kunjungan',
                'data_triase_igd.cara_masuk',
                'data_triase_igd.alat_transportasi',
                'data_triase_igd.alasan_kedatangan',
                'data_triase_igd.kode_kasus',
                'data_triase_igd.tekanan_darah',
                'data_triase_igd.nadi',
                'data_triase_igd.pernapasan',
                'data_triase_igd.suhu',
                'data_triase_igd.saturasi_o2',
                'penilaian_awal_keperawatan_igd.status_psiko',
                'penilaian_awal_keperawatan_igd.hasil',
                'penilaian_awal_keperawatan_igd.nyeri',
                'penilaian_awal_keperawatan_igd.lokasi',
                'penilaian_awal_keperawatan_igd.skala_nyeri',
                'penilaian_awal_keperawatan_igd.durasi'
            )
            ->where('data_triase_igd.no_rawat', $noRawat)
            ->first();

        if (!empty($data)) {
            $idPasien = SatuSehatController::patientSehat($data->ktp_pasien);
            $idPractition = SatuSehatController::practitioner($data->ktp_dokter);
            $formatWaktu = Carbon::parse($data->tgl_kunjungan)->setTimezone('UTC')->toW3cString();
            if (!empty($idPasien) && (!empty($idPractition)) && (!empty($formatWaktu))) {
                if ($data->status_psiko == 'Tenang') {
                    $dataPsiko = [
                        "system" => "http://snomed.info/sct",
                        "code" => "17326005",
                        "display" => "Well in self"
                    ];
                } else if ($data->status_psiko == 'Takut') {
                    $dataPsiko = [
                        "system" => "http://snomed.info/sct",
                        "code" => "1402001",
                        "display" => "Afraid"
                    ];
                } else if ($data->status_psiko == 'Cemas') {
                    $dataPsiko = [
                        "system" => "http://snomed.info/sct",
                        "code" => "48694002",
                        "display" => "Feeling anxious"
                    ];
                } else if (($data->status_psiko == 'Depresi') || ($data->status_psiko == 'Lain-lain')) {
                    $dataPsiko = [
                        "system" => "http://snomed.info/sct",
                        "code" => "74964007",
                        "display" => "Other"
                    ];
                }

                $dataPsikologis = [
                    "resourceType" => "Observation",
                    "status" => "final",
                    "category" => [
                        [
                            "coding" => [
                                [
                                    "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                    "code" => "survey",
                                    "display" => "Survey"
                                ]
                            ]
                        ]
                    ],
                    "code" => [
                        "coding" => [
                            [
                                "system" => "http://loinc.org",
                                "code" => "8693-4",
                                "display" => "Mental Status"
                            ]
                        ]
                    ],
                    "subject" => [
                        "reference" => "Patient/$idPasien",
                        "display" => "$data->nm_pasien"
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
                    "valueCodeableConcept" => [
                        "coding" => [
                            $dataPsiko
                        ]
                    ]
                ];

                // SatuSehatController::getTokenSehat();
                $access_token = SatuSehatController::getTokenSehat();
                // dd($access_token);
                $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                try {
                    $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}"
                        ],
                        'json' => $dataPsikologis
                    ]);
                } catch (ClientException $e) {
                    // echo $e->getRequest();
                    // echo $e->getResponse();
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();

                        // dd($response);
                        $test = json_decode($response->getBody());
                        dd($test, 'status nyeri', $dataPsikologis);
                    }

                    $message = "Gagal kirim kondisi Psikologis pasien IGD " . $noRawat;

                    Session::flash('error', $message);
                }

                $dataResponse = json_decode($response->getBody());

                if (!empty($dataResponse->id)) {
                    $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                    $update->asesmen_psikologis = $dataResponse->id;
                    $update->save();
                };
            }
        }
    }

    public function sendUpdateKepulangan($noRawat, $encounter)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('ringkasan_pasien_igd', 'ringkasan_pasien_igd.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('pegawai', 'pegawai.nik', '=', 'ringkasan_pasien_igd.kd_dokter')
            ->select(
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_poli',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'ringkasan_pasien_igd.no_rawat',
                'ringkasan_pasien_igd.kondisi_pulang',
                'ringkasan_pasien_igd.tindak_lanjut',
                'ringkasan_pasien_igd.tgl_selesai'
            )
            ->where('ringkasan_pasien_igd.no_rawat', $noRawat)
            ->first();

        // dd($data);
        $idRS = env('IDRS');

        if (!empty($data->ktp_pasien)) {
            $idPasien = SatuSehatController::patientSehat($data->ktp_pasien);
            $idPoli = SatuSehatController::getIdPoli($data->kd_poli);

            $idPractition = SatuSehatController::practitioner($data->ktp_dokter);
            $waktuMulai = "$data->tgl_registrasi $data->jam_reg";
            $formatWaktuMulai = Carbon::parse($waktuMulai)->setTimezone('UTC')->toW3cString();
            $formatWaktuSelesai = Carbon::parse($data->tgl_selesai)->setTimezone('UTC')->toW3cString();
            if ($formatWaktuSelesai > $formatWaktuMulai) {
                $formatWaktuSelesai = Carbon::parse($data->tgl_selesai)->addHour()->setTimezone('UTC')->toW3cString();
            }
            if (!empty($idPasien) && (!empty($idPractition)) && (!empty($formatWaktuMulai))) {
                if ($data->kondisi_pulang == 'Stabil') {
                    $dataKepulangan = [
                        "system" => "http://snomed.info/sct",
                        "code" => "359746009",
                        "display" => "Patient's condition stable"
                    ];
                } else if ($data->kondisi_pulang == 'Perbaikan') {
                    $dataKepulangan = [
                        "system" => "http://snomed.info/sct",
                        "code" => "268910001",
                        "display" => "Patient's condition improved"
                    ];
                }

                if (!empty($dataKepulangan)) {
                    $dataKondisiMeninggalkan = [
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
                                        "code" => "problem-list-item",
                                        "display" => "Problem List Item"
                                    ]
                                ]
                            ]
                        ],
                        "code" => [
                            "coding" => [
                                $dataKepulangan
                            ]
                        ],
                        "subject" => [
                            "reference" => "Patient/$idPasien",
                            "display" => "$data->nm_pasien"
                        ],
                        "encounter" => [
                            "reference" => "Encounter/$encounter"
                        ]
                    ];

                    // SatuSehatController::getTokenSehat();
                    $access_token = SatuSehatController::getTokenSehat();
                    // dd($access_token);
                    $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Condition', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataKondisiMeninggalkan
                        ]);
                    } catch (ClientException $e) {
                        // echo $e->getRequest();
                        // echo $e->getResponse();
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            // dd($response);
                            $test = json_decode($response->getBody());
                            dd($test, 'status Meninggalkan', $dataKondisiMeninggalkan);
                        }

                        $message = "Gagal kirim kondisi Meninggalkan pasien IGD " . $noRawat;

                        Session::flash('error', $message);
                    }

                    $dataResponse = json_decode($response->getBody());

                    if (!empty($dataResponse->id)) {
                        $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                        $update->kondisi_stabil = $dataResponse->id;
                        $update->save();
                    };

                    //Update data Encounter
                    $updateEncounter = [
                        "resourceType" => "Encounter",
                        "id" => "$encounter",
                        "identifier" => [
                            [
                                "system" => "http://sys-ids.kemkes.go.id/encounter/$idRS",
                                "value" => "$noRawat"
                            ]
                        ],
                        "status" => "finished",
                        "class" => [
                            "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                            "code" => "EMER",
                            "display" => "emergency"
                        ],
                        "subject" => [
                            "reference" => "Patient/$idPasien",
                            "display" => "$data->nm_pasien"
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
                                    "reference" => "Practitioner/$idPractition",
                                    "display" => "$data->nama_dokter"
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
                                    "reference" => "Location/$idPoli",
                                    "display" => "Bed Instalasi Gawat Darurat"
                                ],
                                "period" => [
                                    "start" => "$formatWaktuMulai",
                                    "end" => "$formatWaktuSelesai"
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
                        "diagnosis" => [
                            [
                                "condition" => [
                                    "reference" => "Condition/$dataResponse->id"
                                    // ,
                                    // "display" => "Abnormal uterine and vaginal bleeding, unspecified"
                                ],
                                "use" => [
                                    "coding" => [
                                        [
                                            "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
                                            "code" => "AD",
                                            "display" => "Admission diagnosis "
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "statusHistory" => [
                            [
                                "status" => "in-progress",
                                "period" => [
                                    "start" => "$formatWaktuMulai",
                                    "end" => "$formatWaktuSelesai"
                                ]
                            ],
                            [
                                "status" => "finished",
                                "period" => [
                                    "start" => "$formatWaktuMulai",
                                    "end" => "$formatWaktuSelesai"
                                ]
                            ]
                        ],
                        // "hospitalization" => [
                        //     "dischargeDisposition" => [
                        //         "coding" => [
                        //             [
                        //                 "system" => "http://terminology.hl7.org/CodeSystem/discharge-disposition",
                        //                 "code" => "oth",
                        //                 "display" => "Other"
                        //             ]
                        //         ],
                        //         "text" => "Pasien dipindahkan dari IGD ke rawat inap."
                        //     ]
                        // ],
                        "serviceProvider" => [
                            "reference" => "Organization/$idRS"
                        ]
                    ];

                    // SatuSehatController::getTokenSehat();
                    $access_token = SatuSehatController::getTokenSehat();
                    $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                    try {
                        $response = $client->request('PUT', "fhir-r4/v1/Encounter/$encounter", [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $updateEncounter
                        ]);
                    } catch (ClientException $e) {
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            // dd($response);
                            $test = json_decode($response->getBody());
                            // dd($test, 'status update encounter Meninggalkan IGD ', $updateEncounter);
                        }

                        $message = "Gagal update encounter pasien IGD " . $noRawat;

                        LogErrorSatuSehat::create([
                            'subject' => 'Update Encounter Meninggalkan IGD',
                            'keterangan' => "Update encounter pasien no rawat : $noRawat (" . $message . ")"
                        ]);
                    }
                }
            }
        }
    }

    public static function sendServiceRequest($noRawat, $encounter)
    {
        $dataPasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('penilaian_medis_igd', 'penilaian_medis_igd.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->select(
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_poli',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'penilaian_medis_igd.no_rawat',
                // 'ringkasan_pasien_igd.kondisi_pulang',
                // 'ringkasan_pasien_igd.tindak_lanjut',
                'penilaian_medis_igd.tanggal'
            )
            ->where('penilaian_medis_igd.no_rawat', $noRawat)
            ->first();

        // dd($dataPasien, $noRawat, $encounter);
        $idRS = env('IDRS');
        if (!empty($dataPasien)) {
            $idPoli = SatuSehatController::getIdPoli($dataPasien->kd_poli);
            if (!empty($dataPasien->ktp_pasien) || ($dataPasien == '-')) {
                $idPasien = SatuSehatController::patientSehat($dataPasien->ktp_pasien);
            }
            $idPractition = SatuSehatController::practitioner($dataPasien->ktp_dokter);
            //Pinjam mas Ghoni
            $idPractition2 = SatuSehatController::practitioner('6208030807950001');
            $waktuMulai = "$dataPasien->tgl_registrasi $dataPasien->jam_reg";
            $formatWaktuMulai = Carbon::parse($waktuMulai)->setTimezone('UTC')->toW3cString();
            $formatWaktuSelesai = Carbon::parse($dataPasien->tanggal)->setTimezone('UTC')->toW3cString();
        }

        if ((!empty($idPoli)) && (!empty($idPasien)) && (!empty($idPractition2)) && (!empty($dataPasien))) {

            $dataServiceRequest = [
                "resourceType" => "ServiceRequest",
                "identifier" => [
                    [
                        "system" => "http://sys-ids.kemkes.go.id/servicerequest/$idRS",
                        "value" => "$noRawat"
                    ]
                ],
                "status" => "active",
                "intent" => "order",
                "priority" => "urgent",
                "category" => [
                    [
                        "coding" => [
                            [
                                "system" => "http://snomed.info/sct",
                                "code" => "3457005",
                                "display" => "Patient referral"
                            ]
                        ]
                    ]
                ],
                "code" => [
                    "coding" => [
                        [
                            "system" => "http://snomed.info/sct",
                            "code" => "737481003",
                            "display" => "Inpatient care management"
                        ]
                    ]
                    // ,
                    // "text"=> "Rawat inap pasca prosedur caesar emergensi"
                ],
                "subject" => [
                    "reference" => "Patient/$idPasien",
                    "display" => "$dataPasien->nm_pasien"
                ],
                "encounter" => [
                    "reference" => "Encounter/$encounter"
                ],
                "occurrenceDateTime" => "$formatWaktuSelesai",
                "requester" => [
                    "reference" => "Practitioner/$idPractition",
                    "display" => "$dataPasien->nama_dokter"
                ],
                "performer" => [
                    [
                        "reference" => "Practitioner/$idPractition2",
                        "display" => "Abdul Ghoni"
                    ]
                ],
                // "reasonCode" => [
                //     [
                //         "coding" => [
                //             [
                //                 "system" => "http://hl7.org/fhir/sid/icd-10",
                //                 "code" => "O71.0",
                //                 "display" => "Rupture of uterus before onset of labour"
                //             ]
                //         ],
                //         "text" => "Pasien mengalami ruptur uteri sebelum proses persalinan dimulai dan telah dilakukan prosedur caesar emergensi"
                //     ]
                // ],
                "patientInstruction" => "Pasien dirujuk ke rawat inap"
            ];

            // SatuSehatController::getTokenSehat();
            $access_token = SatuSehatController::getTokenSehat();
            $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
            try {
                $response = $client->request('POST', "fhir-r4/v1/ServiceRequest", [
                    'headers' => [
                        'Authorization' => "Bearer {$access_token}"
                    ],
                    'json' => $dataServiceRequest
                ]);
            } catch (ClientException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $test = json_decode($response->getBody());
                    dd($test, 'kirim data Service Request', $dataServiceRequest);
                }

                $message = "Gagal kirim service request pasien IGD " . $noRawat;

                Session::flash('error', $message);
            }

            $dataResponse = json_decode($response->getBody());

            if (!empty($dataResponse->id)) {
                $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                $update->service_request = $dataResponse->id;
                $update->save();

                return true;
            };
        }
    }

    public function checkDataIgd($noRawat)
    {
        $cek =  DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.kd_poli',
                'poliklinik.nm_poli'
            )
            ->where('poliklinik.nm_poli', 'like', '%IGD%')
            ->where('reg_periksa.no_rawat', $noRawat)
            ->count();

        // if ($noRawat == '2024/05/02/000209') {
        //     dd($cek);
        // }

        if ($cek > 0) {
            // dd($noRawat);
            $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
            $update->cara_keluar = null;
            $update->save();
        } else if ($cek == 0) {
            $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
            $update->cara_keluar = 'bukan Pasien IGD';
            $update->save();
        }
    }

    public static function sendDiagnosisAwal($noRawat, $encounter)
    {
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
                        WHERE dp.status = 'Ralan' AND dp.no_rawat = '$noRawat'

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
                        WHERE dpi.status = 'Ralan' AND dpi.no_rawat = '$noRawat'
                    ) as diagnosa_all"))
            ->orderBy('urutan_sumber')
            ->orderBy('prioritas')
            ->get();

        if (($cekDiagnosa->count() > 0)) {
            $getPasien = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->leftJoin('penilaian_medis_igd', 'penilaian_medis_igd.no_rawat', '=', 'reg_periksa.no_rawat')
                ->select(
                    'pasien.nm_pasien',
                    'pasien.no_ktp as ktp_pasien',
                    'pasien.tgl_lahir',
                    'pasien.jk',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.jam_reg',
                    'penilaian_medis_igd.tanggal as tanggal_penilaian_medis'
                )
                ->where('reg_periksa.no_rawat', $noRawat)
                ->first();

            $excleudeDiagnosa = explode(',', env('EXCLUDE_DIAGNOSA'));

            $diagnosa = $cekDiagnosa->where('im', false)
                ->whereNotIn('kd_penyakit', $excleudeDiagnosa)
                ->first();

            if (!empty($diagnosa)) {
                $idPasien = SatuSehatController::patientSehat($getPasien->ktp_pasien);
                $formatWaktu = Carbon::parse("$getPasien->tanggal_penilaian_medis")->setTimezone('UTC')->toW3cString();

                if (!empty($idPasien) && (!empty($formatWaktu))) {
                    $dataDiagnosisAwal = [
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
                                    "code" => "$diagnosa->kd_penyakit",
                                    "display" => "$diagnosa->nm_penyakit"
                                ]
                            ]
                        ],
                        "subject" => [
                            "reference" => "Patient/$idPasien",
                            "display" => "$getPasien->nm_pasien"
                        ],
                        "encounter" => [
                            "reference" => "Encounter/$encounter"
                        ],
                        "onsetDateTime" => "$formatWaktu",
                        "recordedDate" => "$formatWaktu"
                    ];

                    $access_token = SatuSehatController::getTokenSehat();
                    $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                    try {
                        $response = $client->request('POST', "fhir-r4/v1/Condition", [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataDiagnosisAwal
                        ]);
                    } catch (ClientException $e) {
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();
                            $test = json_decode($response->getBody());
                            dd($test, 'kirim data Diagnosis Awal', $dataDiagnosisAwal);
                        }

                        $message = "Gagal kirim diagnosis awal pasien IGD " . $noRawat;

                        LogErrorSatuSehat::create([
                            'subjek' => 'Diagnosis Awal IGD',
                            'keterangan' => $message . ' - ' . $noRawat
                        ]);
                    }

                    $dataResponse = json_decode($response->getBody());

                    if (!empty($dataResponse->id)) {
                        $update = ResponseIgdSatuSehat::where('encounter_id', $encounter)->first();
                        $update->diagnosis_awal = $dataResponse->id;
                        $update->save();

                        return true;
                    };
                }
            }
        }
    }

    public function sendTindakanIgd($noRawat, $encounter)
    {
        $tindakan = DB::connection('mysqlkhanza')->table('prosedur_pasien_inacbg')
            ->join('icd9', 'prosedur_pasien_inacbg.kode', '=', 'icd9.kode')
            ->where('prosedur_pasien_inacbg.no_rawat', $noRawat)
            ->where('prosedur_pasien_inacbg.status', 'Ralan')
            ->get();

        if ($tindakan->count() > 0) {
            $getPasien = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->leftJoin('penilaian_medis_igd', 'penilaian_medis_igd.no_rawat', '=', 'reg_periksa.no_rawat')
                ->leftJoin('pegawai', 'pegawai.nik', '=', 'penilaian_medis_igd.kd_dokter')
                ->select(
                    'pasien.nm_pasien',
                    'pasien.no_ktp as ktp_pasien',
                    'pasien.tgl_lahir',
                    'pasien.jk',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.jam_reg',
                    'penilaian_medis_igd.tanggal as tanggal_penilaian_medis',
                    'pegawai.no_ktp as ktp_dokter',
                    'pegawai.nama as nama_dokter'
                )
                ->where('reg_periksa.no_rawat', $noRawat)
                ->first();

            if ($getPasien) {
                $idPasien = SatuSehatController::patientSehat($getPasien->ktp_pasien);
                $formatWaktu = Carbon::parse("$getPasien->tanggal_penilaian_medis")->setTimezone('UTC')->toW3cString();
                $idPractition = SatuSehatController::practitioner($getPasien->ktp_dokter);
                $dataEncounter = ResponseIgdSatuSehat::where('encounter_id', $encounter)->first();

                if (!empty($idPasien) && (!empty($idPractition))) {
                    foreach ($tindakan as $data) {
                        $json = [
                            "resourceType" => "Procedure",
                            "status" => "completed",
                            "category" => [
                                "coding" => [
                                    [
                                        "system" => "http://snomed.info/sct",
                                        "code" => "373110003",
                                        "display" => "Emergency procedure"
                                    ]
                                ],
                                "text" => "Prosedur emergensi"
                            ],
                            "code" => [
                                "coding" => [
                                    [
                                        "system" => "http://hl7.org/fhir/sid/icd-9-cm",
                                        "code" => "$data->kode",
                                        "display" => "$data->deskripsi_panjang"
                                    ]
                                ]
                            ],
                            "subject" => [
                                "reference" => "Patient/$idPasien",
                                "display" => "$getPasien->nm_pasien"
                            ],
                            "encounter" => [
                                "reference" => "Encounter/$encounter"
                            ],
                            "performedDateTime" => "$formatWaktu",
                            "reasonReference" => [
                                [
                                    "reference" => "Condition/" . $dataEncounter->diagnosis_awal
                                ]
                            ],
                            "performer" => [
                                [
                                    "actor" => [
                                        "reference" => "Practitioner/$idPractition"
                                    ]
                                ]
                            ]
                        ];

                        $access_token = SatuSehatController::getTokenSehat();
                        $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                        try {
                            $response = $client->request('POST', "fhir-r4/v1/Procedure", [
                                'headers' => [
                                    'Authorization' => "Bearer {$access_token}"
                                ],
                                'json' => $json
                            ]);
                        } catch (ClientException $e) {
                            if ($e->hasResponse()) {
                                $response = $e->getResponse();
                                $test = json_decode($response->getBody());
                                if ($test->issue[0]->code == 'duplicate') {
                                    try {
                                        $responseProsedure = $client->request('GET', 'fhir-r4/v1/Procedure?encounter=' . $encounter, [
                                            'headers' => [
                                                'Authorization' => "Bearer {$access_token}"
                                            ]
                                        ]);
                                    } catch (ClientException $e) {
                                        if ($e->hasResponse()) {
                                            $response = $e->getResponse();
                                            $test = json_decode($response->getBody());
                                            dd($test, 'error ambil procedure duplicate IGD');
                                        }
                                    }
                                    $dataResponseProcedure = json_decode($responseProsedure->getBody());

                                    // dd($dataResponseProcedure, 'data fetch duplicate procedure', $data);
                                    foreach ($dataResponseProcedure->entry as $prosedure) {
                                        foreach ($prosedure->resource->code->coding as $coding) {
                                            if ($coding->code == $data->kode) {
                                                $update = new TindakanIgdSatuSehat;
                                                $update->response_igd_satu_sehat_id = $dataEncounter->id;
                                                $update->procedure_id = $prosedure->resource->id;
                                                $update->save();
                                            }
                                        }
                                    }

                                    goto KirimNextProcedure;
                                } else {
                                    $message = $test->issue[0]->details->text;

                                    LogErrorSatuSehat::create([
                                        'subject' => 'Kirim status Tindakan IGD',
                                        'keterangan' => "Pengiriman data tindakan pasien no rawat : $noRawat (" . $message . ")"
                                    ]);
                                }
                            }

                            $message = "Gagal kirim tindakan pasien IGD " . $noRawat;

                            LogErrorSatuSehat::create([
                                'subjek' => 'Tindakan IGD',
                                'keterangan' => $message . ' - ' . $noRawat
                            ]);
                        }

                        $dataResponse = json_decode($response->getBody());

                        if (!empty($dataResponse->id)) {
                            TindakanIgdSatuSehat::create([
                                'response_igd_satu_sehat_id' => $dataEncounter->id,
                                'procedure_id' => $dataResponse->id
                            ]);
                        };

                        KirimNextProcedure:
                    }
                } else {
                    $message = "Gagal kirim tindakan pasien IGD " . $noRawat . " karena data tidak lengkap (idPasien, formatWaktu, idPractition, diagnosis_awal)";

                    LogErrorSatuSehat::create([
                        'subjek' => 'Tindakan IGD',
                        'keterangan' => $message . ' - ' . $noRawat
                    ]);
                }
            }
        }
    }
}
