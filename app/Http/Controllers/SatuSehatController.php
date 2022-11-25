<?php

namespace App\Http\Controllers;

use App\LogErrorSatuSehat;
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
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
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
        session()->forget('anak');
        session()->forget('cucu');

        if (empty($request->get('tanggal'))) {
            $tanggal = Carbon::now();
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
        }
        $dataLog = ResponseSatuSehat::whereDate('tgl_registrasi', $tanggal)
            ->get();
        return view('satu_sehat.summary', compact('dataLog'));
    }

    public function bundleData()
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Api Bundle');
        session()->forget('cucu');
        set_time_limit(0);

        $pasien_tanggal = '2022-09-13';
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

        foreach ($data as $key => $dataPengunjung) {
            $cekLog = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->count();

            if ($cekLog == 0) {
                $idRS = '10080055';
                $idPasien = SatuSehatController::patientSehat($dataPengunjung->ktp_pasien);
                $idDokter = SatuSehatController::practitioner($dataPengunjung->ktp_dokter);
                $idLokasi = SatuSehatController::getIdPoli($dataPengunjung->kd_poli);
                $diagnosaPrimer = SatuSehatController::getDiagnosisPrimerRalan($dataPengunjung->no_rawat);

                // dd($dataPengunjung->no_rawat);

                if ((!empty($idPasien)) && (!empty($idDokter)) && (!empty($diagnosaPrimer)) && (!empty($idLokasi))) {
                    $diagnosaSekunder = SatuSehatController::getDiagnosisSekunderRalan($dataPengunjung->no_rawat);
                    $procedurePasien = SatuSehatController::getProcedureRalan($dataPengunjung->no_rawat);
                    $cekDiet = SatuSehatController::getDiet($dataPengunjung->no_rawat, $dataPengunjung->tgl_registrasi); //nyoba bundle composition

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
                            $diastole = floatval($darah[1]);
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

                    //UUID
                    $uuidEncounter = Str::uuid();
                    $uuidDiagnosaPrimer = Str::uuid();
                    $uuidCondition1 = Str::uuid();
                    $uuidCondition2 = Str::uuid();

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
                        //diagnosa 2
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
                    }

                    // dd($heartRate, $respiratory, $sistole, $diastole, $temperature, $formatWaktuMulai, $formatWaktuProgress, $formatWaktuSelesai);
                    // dd($idDokter, $idPasien, $idLokasi, $diagnosaPrimer, $diagnosaSekunder, $waktu_mulai);

                    //diagnosa 1
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
                    //     dd($dataBundle);
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

                    // if ($dataPengunjung->no_rawat == '2022/09/16/000022') {
                    //     dd($data);
                    // }

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
                }
            }
        }
        $dataLog = ResponseSatuSehat::whereDate('created_at', Carbon::now())
            ->get();

        // dd($dataLog);

        return view('satu_sehat.client_bundle', compact('dataLog'));
    }

    public function sendEncounter()
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Api Composition');
        session()->forget('cucu');
        set_time_limit(0);

        $pasien_tanggal = '2022-09-13';
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

        foreach ($data as $key => $dataPengunjung) {
            $cekLog = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->count();

            if ($cekLog == 0) {
                $idRS = '10080055';
                $idPasien = SatuSehatController::patientSehat($dataPengunjung->ktp_pasien);
                $idDokter = SatuSehatController::practitioner($dataPengunjung->ktp_dokter);
                $idLokasi = SatuSehatController::getIdPoli($dataPengunjung->kd_poli);

                if ((!empty($idPasien)) && (!empty($idDokter))) {
                    //Waktu
                    $waktuAwal = $dataPengunjung->tgl_registrasi . ' ' . $dataPengunjung->jam_reg;
                    $waktu_mulai = new Carbon($waktuAwal);
                    $waktuSelesai = Carbon::parse($waktuAwal)->addHour(2);
                    $formatWaktuMulai = Carbon::parse($waktuAwal)->format('Y-m-d') . 'T' . Carbon::parse($waktuAwal)->format('H:i:s+07:00');
                    $waktuInprogress = Carbon::parse($waktuAwal)->addHour();
                    $formatWaktuProgress = Carbon::parse($waktuInprogress)->format('Y-m-d') . 'T' . Carbon::parse($waktuInprogress)->format('H:i:s+07:00');

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
                            "reference" => "Patient/100000030009",
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

                    dd($data);
                }
            }
        }
    }

    public function sendComposition()
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Api Composition');
        session()->forget('cucu');
        set_time_limit(0);

        $pasien_tanggal = '2022-09-13';
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

        foreach ($data as $key => $dataPengunjung) {
            $cekLog = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
            $cekDiet = SatuSehatController::getDiet('2022/08/23/000193', '2022-08-24');

            // dd($cekDiet);

            if ((!empty($cekLog)) && (empty($cekLog->composition_id)) && (!empty($cekDiet))) {
                // dd($cekLog, $cekDiet->monitoring_evaluasi);
                $idRS = '10080055';
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
                    $response = $client->request('POST', 'fhir-r4/v1/Composition', [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}"
                        ],
                        'json' => $compositionData
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

                dd($data);
            }
        }
    }

    public function sendMedication()
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Api Medication');
        session()->forget('cucu');
        set_time_limit(0);

        $pasien_tanggal = '2022-09-16';
        $idRS = '10080055';

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


        foreach ($data as $key => $dataPengunjung) {
            $idPasien = SatuSehatController::patientSehat($dataPengunjung->ktp_pasien);
            $idDokter = SatuSehatController::practitioner($dataPengunjung->ktp_dokter);
            $idLokasi = SatuSehatController::getIdPoli($dataPengunjung->kd_poli);

            $getResep = SatuSehatController::getResepObat($dataPengunjung->no_rawat);
            $idCounter = SatuSehatController::getEncounterId($dataPengunjung->no_rawat);

            // dd($getResep);

            if ((!empty($getResep)) && (!empty($idCounter))) {
                $listObat = SatuSehatController::getListObat($getResep->no_resep);
                // dd($listObat);

                foreach ($listObat as $index => $dataListObat) {
                    // dd($dataListObat);
                    $noUrutResep = $index + 1;
                    $noresep = $dataListObat->no_resep . '-' . $noUrutResep;
                    // dd($noresep);

                    //Cek List Obat di Response Medication apakah sudah ada
                    $cekResponse = ResponseMedicationSatuSehat::where('noResep', $noresep)->first();
                    //Get Id Obat
                    $idObat = SatuSehatController::getIdObat($dataListObat->kode_brng);
                    dd($dataListObat, $idObat);

                    if ((empty($cekResponse)) && (!empty($idObat))) {
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
                                "coding" => [
                                    [
                                        "system" => "http://sys-ids.kemkes.go.id/kfa",
                                        "code" => "93001019",
                                        "display" => "Obat Anti Tuberculosis / Rifampicin 150 mg / Isoniazid 75 mg / Pyrazinamide 400 mg / Ethambutol 275 mg Kaplet Salut Selaput (KIMIA FARMA)"
                                    ]
                                ]
                            ],
                            "status" => "active",
                            // "manufacturer" => [ //ora usah ra popo jare
                            //     "reference" => "Organization/900001"
                            // ],
                            "form" => [
                                "coding" => [
                                    [
                                        "system" => "https://terminology.kemkes.go.id/CodeSystem/medication-form",
                                        "code" => "BS023",
                                        "display" => "Kaplet Salut Selaput"
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
                            "extension" => [
                                [
                                    "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/MedicationType",
                                    "valueCodeableConcept" => [
                                        "coding" => [
                                            [
                                                "system" => "https://terminology.kemkes.go.id/CodeSystem/medication-type",
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
                                'json' => $medication1
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

                        // dd($data, $data->id, $data->resourceType, $index);

                        if (!empty($data->id) && $data->resourceType == "Medication") {

                            $simpan = new ResponseMedicationSatuSehat();
                            $simpan->noRawat = $dataPengunjung->no_rawat;
                            $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                            $simpan->noResep = $noresep;
                            $simpan->medication1 = $data->id;
                            $simpan->save();

                            $response1 = SatuSehatController::getMedicationId($noresep);
                            // dd($response1, $idCounter->encounter_id);
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
                                        "coding" => [
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
                                    "reference" => "Medication/$response1->medication1",
                                    "display" => "Obat Anti Tuberculosis / Rifampicin 150 mg / Isoniazid 75 mg / Pyrazinamide 400 mg / Ethambutol 275 mg Kaplet Salut Selaput (KIMIA FARMA)"
                                ],
                                "subject" => [
                                    "reference" => "Patient/100000030009",
                                    "display" => "$dataPengunjung->nm_pasien"
                                ],
                                "encounter" => [
                                    "reference" => "Encounter/$idCounter->encounter_id"
                                ],
                                "authoredOn" => "$dataPengunjung->tgl_registrasi",
                                "requester" => [
                                    "reference" => "Practitioner/$idDokter",
                                    "display" => "$dataPengunjung->nama_dokter"
                                ],
                                "reasonCode" => [
                                    [
                                        "coding" => [
                                            [
                                                "system" => "http://hl7.org/fhir/sid/icd-10",
                                                "code" => "A15.0", //diagnosa pasien icd 10
                                                "display" => "Tuberculosis of lung, confirmed by sputum microscopy with or without culture"
                                            ]
                                        ]
                                    ]
                                ],
                                "courseOfTherapyType" => [ //optional
                                    "coding" => [
                                        [
                                            "system" => "http://terminology.hl7.org/CodeSystem/medicationrequest-course-of-therapy",
                                            "code" => "continuous",
                                            "display" => "Continuing long term therapy"
                                        ]
                                    ]
                                ],
                                "dosageInstruction" => [
                                    [
                                        "sequence" => 1,
                                        "text" => "4 tablet per hari",
                                        "additionalInstruction" => [
                                            [
                                                "text" => "Diminum setiap hari"
                                            ]
                                        ],
                                        "patientInstruction" => "4 tablet perhari, diminum setiap hari tanpa jeda sampai prose pengobatan berakhir",
                                        "timing" => [
                                            "repeat" => [
                                                "frequency" => 1,
                                                "period" => 1,
                                                "periodUnit" => "d"
                                            ]
                                        ],
                                        "route" => [ //wajib
                                            "coding" => [
                                                [
                                                    "system" => "http://www.whocc.no/atc",
                                                    "code" => "O",
                                                    "display" => "Oral"
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
                                                    "value" => 4,
                                                    "unit" => "TAB",
                                                    "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                                                    "code" => "TAB"
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                "dispenseRequest" => [
                                    "dispenseInterval" => [
                                        "value" => 1,
                                        "unit" => "days",
                                        "system" => "http://unitsofmeasure.org",
                                        "code" => "d"
                                    ],
                                    "validityPeriod" => [ //optional
                                        "start" => "2022-01-01",
                                        "end" => "2022-01-30"
                                    ],
                                    "numberOfRepeatsAllowed" => 0,
                                    "quantity" => [ //wajib
                                        "value" => 120,
                                        "unit" => "TAB",
                                        "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                                        "code" => "TAB"
                                    ],
                                    "expectedSupplyDuration" => [
                                        "value" => 30,
                                        "unit" => "days",
                                        "system" => "http://unitsofmeasure.org",
                                        "code" => "d"
                                    ],
                                    "performer" => [
                                        "reference" => "Organization/$idRS"
                                    ]
                                ]
                            ];

                            // dd($medicationRequest);

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

                            //Update data di table respone medication request
                            $update = ResponseMedicationSatuSehat::where('noResep', $noresep)->first();
                            $update->medicationRequest = $data->id;
                            $update->save();
                            // $update2 = ResponseMedicationSatuSehat::where('noResep', $noresep)->first();
                            // dd($data, $update2);
                            if (!empty($data->id) && $data->resourceType == "MedicationRequest") {
                                //Langsung kirim medication 1 sebagai medication2
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

                                // dd($data);
                                //Update data di table respone medication2
                                $update = ResponseMedicationSatuSehat::where('noResep', $noresep)->first();
                                $update->medication2 = $data->id;
                                $update->save();

                                //variabel dinamis
                                $apoteker = SatuSehatController::practitioner('3309090909870004');
                                $lokasiApotek = '6a647d0b-d880-4e91-aa87-7bc7e4f7c066';
                                //Waktu
                                $waktuAwal = $getResep->tgl_permintaan . ' ' . $getResep->jam_permintaan;
                                $waktu_mulai = new Carbon($waktuAwal);
                                $formatWaktuMulai = Carbon::parse($waktuAwal)->format('Y-m-d') . 'T' . Carbon::parse($waktuAwal)->format('H:i:s+07:00');
                                $waktuSelesai = $getResep->tgl_penyerahan . ' ' . $getResep->jam_penyerahan;
                                $waktu_selesai = new Carbon($waktuSelesai);
                                $formatWaktuSelesai = Carbon::parse($waktuSelesai)->format('Y-m-d') . 'T' . Carbon::parse($waktuSelesai)->format('H:i:s+07:00');

                                // dd($apoteker);

                                // $update2 = ResponseMedicationSatuSehat::where('noResep', $noresep)->first();
                                // dd($data, $update2);
                                $response2 = SatuSehatController::getMedicationId($noresep);
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
                                            "reference" => "Medication/$response2->medication2", //wajib
                                            "display" => "Obat Anti Tuberculosis / Rifampicin 150 mg / Isoniazid 75 mg / Pyrazinamide 400 mg / Ethambutol 275 mg Kaplet Salut Selaput (KIMIA FARMA)" //free text
                                        ],
                                        "subject" => [ //wajib
                                            "reference" => "Patient/100000030009",
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
                                                "reference" => "MedicationRequest/$response2->medicationRequest"
                                            ]
                                        ],
                                        "quantity" => [ //wajib
                                            "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                                            "code" => "TAB",
                                            "value" => 120
                                        ],
                                        "daysSupply" => [ //optional
                                            "value" => 30,
                                            "unit" => "Day",
                                            "system" => "http://unitsofmeasure.org",
                                            "code" => "d"
                                        ],
                                        "whenPrepared" => "$formatWaktuMulai", //optional
                                        "whenHandedOver" => "$formatWaktuSelesai", //optional
                                        "dosageInstruction" => [
                                            [
                                                "sequence" => 1, //wajib
                                                "text" => "Diminum 4 tablet sekali dalam sehari",
                                                "timing" => [
                                                    "repeat" => [
                                                        "frequency" => 1,
                                                        "period" => 1,
                                                        "periodUnit" => "d"
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
                                                            "value" => 4,
                                                            "unit" => "TAB",
                                                            "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                                                            "code" => "TAB"
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

                                    //Update data di table respone medication request
                                    $update = ResponseMedicationSatuSehat::where('noResep', $noresep)->first();
                                    $update->medicationDispence = $data->id;
                                    $update->save();
                                }
                            }
                        }
                    }
                }
            }
        }
        $dataLog = ResponseMedicationSatuSehat::whereDate('created_at', Carbon::now())
            ->get();

        // dd($dataLog);

        return view('satu_sehat.client_apotek', compact('dataLog'));
    }

    public function sendLab()
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Api Poli Rujuk Lab');
        session()->forget('cucu');
        set_time_limit(0);

        $pasien_tanggal = '2022-09-16';
        $idRS = '10080055';

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
            ->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->where('reg_periksa.stts', 'Sudah')
            ->where('reg_periksa.kd_poli', '!=', 'LAB')
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
                        // dd($PeriksaLab, $mappingLoinc);

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
                                    "reference" => "Patient/100000030009"
                                ],
                                "encounter" => [
                                    "reference" => "Encounter/$idCounter->encounter_id",
                                    "display" => "Permintaan $PeriksaLab->nm_perawatan pada $PeriksaLab->tgl_periksa pukul $PeriksaLab->jam WIB"
                                ],
                                "occurrenceDateTime" => $PeriksaLab->tgl_periksa . "T" . $PeriksaLab->jam . "+07:00",
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

                            // dd($ServiceRequest);

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

                                $message = "Error Kirim Service Request";

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
                                                "system" => "http://snomed.info/sct",
                                                "code" => "119297000",
                                                "display" => "Blood specimen (specimen)"
                                            ]
                                        ]
                                    ],
                                    "collection" => [
                                        "method" => [
                                            "coding" => [
                                                [
                                                    "system" => "https://snomed.info/sct",
                                                    "code" => "82078001",
                                                    "display" => "Collection of blood specimen for laboratory (procedure)"
                                                ]
                                            ]
                                        ],
                                        "collectedDateTime" => "2022-06-14T08:15:00+07:00"
                                    ],
                                    "subject" => [
                                        "reference" => "Patient/100000030009",
                                        "display" => "$pasienLab->nm_pasien"
                                    ],
                                    "request" => [
                                        [
                                            "reference" => "ServiceRequest/$idServiceRequest"
                                        ]
                                    ],
                                    "receivedTime" => $cekLab->tgl_sampel . "T" . $cekLab->jam_sampel . "+07:00"
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

                                    $message = "Error Kirim Specimen";

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
                                        ->select(
                                            'detail_periksa_lab.no_rawat',
                                            'detail_periksa_lab.kd_jenis_prw',
                                            'detail_periksa_lab.tgl_periksa',
                                            'detail_periksa_lab.jam',
                                            'detail_periksa_lab.nilai',
                                            'detail_periksa_lab.nilai_rujukan',
                                            'detail_periksa_lab.keterangan',
                                            'jns_perawatan_lab.nm_perawatan'
                                        )
                                        ->where('detail_periksa_lab.no_rawat', $pasienLab->no_rawat)
                                        ->where('detail_periksa_lab.kd_jenis_prw', $PeriksaLab->kd_jenis_prw)
                                        ->get();

                                    // dd($pasienLab, $cekLab, $idCounter->encounter_id, $cekLab->dokter_perujuk, $dokterPerujuk);
                                    foreach ($detailLab as $DetailLab) {
                                        //cek nilai hasil lab kosong atau masih dalam proses jika tidak lanjut
                                        if ((!empty($DetailLab->nilai)) && (strpos($DetailLab->nilai, "proses") === false)) {
                                            //Seharusnya cek dulu ini paket atau tidak hasilnya juga di foreach tp ini lurus2 aja dulu
                                            //dah diatas ya dicek

                                            $dataHasil = SatuSehatController::getLoinc($DetailLab->kd_jenis_prw);

                                            // dd($DetailLab, "test", $dataHasil, is_string($DetailLab->nilai), is_numeric($DetailLab->nilai), empty($DetailLab->nilai));
                                            if (!empty($dataHasil)) {
                                                if ($dataHasil->tipe_hasil_pemeriksaan == "Nominal") {
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
                                                            "reference" => "Patient/100000030009"
                                                        ],
                                                        "encounter" => [
                                                            "reference" => "Encounter/$idCounter->encounter_id"
                                                        ],
                                                        "effectiveDateTime" => "$DetailLab->tgl_periksa",
                                                        "issued" => $DetailLab->tgl_periksa . "T" . $DetailLab->jam . "+07:00",
                                                        "performer" => [
                                                            [
                                                                "reference" => "Practitioner/N10000001"
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
                                                                    "system" => "http://loinc.org",
                                                                    "code" => "LA19710-5",
                                                                    "display" => "Group A"
                                                                ]
                                                            ]
                                                        ]
                                                    ];
                                                } elseif ($dataHasil->tipe_hasil_pemeriksaan == "Ordinal") {
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
                                                            "reference" => "Patient/100000030009"
                                                        ],
                                                        "encounter" => [
                                                            "reference" => "Encounter/$idCounter->encounter_id"
                                                        ],
                                                        "effectiveDateTime" => "$DetailLab->tgl_periksa",
                                                        "issued" => $DetailLab->tgl_periksa . "T" . $DetailLab->jam . "+07:00",
                                                        "performer" => [
                                                            [
                                                                "reference" => "Practitioner/N10000001"
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
                                                                    "system" => "http://snomed.info/sct",
                                                                    "code" => "260347006",
                                                                    "display" => "+"
                                                                ]
                                                            ]
                                                        ],
                                                        "referenceRange" => [
                                                            [
                                                                "text" => "$DetailLab->nilai_rujukan"
                                                            ]
                                                        ]
                                                    ];
                                                } elseif ($dataHasil->tipe_hasil_pemeriksaan == "Quantitative") { //
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
                                                            "reference" => "Patient/100000030009"
                                                        ],
                                                        "encounter" => [
                                                            "reference" => "Encounter/$idCounter->encounter_id"
                                                        ],
                                                        "effectiveDateTime" => "$DetailLab->tgl_periksa",
                                                        "issued" => $DetailLab->tgl_periksa . "T" . $DetailLab->jam . "+07:00",
                                                        "performer" => [
                                                            [
                                                                "reference" => "Practitioner/N10000001"
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
                                                            "reference" => "Patient/100000030009"
                                                        ],
                                                        "encounter" => [
                                                            "reference" => "Encounter/$idCounter->encounter_id"
                                                        ],
                                                        "effectiveDateTime" => "$DetailLab->tgl_periksa",
                                                        "issued" => $DetailLab->tgl_periksa . "T" . $DetailLab->jam . "+07:00",
                                                        "performer" => [
                                                            [
                                                                "reference" => "Practitioner/N10000001"
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
                                                        "valueString" => "Sediaan apus terdiri atas sel-sel epitel skuamosa normal, sel-sel metaplastik dan leukosit"
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
                                                        "code" => "CM",
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
                                            "reference" => "Patient/100000030009"
                                        ],
                                        "encounter" => [
                                            "reference" => "Encounter/$idCounter->encounter_id"
                                        ],
                                        "effectiveDateTime" => $PeriksaLab->tgl_periksa . "T" . $PeriksaLab->jam . "+07:00",
                                        "issued" => $PeriksaLab->tgl_periksa . "T" . $PeriksaLab->jam . "+07:00",
                                        "performer" => [
                                            [
                                                "reference" => "Practitioner/N10000001"
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
                                            // dd($test);
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

        $dataLog = ResponseLabSatuSehat::all();

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

    public function patientSehat($id)
    {
        // $nik = $id;

        if (is_numeric($id)) {
            $cek_lokal = PasienSehat::where('nik', $id)->first();

            if (!empty($cek_lokal)) {
                return $cek_lokal->satu_sehat_id;
            } else {
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

                    $simpanID = new PasienSehat();
                    $simpanID->nik = $id;
                    $simpanID->satu_sehat_id = $data;
                    $simpanID->save();

                    return $data;
                    // dd($data);
                } else {
                    $error = new LogErrorSatuSehat();
                    $error->subject = 'Pasien';
                    $error->keterangan = $id . ' tidak ditemukan di Satu Sehat';
                    $error->save();

                    return null;
                }
            }
        } else {
            return null;
        }
    }

    public function practitioner($id)
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

                    $simpanID = new PraktisiSehat();
                    $simpanID->nik = $id;
                    $simpanID->satu_sehat_id = $data;
                    $simpanID->save();

                    return $data;
                } else {
                    $error = new LogErrorSatuSehat();
                    $error->subject = 'Praktitioner';
                    $error->keterangan = $id . ' tidak ditemukan di Satu Sehat';
                    $error->save();

                    return null;
                }
            }
        } else {
            return null;
        }
    }

    public function getIdPoli($id)
    {
        $data = DB::connection('mysqlkhanza')->table('fhir_poliklinik')
            ->select(
                'fhir_poliklinik.kd_poli',
                'fhir_poliklinik.id_ihs',

            )
            ->where('fhir_poliklinik.kd_poli', $id)
            ->first();
        // dd($data);
        if (!empty($data)) {
            return $data->id_ihs;
        } else {
            $error = new LogErrorSatuSehat();
            $error->subject = 'Lokasi';
            $error->keterangan = $id . ' tidak ditemukan di Satu Sehat';
            $error->save();

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
        $data = DB::connection('mysqlkhanza')->table('pemeriksaan_ralan')
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
                'resep_obat.jam_penyerahan',
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

    public function getIdObat($kd_obat)
    {
        $data = DB::connection('mysqlkhanza')->table('fhir_farmasi')
            ->select(
                'fhir_farmasi.kode_brng',
                'fhir_farmasi.id_ihs'
            )
            ->where('fhir_farmasi.kode_brng', $kd_obat)
            ->first();

        if (!empty($data)) {
            return $data->id_ihs;
        } else {
            $error = new LogErrorSatuSehat();
            $error->subject = 'Obat';
            $error->keterangan = $kd_obat . ' Kode Obat tidak ditemukan di Mapping Satu Sehat';
            $error->save();

            return null;
        }
    }

    public function getMedicationId($noResep)
    {
        $data = ResponseMedicationSatuSehat::where('noResep', $noResep)
            ->first();

        return $data;
    }

    public function getEncounterId($no_rawat)
    {
        $data = ResponseSatuSehat::where('noRawat', $no_rawat)
            ->first();

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
            $error = new LogErrorSatuSehat();
            $error->subject = 'Lab';
            $error->keterangan = $id . ' Kode perawatan Lab belum ditemukan dimapping';
            $error->save();

            return null;
        }
    }
}
