<?php

namespace App\Http\Controllers;

use App\Setting;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

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

    public function bundleData()
    {
        $pasien_tanggal = '2022-09-02';
        $data = DB::connection('mysqlkhanzadummy')->table('reg_periksa')
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

        foreach ($data as $key => $dataPengunjung) {
            $idRS = '10080055';
            $idPasien = SatuSehatController::patientSehat($dataPengunjung->ktp_pasien);
            $idDokter = SatuSehatController::practitioner($dataPengunjung->ktp_dokter);
            $idLokasi = SatuSehatController::getIdPoli($dataPengunjung->kd_poli);
            $diagnosaPrimer = SatuSehatController::getDiagnosisPrimerRalan($dataPengunjung->no_rawat);
            $uuidDiagnosaPrimer = Str::uuid();
            $diagnosaSekunder = SatuSehatController::getDiagnosisSekunderRalan($dataPengunjung->no_rawat);
            if ($diagnosaSekunder != null) {
                $uuidDiagnosaSekunder = Str::uuid();
            }

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
            // dd($day, $day2, $formatDay);

            $vital = SatuSehatController::getVital($dataPengunjung->no_rawat);
            $heartRate = floatval($vital->nadi);
            $respiratory = floatval($vital->respirasi);
            $darah = explode('/', $vital->tensi);
            $sistole = floatval($darah[0]);
            $diastole = floatval($darah[1]);
            // $waktu_mulai = new Carbon($waktuAwal);
            $temperature = floatval($vital->suhu_tubuh);

            //UUID
            $uuidEncounter = Str::uuid();
            $uuidCondition1 = Str::uuid();
            $uuidCondition2 = Str::uuid();
            $uuidHeart = Str::uuid();
            $uuidRespiratory = Str::uuid();
            $uuidSistol = Str::uuid();
            $uuidDiastol = Str::uuid();
            $uuidTemperature = Str::uuid();

            // dd($heartRate, $respiratory, $sistole, $diastole, $temperature, $formatWaktuMulai, $formatWaktuProgress, $formatWaktuSelesai);

            // dd($idDokter, $idPasien, $idLokasi, $diagnosaPrimer, $diagnosaSekunder, $waktu_mulai);


            $Encounter1 = [
                "fullUrl" => "urn:uuid:$uuidEncounter",
                "resource" => [
                    "resourceType" => "Encounter",
                    "status" => "finished",
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
                        "end" => "$formatWaktuProgress"
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
            $Encounter2 = [
                "fullUrl" => "urn:uuid:$uuidEncounter",
                "resource" => [
                    "resourceType" => "Encounter",
                    "status" => "finished",
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
                        "end" => "$formatWaktuProgress"
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
            $diagnosis1 = [
                "fullUrl" => "urn:uuid:$uuidCondition1",
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
            $diagnosis2 = [
                "fullUrl" => "urn:uuid:$uuidCondition2",
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
                    "encounter" => [
                        "reference" => "urn:uuid:$uuidEncounter",
                        "display" => "Pemeriksaan Fisik Nadi $dataPengunjung->nm_pasien di hari $formatDay"
                    ],
                    "effectiveDateTime" => "$dataPengunjung->tgl_registrasi",
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
                    "encounter" => [
                        "reference" => "urn:uuid:$uuidEncounter",
                        "display" => "Pemeriksaan Fisik Pernafasan $dataPengunjung->nm_pasien di hari $formatDay"
                    ],
                    "effectiveDateTime" => "$dataPengunjung->tgl_registrasi",
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
                    "encounter" => [
                        "reference" => "urn:uuid:$uuidEncounter",
                        "display" => "Pemeriksaan Fisik Sistolik $dataPengunjung->nm_pasien di hari $formatDay"
                    ],
                    "effectiveDateTime" => "$dataPengunjung->tgl_registrasi",
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
                    "encounter" => [
                        "reference" => "urn:uuid:$uuidEncounter",
                        "display" => "Pemeriksaan Fisik Diastolik $dataPengunjung->nm_pasien di hari $formatDay"
                    ],
                    "effectiveDateTime" => "$dataPengunjung->tgl_registrasi",
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
                    "encounter" => [
                        "reference" => "urn:uuid:$uuidEncounter",
                        "display" => "Pemeriksaan Fisik Suhu $dataPengunjung->nm_pasien di hari $formatDay"
                    ],
                    "effectiveDateTime" => "$dataPengunjung->tgl_registrasi",
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
            if (!empty($diagnosaSekunder)) {
                $dataBundle = [$Encounter1, $diagnosis1, $diagnosis2, $vital1, $vital2, $vital3, $vital4, $vital5];
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
            } catch (ClientException $e) {
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

            dd(($data->entry));

            foreach ($data->entry as $index => $dataRespone) {
                foreach ($dataRespone as $dataPoint) {
                    dd($dataPoint);
                    if (!empty($diagnosaSekunder)) {
                        if (($index == 0) and ($dataPoint->resourceType == 'Encounter')) {
                            echo 'test';
                        }
                    }
                }
            }
        }
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

    public function patientSehat($id)
    {
        $nik = $id;
        SatuSehatController::getTokenSehat();
        $access_token = Session::get('tokenSatuSehat');
        // dd($access_token);
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('GET', 'fhir-r4/v1/Patient?identifier=https://fhir.kemkes.go.id/id/nik|' . $nik, [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ]
            ]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }
            dd($test);
            // $id = Crypt::encrypt($id);
            Session::flash('error', $test->message);

            return redirect()->back()->withInput();
        }

        $data = json_decode($response->getBody());
        // $data = $data->entry;

        if ($data->total == 1) {
            foreach ($data->entry as $responseData) {
                // dd($responseData->resource);
                $data = $responseData->resource->id;
            }

            return $data;
            // dd($data);
        } else {
            return null;
        }
    }

    public function practitioner($id)
    {
        $nik = $id;
        SatuSehatController::getTokenSehat();
        $access_token = Session::get('tokenSatuSehat');
        // dd($access_token);
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('GET', 'fhir-r4/v1/Practitioner?identifier=https://fhir.kemkes.go.id/id/nik|' . $nik, [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ]
            ]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }
            dd($test);
            // $id = Crypt::encrypt($id);
            Session::flash('error', $test->message);

            return redirect()->back()->withInput();
        }

        $data = json_decode($response->getBody());
        // $data = $data->entry;

        // dd($data->resource->id);
        if ($data->total == 1) {
            foreach ($data->entry as $responseData) {
                $data = $responseData->resource->id;
                // dd($responseData->resource);
            }
            return $data;
        } else {
            return null;
        }
    }

    public function getIdPoli($id)
    {
        $data = DB::connection('mysqlkhanzadummy')->table('fhir_poliklinik')
            ->select(
                'fhir_poliklinik.kd_poli',
                'fhir_poliklinik.id_ihs',

            )
            ->where('fhir_poliklinik.kd_poli', $id)
            ->first();
        // dd($data);

        return $data->id_ihs;
    }

    public function getDiagnosisPrimerRalan($id)
    {
        $data = DB::connection('mysqlkhanzadummy')->table('diagnosa_pasien')
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
        // dd($data);

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }

        // return $data->id_ihs;
    }
    public function getDiagnosisSekunderRalan($id)
    {
        $data = DB::connection('mysqlkhanzadummy')->table('diagnosa_pasien')
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
        // dd($data);

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }

    public function getVital($id)
    {
        $data = DB::connection('mysqlkhanzadummy')->table('pemeriksaan_ralan')
            // ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            ->select(
                'pemeriksaan_ralan.no_rawat',
                'pemeriksaan_ralan.suhu_tubuh',
                'pemeriksaan_ralan.tensi',
                'pemeriksaan_ralan.nadi',
                'pemeriksaan_ralan.respirasi'
            )
            ->where('pemeriksaan_ralan.no_rawat', $id)
            ->first();

        return $data;
    }
}
