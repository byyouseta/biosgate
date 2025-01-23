<?php

namespace App\Http\Controllers;

use App\LogErrorSatuSehat;
use App\ResponseIgdSatuSehat;
use App\ResponseRanapSatuSehat;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RanapSehatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Ranap Satu Sehat');
        session()->forget('cucu');

        if (empty($request->get('tanggal'))) {
            $tanggal = Carbon::now();
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
        }

        $dataLog = ResponseRanapSatuSehat::whereDate('tgl_registrasi', $tanggal)
            ->get();

        $errorLog = LogErrorSatuSehat::whereDate('created_at', $tanggal)->get();

        // dd($errorLog);

        return view('satu_sehat.summaryRanap', compact('dataLog', 'errorLog'));
    }

    public function sendEncounter(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Ranap Satu Sehat');
        session()->put('cucu', 'Client Kirim Encounter Ranap');
        set_time_limit(0);

        if (empty($request->get('tanggal'))) {
            $pasien_tanggal = Carbon::now()->format('Y-m-d');
            $kemarin = Carbon::yesterday()->format('Y-m-d');

            $data = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->leftJoin('dpjp_ranap', 'dpjp_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->leftJoin('pegawai', 'pegawai.nik', '=', 'dpjp_ranap.kd_dokter')
                ->select(
                    'reg_periksa.no_rkm_medis',
                    'reg_periksa.no_rawat',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.jam_reg',
                    'dpjp_ranap.kd_dokter',
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
                    'kamar_inap.kd_kamar',
                    'kamar_inap.tgl_masuk',
                    'kamar_inap.jam_masuk'
                )
                // ->where('reg_periksa.stts', 'Sudah')
                // ->where('reg_periksa.no_rawat', '=', '2023/03/09/000107')
                // ->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
                // ->orWhere('reg_periksa.tgl_registrasi', $kemarin)
                ->whereBetween('reg_periksa.tgl_registrasi', [$kemarin, $pasien_tanggal])
                ->where('reg_periksa.status_lanjut', 'Ranap')
                ->orderBy('reg_periksa.no_rawat', 'ASC')
                ->get();
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
            $pasien_tanggal = Carbon::parse($tanggal)->format('Y-m-d');

            $data = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->leftJoin('dpjp_ranap', 'dpjp_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->leftJoin('pegawai', 'pegawai.nik', '=', 'dpjp_ranap.kd_dokter')
                ->select(
                    'reg_periksa.no_rkm_medis',
                    'reg_periksa.no_rawat',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.jam_reg',
                    'dpjp_ranap.kd_dokter',
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
                    'kamar_inap.kd_kamar',
                    'kamar_inap.tgl_masuk',
                    'kamar_inap.jam_masuk'
                )
                ->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
                ->where('reg_periksa.status_lanjut', 'Ranap')
                ->orderBy('reg_periksa.no_rawat', 'ASC')
                ->get();
        }

        // dd($data);
        // $pasien_tanggal = Carbon::now()->format('Y-m-d');
        // $pasien_tanggal = "2023-10-10";
        $idRS = env('IDRS');

        foreach ($data as $dataPengunjung) {
            $idPasien = SatuSehatController::patientSehat($dataPengunjung->ktp_pasien);
            $idKamar = RanapSehatController::getIdKamar($dataPengunjung->kd_kamar);
            if (!empty($idKamar)) {
                $pecahCode = explode(' ', $idKamar->kelas);
                $codeKelas = strtolower($pecahCode[1]);
            }
            if (!empty($dataPengunjung->ktp_dokter)) {
                $idPractition = SatuSehatController::practitioner($dataPengunjung->ktp_dokter);
            }
            $idRequest = RanapSehatController::getRequestFromIgd($dataPengunjung->no_rawat);
            // dd($idPractition, $idPasien, $idKamar);
            $waktuMulai = Carbon::parse($dataPengunjung->tgl_masuk . ' ' . $dataPengunjung->jam_masuk)->locale('id');
            $waktuMulai->setTimezone('UTC');
            $formatMulai = Carbon::parse($waktuMulai)->format('Y-m-d') . 'T' . Carbon::parse($waktuMulai)->format('H:i:s') . '+00:00';
            // dd($idPasien, $idKamar, $idRequest);

            $cekStatus = ResponseRanapSatuSehat::where('noRawat', $dataPengunjung->no_rawat)
                ->count();


            if (($cekStatus == 0) && (!empty($idPasien)) && (!empty($idPractition)) && (!empty($idRequest)) && (!empty($idKamar->id_ihs))) {

                // if (!empty($idPasien)) {
                //     dd($cekStatus, $idPasien, $idPractition, $idRequest, $idKamar, $dataPengunjung);
                // }

                $dataEncounter = [
                    "resourceType" => "Encounter",
                    "identifier" => [
                        [
                            "system" => "http://sys-ids.kemkes.go.id/encounter/$idRS",
                            "value" => "$dataPengunjung->no_rawat"
                        ]
                    ],
                    "status" => "in-progress",
                    "class" => [
                        "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                        "code" => "IMP",
                        "display" => "inpatient encounter"
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
                                "reference" => "Location/$idKamar->id_ihs",
                                "display" => "$idKamar->kd_kamar $idKamar->nm_bangsal"
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
                                                        "system" => "http://terminology.kemkes.go.id/CodeSystem/locationServiceClass-Inpatient",
                                                        "code" => "$codeKelas",
                                                        "display" => "$idKamar->kelas"
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
                        "reference" => "Organization/$idRS"
                    ],
                    "basedOn" => [
                        [
                            "reference" => "ServiceRequest/$idRequest"
                        ]
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
                        // dd($test, $dataEncounter);
                    }

                    $message = "Gagal kirim encounter Ranap pasien " . $dataPengunjung->no_rawat;

                    Session::flash('error', $message);

                    goto KirimPasienLain;
                }

                // dd($response);

                $data = json_decode($response->getBody());

                // dd($data);

                if (!empty($data->id)) {
                    $simpan = new ResponseRanapSatuSehat();
                    $simpan->noRawat = $dataPengunjung->no_rawat;
                    $simpan->tgl_registrasi = $dataPengunjung->tgl_registrasi;
                    $simpan->encounter_id = $data->id;
                    $simpan->save();
                }
            }

            KirimPasienLain:
        }

        $dataLog = ResponseRanapSatuSehat::whereDate('tgl_registrasi', $pasien_tanggal)->get();
        // dd($dataLog);

        return view('satu_sehat.client_ranap', compact('dataLog'));
    }

    public function closeEncounter()
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Ranap Satu Sehat');
        session()->put('cucu', 'Client Update Encounter Ranap');
        set_time_limit(0);

        $dataEncounter = ResponseRanapSatuSehat::where('kondisi_stabil', null)
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

            // dd($cekPulang);
            if (isset($cekPulang) && ($cekPulang->status_bayar == 'Sudah Bayar')) {

                if ($dataTerkirim->asesmen_nadi == null) {
                    RanapSehatController::sendVitalSign($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                    // RanapSehatController::copySalahKeIgd($dataTerkirim->noRawat);
                }
                if ($dataTerkirim->diagnosis_primer == null) {
                    RanapSehatController::sendDiagnosa($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                }
                if ($dataTerkirim->kondisi_stabil == null) {
                    // if ($dataTerkirim->noRawat == '2024/03/04/000388') {
                    //     dd($dataTerkirim);
                    // }
                    RanapSehatController::sendUpdateKepulangan($dataTerkirim->noRawat, $dataTerkirim->encounter_id);
                }
                // if ($dataTerkirim->kondisi_stabil != null) {
                //     $update = ResponseIgdSatuSehat::where('noRawat', $dataTerkirim->noRawat)->first();
                //     $update->cara_keluar = 'IGD Pulang';
                //     $update->save();
                // };
            }
        }
        // $data = IgdSehatController::sendTransportasiKedatangan();
        $dataLog = ResponseRanapSatuSehat::whereDate('updated_at', Carbon::now())
            ->orderBy('updated_at', 'DESC')
            ->limit(30)
            ->get();
        // $dataLog = ResponseIgdSatuSehat::whereDate('tgl_registrasi', $pasien_tanggal)->get();
        // dd($dataLog);

        return view('satu_sehat.client_ranap', compact('dataLog'));
    }


    public function getIdKamar($idKamar)
    {
        $data = DB::connection('mysqlkhanza')->table('kamar')
            ->join('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->leftJoin('fhir_kamar', 'fhir_kamar.kd_kamar', '=', 'kamar.kd_kamar')
            ->select(
                'kamar.kd_kamar',
                'kamar.kelas',
                'bangsal.nm_bangsal',
                'fhir_kamar.id_ihs'
            )
            ->where('kamar.kd_kamar', $idKamar)
            ->first();

        // dd($data);
        if (!empty($data)) {
            return $data;
        } else {
            $cari = LogErrorSatuSehat::where('keterangan', 'like', '%' . $idKamar . '%')
                ->count();
            if ($cari == 0) {
                $simpan = new LogErrorSatuSehat();
                $simpan->subject = 'Kamar Inap';
                $simpan->keterangan = "id kamar $idKamar tidak ditemukan";
                $simpan->save();
            }

            return null;
        }
    }

    public function getRequestFromIgd($noRawat)
    {
        $data = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();

        if (!empty($data)) {
            return $data->service_request;
        } else {
            return null;
        }
    }

    public function getPrimaryDiagnosisRanap($noRawat)
    {
        $data = ResponseRanapSatuSehat::where('noRawat', $noRawat)->first();

        if (!empty($data)) {
            if (!empty($data->diagnosis_primer)) {
                return $data->diagnosis_primer;
            } elseif (!empty($data->diagnosis_sekunder)) {
                return $data->diagnosis_sekunder;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function sendVitalSign($noRawat, $encounter)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pemeriksaan_ranap', 'pemeriksaan_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'pemeriksaan_ranap.nip')
            ->select(
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pegawai.no_ktp as ktp_petugas',
                'pemeriksaan_ranap.no_rawat',
                'pemeriksaan_ranap.tgl_perawatan',
                'pemeriksaan_ranap.jam_rawat',
                'pemeriksaan_ranap.suhu_tubuh',
                'pemeriksaan_ranap.tensi',
                'pemeriksaan_ranap.nadi',
                'pemeriksaan_ranap.respirasi'
            )
            ->selectRaw(DB::raw("CONCAT(pemeriksaan_ranap.tgl_perawatan, ' ',pemeriksaan_ranap.jam_rawat) AS waktu_rawat"))
            ->where('pemeriksaan_ranap.no_rawat', $noRawat)
            ->orderBy('waktu_rawat', 'DESC')
            ->first();

        // if ($noRawat == '2024/04/25/000063') {
        //     dd($data);
        // }

        if (!empty($data)) {
            $idPasien = SatuSehatController::patientSehat($data->ktp_pasien);
            $idPractition = SatuSehatController::practitioner($data->ktp_petugas);
            $waktuPerawatan = "$data->tgl_perawatan $data->jam_rawat";
            $formatWaktu = Carbon::parse($waktuPerawatan)->setTimezone('UTC')->toW3cString();
            // $displayWaktu = $formatWaktu->toW3cString();
            $tekanan = explode('/', $data->tensi);
            // dd($data, $formatWaktu, $tekanan);

            if (!empty($idPractition)) {
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
                            // dd($test, 'nadi');
                        }

                        $message = "Gagal kirim vital sign Nadi pasien Ranap " . $noRawat;

                        Session::flash('error', $message);
                    }

                    // dd($response);

                    $dataResponse = json_decode($response->getBody());

                    // dd($data);

                    if (!empty($dataResponse->id)) {
                        $update = ResponseRanapSatuSehat::where('noRawat', $noRawat)->first();
                        $update->asesmen_nadi = $dataResponse->id;
                        $update->save();
                    };
                }

                if (!empty($data->respirasi)) {
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
                            "value" => intval($data->respirasi),
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
                            // dd($test, 'pernafasan');
                        }

                        $message = "Gagal kirim vital sign Pernafasan pasien Ranap " . $noRawat;

                        Session::flash('error', $message);
                    }

                    // dd($response);

                    $dataResponse = json_decode($response->getBody());

                    // dd($data);

                    if (!empty($dataResponse->id)) {
                        $update = ResponseRanapSatuSehat::where('noRawat', $noRawat)->first();
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
                            // dd($test, 'sistol');
                        }

                        $message = "Gagal kirim vital sign Sistol pasien Ranap " . $noRawat;

                        Session::flash('error', $message);
                    }

                    $dataResponse = json_decode($response->getBody());

                    // dd($data);

                    if (!empty($dataResponse->id)) {
                        $update = ResponseRanapSatuSehat::where('noRawat', $noRawat)->first();
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
                            // dd($test, 'diastol');
                        }

                        $message = "Gagal kirim vital sign Diastol pasien Ranap " . $noRawat;

                        Session::flash('error', $message);
                    }

                    // dd($response);

                    $dataResponse = json_decode($response->getBody());

                    // dd($data);

                    if (!empty($dataResponse->id)) {
                        $update = ResponseRanapSatuSehat::where('noRawat', $noRawat)->first();
                        $update->asesmen_diastol = $dataResponse->id;
                        $update->save();
                    };
                }

                if (!empty($data->suhu_tubuh)) {
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
                            "value" => floatval($data->suhu_tubuh),
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
                            // dd($test, 'suhu');
                        }

                        $message = "Gagal kirim vital sign Suhu pasien Ranap " . $noRawat . " suhu " . floatval($data->suhu_tubuh);

                        Session::flash('error', $message);
                    }

                    // dd($response);

                    $dataResponse = json_decode($response->getBody());

                    // dd($data);

                    if (!empty($dataResponse->id)) {
                        $update = ResponseRanapSatuSehat::where('noRawat', $noRawat)->first();
                        $update->asesmen_suhu = $dataResponse->id;
                        $update->save();
                    };
                }
            }
        }
    }

    public function sendDiagnosa($noRawat, $encounter)
    {
        //Primary Diagnosa
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            ->select(
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'kamar_inap.no_rawat',
                'kamar_inap.tgl_keluar',
                'kamar_inap.jam_keluar',
                'diagnosa_pasien.kd_penyakit',
                'diagnosa_pasien.status',
                'diagnosa_pasien.prioritas',
                'penyakit.nm_penyakit'
            )
            ->where('kamar_inap.no_rawat', $noRawat)
            ->where('diagnosa_pasien.prioritas', '1')
            ->first();

        if (!empty($data)) {
            $idPasien = SatuSehatController::patientSehat($data->ktp_pasien);
            $waktuPerawatan = "$data->tgl_keluar $data->jam_keluar";
            $formatWaktu = Carbon::parse($waktuPerawatan)->setTimezone('UTC')->toW3cString();

            if (!empty($data->kd_penyakit)) {
                $dataPrimaryDiagnosa = [
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
                                "code" => "$data->kd_penyakit",
                                "display" => "$data->nm_penyakit"
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
                    "onsetDateTime" => "$formatWaktu",
                    "recordedDate" => "$formatWaktu"
                    // ,
                    // "note" => [
                    //     [
                    //         "text" => "Pasien mengalami Gagal Ginjal Stage 5"
                    //     ]
                    // ]
                ];
                //Send data
                SatuSehatController::getTokenSehat();
                $access_token = Session::get('tokenSatuSehat');
                // dd($access_token);
                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                try {
                    $response = $client->request('POST', 'fhir-r4/v1/Condition', [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}"
                        ],
                        'json' => $dataPrimaryDiagnosa
                    ]);
                } catch (ClientException $e) {
                    // echo $e->getRequest();
                    // echo $e->getResponse();
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();

                        // dd($response);
                        $test = json_decode($response->getBody());
                        // dd($test, $dataPrimaryDiagnosa);
                    }

                    $message = "Gagal kirim primari diagnosa pasien Ranap " . $noRawat;

                    Session::flash('error', $message);
                }

                // dd($response);

                $dataResponse = json_decode($response->getBody());

                // dd($data);

                if (!empty($dataResponse->id)) {
                    $update = ResponseRanapSatuSehat::where('noRawat', $noRawat)->first();
                    $update->diagnosis_primer = $dataResponse->id;
                    $update->save();
                };
            }

            //Secondary Diagnosa
            $data = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->leftJoin('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
                ->leftJoin('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
                ->select(
                    'pasien.nm_pasien',
                    'pasien.no_ktp as ktp_pasien',
                    'kamar_inap.no_rawat',
                    'kamar_inap.tgl_keluar',
                    'kamar_inap.jam_keluar',
                    'diagnosa_pasien.kd_penyakit',
                    'diagnosa_pasien.status',
                    'diagnosa_pasien.prioritas',
                    'penyakit.nm_penyakit'
                )
                ->where('kamar_inap.no_rawat', $noRawat)
                ->where('diagnosa_pasien.prioritas', '2')
                ->first();

            if (!empty($data)) {
                $idPasien = SatuSehatController::patientSehat($data->ktp_pasien);
                $waktuPerawatan = "$data->tgl_keluar $data->jam_keluar";
                $formatWaktu = Carbon::parse($waktuPerawatan)->setTimezone('UTC')->toW3cString();

                if (!empty($data->kd_penyakit)) {
                    $dataSecondaryDiagnosa = [
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
                                    "code" => "$data->kd_penyakit",
                                    "display" => "$data->nm_penyakit"
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
                        "onsetDateTime" => "$formatWaktu",
                        "recordedDate" => "$formatWaktu"
                        // ,
                        // "note" => [
                        //     [
                        //         "text" => "Pasien mengalami Gagal Ginjal Stage 5"
                        //     ]
                        // ]
                    ];
                    //Send data
                    SatuSehatController::getTokenSehat();
                    $access_token = Session::get('tokenSatuSehat');
                    // dd($access_token);
                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                    try {
                        $response = $client->request('POST', 'fhir-r4/v1/Condition', [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ],
                            'json' => $dataSecondaryDiagnosa
                        ]);
                    } catch (ClientException $e) {
                        // echo $e->getRequest();
                        // echo $e->getResponse();
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            // dd($response);
                            $test = json_decode($response->getBody());
                            // dd($test, $dataSecondaryDiagnosa);
                        }

                        $message = "Gagal kirim sekundary diagnosa pasien Ranap " . $noRawat;

                        Session::flash('error', $message);
                    }

                    // dd($response);

                    $dataResponse = json_decode($response->getBody());

                    // dd($data);

                    if (!empty($dataResponse->id)) {
                        $update = ResponseRanapSatuSehat::where('noRawat', $noRawat)->first();
                        $update->diagnosis_sekunder = $dataResponse->id;
                        $update->save();
                    };
                }
            }
        } else {
            //Primary Diagnosa pakai cara 2
            $data = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->leftJoin('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
                ->leftJoin('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
                ->select(
                    'pasien.nm_pasien',
                    'pasien.no_ktp as ktp_pasien',
                    'kamar_inap.no_rawat',
                    'kamar_inap.tgl_keluar',
                    'kamar_inap.jam_keluar',
                    'diagnosa_pasien.kd_penyakit',
                    'diagnosa_pasien.status',
                    'diagnosa_pasien.prioritas',
                    'penyakit.nm_penyakit'
                )
                ->where('kamar_inap.no_rawat', $noRawat)
                ->where('diagnosa_pasien.status', 'Ranap')
                ->orderBy('diagnosa_pasien.prioritas', 'ASC')
                ->get();

            if (!empty($data)) {
                foreach ($data as $index => $listData) {
                    $idPasien = SatuSehatController::patientSehat($listData->ktp_pasien);
                    $waktuPerawatan = "$listData->tgl_keluar $listData->jam_keluar";
                    $formatWaktu = Carbon::parse($waktuPerawatan)->setTimezone('UTC')->toW3cString();

                    if ($index == 0) {
                        $dataPrimaryDiagnosa = [
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
                                        "code" => "$listData->kd_penyakit",
                                        "display" => "$listData->nm_penyakit"
                                    ]
                                ]
                            ],
                            "subject" => [
                                "reference" => "Patient/$idPasien",
                                "display" => "$listData->nm_pasien"
                            ],
                            "encounter" => [
                                "reference" => "Encounter/$encounter"
                            ],
                            "onsetDateTime" => "$formatWaktu",
                            "recordedDate" => "$formatWaktu"
                            // ,
                            // "note" => [
                            //     [
                            //         "text" => "Pasien mengalami Gagal Ginjal Stage 5"
                            //     ]
                            // ]
                        ];
                        //Send data
                        SatuSehatController::getTokenSehat();
                        $access_token = Session::get('tokenSatuSehat');
                        // dd($access_token);
                        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                        try {
                            $response = $client->request('POST', 'fhir-r4/v1/Condition', [
                                'headers' => [
                                    'Authorization' => "Bearer {$access_token}"
                                ],
                                'json' => $dataPrimaryDiagnosa
                            ]);
                        } catch (ClientException $e) {
                            // echo $e->getRequest();
                            // echo $e->getResponse();
                            if ($e->hasResponse()) {
                                $response = $e->getResponse();

                                // dd($response);
                                $test = json_decode($response->getBody());
                                // dd($test, $dataPrimaryDiagnosa);
                            }

                            $message = "Gagal kirim primari diagnosa metode 2 pasien Ranap " . $noRawat;

                            Session::flash('error', $message);
                        }

                        // dd($response);

                        $dataResponse = json_decode($response->getBody());

                        // dd($data);

                        if (!empty($dataResponse->id)) {
                            $update = ResponseRanapSatuSehat::where('noRawat', $noRawat)->first();
                            $update->diagnosis_primer = $dataResponse->id;
                            $update->save();
                        };
                    } elseif ($index == 1) {

                        $dataSecondaryDiagnosa = [
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
                                        "code" => "$listData->kd_penyakit",
                                        "display" => "$listData->nm_penyakit"
                                    ]
                                ]
                            ],
                            "subject" => [
                                "reference" => "Patient/$idPasien",
                                "display" => "$listData->nm_pasien"
                            ],
                            "encounter" => [
                                "reference" => "Encounter/$encounter"
                            ],
                            "onsetDateTime" => "$formatWaktu",
                            "recordedDate" => "$formatWaktu"
                            // ,
                            // "note" => [
                            //     [
                            //         "text" => "Pasien mengalami Gagal Ginjal Stage 5"
                            //     ]
                            // ]
                        ];
                        //Send data
                        SatuSehatController::getTokenSehat();
                        $access_token = Session::get('tokenSatuSehat');
                        // dd($access_token);
                        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                        try {
                            $response = $client->request('POST', 'fhir-r4/v1/Condition', [
                                'headers' => [
                                    'Authorization' => "Bearer {$access_token}"
                                ],
                                'json' => $dataSecondaryDiagnosa
                            ]);
                        } catch (ClientException $e) {
                            // echo $e->getRequest();
                            // echo $e->getResponse();
                            if ($e->hasResponse()) {
                                $response = $e->getResponse();

                                // dd($response);
                                $test = json_decode($response->getBody());
                                // dd($test, $dataSecondaryDiagnosa);
                            }

                            $message = "Gagal kirim sekundary diagnosa metode 2 pasien Ranap " . $noRawat;

                            Session::flash('error', $message);
                        }

                        // dd($response);

                        $dataResponse = json_decode($response->getBody());

                        // dd($data);

                        if (!empty($dataResponse->id)) {
                            $update = ResponseRanapSatuSehat::where('noRawat', $noRawat)->first();
                            $update->diagnosis_sekunder = $dataResponse->id;
                            $update->save();
                        };
                    }
                }
            }
        }
    }

    public function sendUpdateKepulangan($noRawat, $encounter)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dpjp_ranap', 'dpjp_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('pegawai', 'pegawai.nik', '=', 'dpjp_ranap.kd_dokter')
            ->leftJoin('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            // ->leftJoin('bangsal', 'bangsal.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select(
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pegawai.nama as nm_dokter',
                'pegawai.no_ktp as ktp_dokter',
                'kamar_inap.no_rawat',
                'kamar_inap.kd_kamar',
                'kamar_inap.tgl_keluar',
                'kamar_inap.jam_keluar',
                'kamar_inap.stts_pulang',
                'diagnosa_pasien.kd_penyakit',
                'diagnosa_pasien.status',
                'diagnosa_pasien.prioritas',
                'penyakit.nm_penyakit'

            )
            ->selectRaw(DB::raw("CONCAT(kamar_inap.tgl_keluar, ' ',kamar_inap.jam_keluar) AS waktu_keluar"))
            ->where('kamar_inap.no_rawat', $noRawat)
            // ->where('diagnosa_pasien.prioritas', '1')
            ->orderBy('waktu_keluar', 'DESC')
            ->first();

        // if ($noRawat == '2024/03/04/000388') {
        //     dd($data);
        // }

        $idRS = env('IDRS');

        if ((!empty($data->ktp_pasien)) && (!empty($data->ktp_dokter)) && ($data->tgl_keluar != '0000-00-00')) {
            // if ($data->no_rawat == '2024/03/04/000385') {
            //     dd($data);
            // }

            $idPasien = SatuSehatController::patientSehat($data->ktp_pasien);
            $idPractition = SatuSehatController::practitioner($data->ktp_dokter);
            $idKamar = RanapSehatController::getIdKamar($data->kd_kamar);
            if (!empty($idKamar)) {
                $pecahCode = explode(' ', $idKamar->kelas);
                $codeKelas = strtolower($pecahCode[1]);
            }
            $waktuMulai = "$data->tgl_registrasi $data->jam_reg";
            $formatWaktuMulai = Carbon::parse($waktuMulai)->setTimezone('UTC')->toW3cString();
            $formatWaktuSelesai = Carbon::parse($data->waktu_keluar)->setTimezone('UTC')->toW3cString();
            if ($formatWaktuSelesai > $formatWaktuMulai) {
                $formatWaktuSelesai = Carbon::parse($data->waktu_keluar)->addHour()->setTimezone('UTC')->toW3cString();
            }
            $idRequest = RanapSehatController::getRequestFromIgd($data->no_rawat);
            $idDiagnosa = RanapSehatController::getPrimaryDiagnosisRanap($data->no_rawat);

            if ($data->stts_pulang == 'Sembuh') {
                $dataKepulangan = [
                    "system" => "http://snomed.info/sct",
                    "code" => "359746009",
                    "display" => "Patient's condition stable"
                ];
            } else if ($data->stts_pulang == 'Membaik' || $data->stts_pulang == 'Atas Persetujuan Dokter' || $data->stts_pulang == 'Lain-lain') {
                $dataKepulangan = [
                    "system" => "http://snomed.info/sct",
                    "code" => "268910001",
                    "display" => "Patient's condition improved"
                ];
            } else if ($data->stts_pulang == 'Atas Permintaan Sendiri' || $data->stts_pulang == 'Pulang Paksa' || $data->stts_pulang == 'APS' || $data->stts_pulang == 'Meninggal' || $data->stts_pulang == 'Rujuk') {
                $dataKepulangan = [
                    "system" => "http://snomed.info/sct",
                    "code" => "162668006",
                    "display" => "Patient's condition unstable"
                ];
                // } else if ($data->stts_pulang == 'Atas Persetujuan Dokter') {
                //     $dataKepulangan = [
                //         "system" => "http://terminology.hl7.org/CodeSystem/discharge-disposition",
                //         "code" => "home",
                //         "display" => "home"
                //     ];
                // } else if ($data->stts_pulang == 'Lain-lain') {
                //     $dataKepulangan = [
                //         "system" => "http://terminology.hl7.org/CodeSystem/discharge-disposition",
                //         "code" => "oth",
                //         "display" => "Other"
                //     ];
            }

            if ($data->stts_pulang == 'Atas Permintaan Sendiri' || $data->stts_pulang == 'Pulang Paksa' || $data->stts_pulang == 'APS') {
                $statusKepulangan = [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/discharge-disposition",
                            "code" => "aadvice",
                            "display" => "Left against advice"
                        ]
                    ],
                    "text" => "Pasien pulang atas permintaan sendiri atau tidak sesuai dengan saran medis"
                ];
            } else if ($data->stts_pulang == 'Atas Persetujuan Dokter' || $data->stts_pulang == 'Sembuh' || $data->stts_pulang == 'Membaik') {
                $statusKepulangan = [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/discharge-disposition",
                            "code" => "home",
                            "display" => "Home"
                        ]
                    ],
                    "text" => "Anjuran dokter untuk pulang dan kontrol kembali"
                ];
            } else if ($data->stts_pulang == 'Lain-lain') {
                $statusKepulangan = [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/discharge-disposition",
                            "code" => "oth",
                            "display" => "Other"
                        ]
                    ],
                    "text" => "Kepulangan belum terdefinisi di tempat lain"
                ];
            } else if ($data->stts_pulang == 'Meninggal') {
                $statusKepulangan = [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/discharge-disposition",
                            "code" => "exp",
                            "display" => "Expired"
                        ]
                    ],
                    "text" => "Pasien meninggal saat kunjungan terjadi"
                ];
            } else if ($data->stts_pulang == 'Rujuk') {
                $statusKepulangan = [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/discharge-disposition",
                            "code" => "other-hcf",
                            "display" => "Other healthcare facility"
                        ]
                    ],
                    "text" => "Pasien dirujuk ke fasilitas pelayanan kesehatan lainnya"
                ];
            }

            if ((!empty($dataKepulangan)) && (!empty($idKamar->id_ihs)) && (!empty($idDiagnosa))) {
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

                    $message = "Gagal kirim kondisi Meninggalkan pasien Ranap " . $noRawat;

                    Session::flash('error', $message);
                }

                $dataResponse = json_decode($response->getBody());

                if (!empty($dataResponse->id)) {
                    $update = ResponseRanapSatuSehat::where('noRawat', $noRawat)->first();
                    $update->kondisi_stabil = $dataResponse->id;
                    $update->save();
                };

                $updateEncounter = [
                    "resourceType" => "Encounter",
                    "id" => "$encounter",
                    "identifier" => [
                        [
                            "system" => "http://sys-ids.kemkes.go.id/encounter/$idRS",
                            "value" => "$data->no_rawat"
                        ]
                    ],
                    "status" => "finished",
                    "class" => [
                        "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                        "code" => "IMP",
                        "display" => "inpatient encounter"
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
                                "display" => "$data->nm_dokter"
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
                                "reference" => "Location/$idKamar->id_ihs",
                                "display" => "$idKamar->kd_kamar $idKamar->nm_bangsal"
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
                                                        "system" => "http://terminology.kemkes.go.id/CodeSystem/locationServiceClass-Inpatient",
                                                        "code" => "$codeKelas",
                                                        "display" => "$idKamar->kelas"
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
                                "reference" => "Condition/$idDiagnosa",
                                "display" => "$data->nm_penyakit"
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
                            "status" => "in-progress",
                            "period" => [
                                "start" => "$formatWaktuMulai",
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
                    "hospitalization" => [
                        "dischargeDisposition" =>
                        $statusKepulangan
                    ],
                    "serviceProvider" => [
                        "reference" => "Organization/$idRS"
                    ],
                    "basedOn" => [
                        [
                            "reference" => "ServiceRequest/$idRequest"
                        ]
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
                        dd($test, 'status update encounter Meninggalkan Ranap ', $updateEncounter);
                    }

                    $message = "Gagal update encounter pasien Ranap " . $noRawat;

                    Session::flash('error', $message);
                }
            }
        }
    }

    public function copySalahKeIgd($noRawat)
    {
        $dataIgd = ResponseIgdSatuSehat::where('noRawat', $noRawat)->first();
        $dataRanap = ResponseRanapSatuSehat::where('noRawat', $noRawat)->first();
        $dataRanap->asesmen_nadi = $dataIgd->asesmen_nadi;
        $dataRanap->asesmen_sistol = $dataIgd->asesmen_sistol;
        $dataRanap->asesmen_diastol = $dataIgd->asesmen_diastol;
        $dataRanap->asesmen_pernapasan = $dataIgd->asesmen_pernapasan;
        $dataRanap->asesmen_suhu = $dataIgd->asesmen_suhu;
        $dataRanap->save();
    }
}
