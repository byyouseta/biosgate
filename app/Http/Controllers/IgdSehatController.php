<?php

namespace App\Http\Controllers;

use App\LogErrorSatuSehat;
use App\ResponseIgdSatuSehat;
use App\ResponseSatuSehat;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        session()->forget('cucu');

        if (empty($request->get('tanggal'))) {
            $tanggal = Carbon::now();
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
        }

        $dataLog = ResponseIgdSatuSehat::whereDate('tgl_registrasi', $tanggal)
            ->get();

        $errorLog = LogErrorSatuSehat::whereDate('created_at', $tanggal)->get();

        // dd($errorLog);

        return view('satu_sehat.summaryIgd', compact('dataLog', 'errorLog'));
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
                    // 'reg_periksa.kd_poli',
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
                // ->selectRaw("(CASE WHEN (poliklinik.kd_poli = 'u0041') THEN 'IGD' ELSE poliklinik.nm_poli END) as alias_nm_poli")
                ->whereBetween('reg_periksa.tgl_registrasi', [$kemarin, $pasien_tanggal])
                // ->orWhereDate('reg_periksa.tgl_registrasi', $kemarin)
                ->where('poliklinik.nm_poli', 'like', '%IGD%')
                // ->where('reg_periksa.stts', 'Sudah')
                // ->where('reg_periksa.no_rawat', '=', '2023/03/09/000107')

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
                    // 'reg_periksa.kd_poli',
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
                ->where('poliklinik.nm_poli', 'like', '%IGD%')
                // ->where('reg_periksa.stts', 'Sudah')
                // ->where('reg_periksa.no_rawat', '=', '2023/03/09/000107')
                ->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
                ->orderBy('reg_periksa.tgl_registrasi', 'ASC')
                ->get();
        }

        // dd($data);
        // $pasien_tanggal = Carbon::now()->format('Y-m-d');
        // $pasien_tanggal = "2023-10-10";
        $idRs = env('IDRS');

        // dd($data);
        foreach ($data as $dataPengunjung) {
            $idPasien = SatuSehatController::patientSehat($dataPengunjung->ktp_pasien);
            $idPoli = SatuSehatController::getIdPoli($dataPengunjung->kd_poli);
            $idPractition = SatuSehatController::practitioner($dataPengunjung->ktp_dokter);
            // dd($idPractition);
            $waktuMulai = Carbon::parse($dataPengunjung->tgl_registrasi . ' ' . $dataPengunjung->jam_reg)->locale('id');
            $waktuMulai->setTimezone('UTC');
            $formatMulai = Carbon::parse($waktuMulai)->format('Y-m-d') . 'T' . Carbon::parse($waktuMulai)->format('H:i:s') . '+00:00';
            // dd($dataPengunjung, $waktuMulai, $formatMulai);

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
                } catch (ClientException $e) {
                    // echo $e->getRequest();
                    // echo $e->getResponse();
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();

                        // dd($response);
                        $test = json_decode($response->getBody());
                        dd($test, $dataEncounter);
                    }

                    $message = "Gagal kirim encounter IGD pasien " . $dataPengunjung->no_rawat;

                    Session::flash('error', $message);

                    goto KirimPasienLain;
                }

                // dd($response);

                $data = json_decode($response->getBody());

                // dd($data);

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
        // dd($dataLog);

        return view('satu_sehat.client_igd', compact('dataLog'));
    }

    public function closeEncounter()
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'IGD Satu Sehat');
        session()->put('cucu', 'Client Update Encounter IGD');
        set_time_limit(0);

        $dataEncounter = ResponseIgdSatuSehat::where('cara_keluar', null)
            ->get();

        // dd($dataEncounter);

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

            // if (empty($cekPulang->status_lanjut)) {
            //     dd($cekPulang, $dataTerkirim->noRawat);
            // }
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
        // $data = IgdSehatController::sendTransportasiKedatangan();
        $dataLog = ResponseIgdSatuSehat::whereDate('updated_at', Carbon::now())
            ->orderBy('updated_at', 'DESC')
            ->limit(30)
            ->get();
        // $dataLog = ResponseIgdSatuSehat::whereDate('tgl_registrasi', $pasien_tanggal)->get();
        // dd($dataLog);

        return view('satu_sehat.client_igd', compact('dataLog'));
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

        // $displayWaktu = $formatWaktu->toW3cString();
        // dd($data, $formatWaktu->toW3cString());


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

            // dd($transportasi);
            //Send data
            SatuSehatController::getTokenSehat();
            $access_token = Session::get('tokenSatuSehat');
            // dd($access_token);
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            try {
                $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                    'headers' => [
                        'Authorization' => "Bearer {$access_token}"
                    ],
                    'json' => $transportasi
                ]);
            } catch (ClientException $e) {
                // echo $e->getRequest();
                // echo $e->getResponse();
                if ($e->hasResponse()) {
                    $response = $e->getResponse();

                    // dd($response);
                    $test = json_decode($response->getBody());
                    dd($test, 'transportasi');
                }

                $message = "Gagal kirim observasi transportasi pasien IGD " . $noRawat;

                Session::flash('error', $message);

                return $message;
            }

            // dd($response);

            $dataResponse = json_decode($response->getBody());

            // dd($data);

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
            // $displayWaktu = $formatWaktu->toW3cString();
            // dd($data, $formatWaktu->toW3cString());
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

                // dd($transportasi);
                //Send data
                SatuSehatController::getTokenSehat();
                $access_token = Session::get('tokenSatuSehat');
                // dd($access_token);
                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
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
            // $displayWaktu = $formatWaktu->toW3cString();
            // dd($data, $formatWaktu->toW3cString());
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

                // dd($transportasi);
                //Send data
                SatuSehatController::getTokenSehat();
                $access_token = Session::get('tokenSatuSehat');
                // dd($access_token);
                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                try {
                    $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}"
                        ],
                        'json' => $dataNyeri
                    ]);
                } catch (ClientException $e) {
                    // echo $e->getRequest();
                    // echo $e->getResponse();
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();

                        // dd($response);
                        $test = json_decode($response->getBody());
                        dd($test, 'status nyeri', $dataNyeri);
                    }

                    $message = "Gagal kirim observasi transportasi pasien IGD " . $noRawat;

                    Session::flash('error', $message);

                    // return $message;
                }

                // dd($response);

                $dataResponse = json_decode($response->getBody());

                // dd($data);

                if (!empty($dataResponse->id)) {
                    $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                    $update->asesmen_nyeri = $dataResponse->id;
                    $update->save();
                };

                if ($statusNyeri == true) {
                    // dd($data);
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
                    SatuSehatController::getTokenSehat();
                    $access_token = Session::get('tokenSatuSehat');
                    // dd($access_token);
                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataSkalaNyeri
                        ]);
                    } catch (ClientException $e) {
                        // echo $e->getRequest();
                        // echo $e->getResponse();
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            // dd($response);
                            $test = json_decode($response->getBody());
                            dd($test, 'skala nyeri');
                        }

                        $message = "Gagal kirim observasi transportasi pasien IGD " . $noRawat;

                        Session::flash('error', $message);

                        // return $message;
                    }

                    // dd($response);

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

                        SatuSehatController::getTokenSehat();
                        $access_token = Session::get('tokenSatuSehat');
                        // dd($access_token);
                        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                        try {
                            $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                                'headers' => [
                                    'Authorization' => "Bearer {$access_token}"
                                ],
                                'json' => $dataLokasiNyeri
                            ]);
                        } catch (ClientException $e) {
                            // echo $e->getRequest();
                            // echo $e->getResponse();
                            if ($e->hasResponse()) {
                                $response = $e->getResponse();

                                // dd($response);
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
            // $displayWaktu = $formatWaktu->toW3cString();
            $tekanan = explode('/', $data->tekanan_darah);
            // dd($data, $formatWaktu, $tekanan);
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
                    SatuSehatController::getTokenSehat();
                    $access_token = Session::get('tokenSatuSehat');
                    // dd($access_token);
                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataNadi
                        ]);
                    } catch (ClientException $e) {
                        // echo $e->getRequest();
                        // echo $e->getResponse();
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            // dd($response);
                            $test = json_decode($response->getBody());
                            dd($test, 'nadi');
                        }

                        $message = "Gagal kirim vital sign Nadi pasien IGD " . $noRawat;

                        Session::flash('error', $message);
                    }

                    // dd($response);

                    $dataResponse = json_decode($response->getBody());

                    // dd($data);

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
                    SatuSehatController::getTokenSehat();
                    $access_token = Session::get('tokenSatuSehat');
                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataPernafasan
                        ]);
                    } catch (ClientException $e) {
                        // echo $e->getRequest();
                        // echo $e->getResponse();
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            // dd($response);
                            $test = json_decode($response->getBody());
                            dd($test, 'pernafasan');
                        }

                        $message = "Gagal kirim vital sign Pernafasan pasien IGD " . $noRawat;

                        Session::flash('error', $message);
                    }

                    // dd($response);

                    $dataResponse = json_decode($response->getBody());

                    // dd($data);

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
                    SatuSehatController::getTokenSehat();
                    $access_token = Session::get('tokenSatuSehat');
                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataSistole
                        ]);
                    } catch (ClientException $e) {
                        // echo $e->getRequest();
                        // echo $e->getResponse();
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            // dd($response);
                            $test = json_decode($response->getBody());
                            dd($test, 'sistol');
                        }

                        $message = "Gagal kirim vital sign Sistol pasien IGD " . $noRawat;

                        Session::flash('error', $message);
                    }

                    $dataResponse = json_decode($response->getBody());

                    // dd($data);

                    if (!empty($dataResponse->id)) {
                        $update = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
                        $update->asesmen_sistol = $dataResponse->id;
                        $update->save();
                    };
                }

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
                    SatuSehatController::getTokenSehat();
                    $access_token = Session::get('tokenSatuSehat');
                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataDiastol
                        ]);
                    } catch (ClientException $e) {
                        // echo $e->getRequest();
                        // echo $e->getResponse();
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            // dd($response);
                            $test = json_decode($response->getBody());
                            dd($test, 'diastol');
                        }

                        $message = "Gagal kirim vital sign Diastol pasien IGD " . $noRawat;

                        Session::flash('error', $message);
                    }

                    // dd($response);

                    $dataResponse = json_decode($response->getBody());

                    // dd($data);

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
                    SatuSehatController::getTokenSehat();
                    $access_token = Session::get('tokenSatuSehat');
                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataSuhu
                        ]);
                    } catch (ClientException $e) {
                        // echo $e->getRequest();
                        // echo $e->getResponse();
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            // dd($response);
                            $test = json_decode($response->getBody());
                            // dd($test, 'suhu', $noRawat);
                        }

                        $message = "Gagal kirim vital sign Suhu pasien IGD " . $noRawat;

                        Session::flash('error', $message);
                    }

                    // dd($response);

                    $dataResponse = json_decode($response->getBody());

                    // dd($data);

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

                SatuSehatController::getTokenSehat();
                $access_token = Session::get('tokenSatuSehat');
                // dd($access_token);
                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
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

                    SatuSehatController::getTokenSehat();
                    $access_token = Session::get('tokenSatuSehat');
                    // dd($access_token);
                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
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

                    SatuSehatController::getTokenSehat();
                    $access_token = Session::get('tokenSatuSehat');
                    // dd($access_token);
                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                    try {
                        $response = $client->request('PUT', "fhir-r4/v1/Encounter/$encounter", [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $updateEncounter
                        ]);
                    } catch (ClientException $e) {
                        // echo $e->getRequest();
                        // echo $e->getResponse();
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            // dd($response);
                            $test = json_decode($response->getBody());
                            dd($test, 'status update encounter Meninggalkan IGD ', $updateEncounter);
                        }

                        $message = "Gagal update encounter pasien IGD " . $noRawat;

                        Session::flash('error', $message);
                    }
                }
            }
        }
    }

    public function sendServiceRequest($noRawat, $encounter)
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

            SatuSehatController::getTokenSehat();
            $access_token = Session::get('tokenSatuSehat');
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
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
}
