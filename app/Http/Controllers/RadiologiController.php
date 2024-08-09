<?php

namespace App\Http\Controllers;

use App\LogErrorSatuSehat;
use App\ResponseRadiologiSatuSehat;
use Carbon\Carbon;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RadiologiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Rajal Satu Sehat');
        session()->put('cucu', 'API Radiologi');
        set_time_limit(0);

        if (empty($request->get('tanggal'))) {
            $pasien_tanggal = Carbon::now()->format('Y-m-d');
            $kemarin = Carbon::yesterday()->format('Y-m-d');

            $dataPengunjung = DB::connection('mysqlkhanza')->table('permintaan_radiologi')
                ->join('pegawai', 'pegawai.nik', '=', 'permintaan_radiologi.dokter_perujuk')
                ->join('radiologi_ascension', 'radiologi_ascension.noorder', '=', 'permintaan_radiologi.noorder')
                ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_radiologi.no_rawat')
                ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->select(
                    'reg_periksa.no_rkm_medis',
                    'reg_periksa.no_rawat',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.jam_reg',
                    'reg_periksa.status_lanjut',
                    'pasien.nm_pasien',
                    'pasien.no_ktp as ktp_pasien',
                    'pegawai.no_ktp as ktp_dokter',
                    'pegawai.nama as nama_dokter',
                    'permintaan_radiologi.noorder',
                    'permintaan_radiologi.jam_permintaan',
                    'permintaan_radiologi.tgl_permintaan',
                    'permintaan_radiologi.tgl_hasil',
                    'permintaan_radiologi.jam_hasil',
                    'radiologi_ascension.ascension'
                )
                ->where('reg_periksa.status_lanjut', 'Ralan')
                ->where('reg_periksa.stts', 'Sudah')
                ->where('permintaan_radiologi.tgl_hasil', '!=', '0000-00-00')
                // ->where('reg_periksa.tgl_registrasi', '2024-03-07')
                ->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
                ->orWhere('reg_periksa.tgl_registrasi', $kemarin)
                ->get();
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
            $pasien_tanggal = Carbon::parse($tanggal)->format('Y-m-d');

            $dataPengunjung = DB::connection('mysqlkhanza')->table('permintaan_radiologi')
                ->join('pegawai', 'pegawai.nik', '=', 'permintaan_radiologi.dokter_perujuk')
                ->join('radiologi_ascension', 'radiologi_ascension.noorder', '=', 'permintaan_radiologi.noorder')
                ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_radiologi.no_rawat')
                ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->select(
                    'reg_periksa.no_rkm_medis',
                    'reg_periksa.no_rawat',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.jam_reg',
                    'reg_periksa.status_lanjut',
                    'pasien.nm_pasien',
                    'pasien.no_ktp as ktp_pasien',
                    'pegawai.no_ktp as ktp_dokter',
                    'pegawai.nama as nama_dokter',
                    'permintaan_radiologi.noorder',
                    'permintaan_radiologi.jam_permintaan',
                    'permintaan_radiologi.tgl_permintaan',
                    'permintaan_radiologi.tgl_hasil',
                    'permintaan_radiologi.jam_hasil',
                    'radiologi_ascension.ascension'
                )
                ->where('reg_periksa.status_lanjut', 'Ralan')
                ->where('reg_periksa.stts', 'Sudah')
                ->where('permintaan_radiologi.tgl_hasil', '!=', '0000-00-00')
                // ->where('reg_periksa.tgl_registrasi', '2024-03-07')
                ->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
                ->get();
        }

        $idRS = env('IDRS');

        $dataPengunjung = DB::connection('mysqlkhanza')->table('permintaan_radiologi')
            ->join('pegawai', 'pegawai.nik', '=', 'permintaan_radiologi.dokter_perujuk')
            ->join('radiologi_ascension', 'radiologi_ascension.noorder', '=', 'permintaan_radiologi.noorder')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_radiologi.no_rawat')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'permintaan_radiologi.noorder',
                'permintaan_radiologi.jam_permintaan',
                'permintaan_radiologi.tgl_permintaan',
                'permintaan_radiologi.tgl_hasil',
                'permintaan_radiologi.jam_hasil',
                'radiologi_ascension.ascension'
            )
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->where('reg_periksa.stts', 'Sudah')
            ->where('permintaan_radiologi.tgl_hasil', '!=', '0000-00-00')
            // ->where('reg_periksa.tgl_registrasi', '2024-03-07')
            ->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
            // ->orWhere('reg_periksa.tgl_registrasi', $kemarin)
            ->get();

        // dd($dataPengunjung);

        foreach ($dataPengunjung as $pasienRadio) {
            $checkService = ResponseRadiologiSatuSehat::where('noRawat', $pasienRadio->no_rawat)->count();
            if ($checkService > 0) {
                $dataSehat = ResponseRadiologiSatuSehat::where('noRawat', $pasienRadio->no_rawat)->first();

                if (($dataSehat->service_request_id != null) && ($dataSehat->imaging_study_id == null)) {
                    // dd($dataSehat);
                    RadiologiController::getImagingStudy($pasienRadio->ascension);
                }
                if (($dataSehat->imaging_study_id != null) && ($dataSehat->observation_id == null)) {
                    RadiologiController::sendObservation($pasienRadio);
                }
                if (($dataSehat->imaging_study_id != null) && ($dataSehat->observation_id != null) && ($dataSehat->diagnostic_report_id == null)) {
                    RadiologiController::sendDiagnosticReport($pasienRadio);
                }

                goto KirimPasienLain;
            }

            $checkPacs = RadiologiController::checkPacs($pasienRadio->ascension);
            $dataEncounter = SatuSehatController::getEncounterId($pasienRadio->no_rawat);
            $idPasien = SatuSehatController::patientSehat($pasienRadio->ktp_pasien);

            if (!empty($dataEncounter)) {
                $idEncounter = $dataEncounter->encounter_id;
            }
            $mapping = RadiologiController::getMapping($pasienRadio->noorder);

            if (!empty($idEncounter) && (!empty($checkPacs)) && (!empty($mapping)) && (!empty($idPasien))) {
                if (!empty($pasienRadio->ktp_dokter)) {
                    $idPractition = SatuSehatController::practitioner($pasienRadio->ktp_dokter);
                }

                $waktuRequest = $pasienRadio->tgl_permintaan . ' ' . $pasienRadio->jam_permintaan;
                $waktu_request = new Carbon($waktuRequest);
                $formatWaktuRequest = $waktu_request->setTimezone('UTC')->toW3cString();

                $dataService = [
                    "resourceType" => "ServiceRequest",
                    "identifier" => [
                        [
                            "system" => "http://sys-ids.kemkes.go.id/servicerequest/$idRS",
                            "value" => "$pasienRadio->no_rawat"
                        ],
                        [
                            "use" => "usual",
                            "type" => [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/v2-0203",
                                        "code" => "ACSN"
                                    ]
                                ]
                            ],
                            "system" => "http://sys-ids.kemkes.go.id/acsn/$idRS",
                            "value" => "$pasienRadio->ascension"
                        ]
                    ],
                    "status" => "active",
                    "intent" => "original-order",
                    "priority" => "routine",
                    "category" => [
                        [
                            "coding" => [
                                [
                                    "system" => "http://snomed.info/sct",
                                    "code" => "363679005",
                                    "display" => "Imaging"
                                ]
                            ]
                        ]
                    ],
                    "code" => [
                        "coding" => [
                            [
                                "system" => "http://loinc.org",
                                "code" => "$mapping->code",
                                "display" => "$mapping->display"
                            ]
                        ],
                        "text" => "Pemeriksaan $mapping->nama_pemeriksaan"
                    ],
                    "subject" => [
                        "reference" => "Patient/$idPasien"
                    ],
                    "encounter" => [
                        "reference" => "Encounter/$idEncounter"
                    ],
                    "occurrenceDateTime" => "$formatWaktuRequest",
                    "authoredOn" => "$formatWaktuRequest",
                    "requester" => [
                        "reference" => "Practitioner/$idPractition",
                        "display" => "$pasienRadio->nama_dokter"
                    ],
                    "performer" => [
                        [
                            "reference" => "Practitioner/10002679509",
                            "display" => "dr. SRI SUMIYATI, Sp.Rad"
                        ]
                    ]
                    // ,
                    // "bodySite" => [
                    //     [
                    //         "coding" => [
                    //             [
                    //                 "system" => "http://snomed.info/sct",
                    //                 "code" => "80581009",
                    //                 "display" => "Upper abdomen structure"
                    //             ]
                    //         ]
                    //     ]
                    // ]
                    // ,
                    // "reasonCode" => [
                    //     [
                    //         "text" => "Periksa risiko adanya sumbatan batu empedu"
                    //     ]
                    // ]
                ];

                // dd($idEncounter, $pasienRadio->no_rawat, $dataService);

                //Send data
                SatuSehatController::getTokenSehat();
                $access_token = Session::get('tokenSatuSehat');
                // dd($access_token);
                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                try {
                    $response = $client->request('POST', 'fhir-r4/v1/ServiceRequest', [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}"
                        ],
                        'json' => $dataService
                    ]);
                } catch (ClientException $e) {
                    // echo $e->getRequest();
                    // echo $e->getResponse();
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();

                        // dd($response);
                        $test = json_decode($response->getBody(true));
                        dd($test, $dataEncounter, $dataService, 'sendEncounter');
                    }

                    $message = "Gagal kirim Service Request pasien " . $pasienRadio->no_rawat;

                    Session::flash('error', $message);

                    goto KirimPasienLain;
                }

                // dd($response);
                $dataResponse = json_decode($response->getBody());

                if (!empty($dataResponse->id)) {
                    $simpan = new ResponseRadiologiSatuSehat();
                    $simpan->noRawat = $pasienRadio->no_rawat;
                    $simpan->tgl_registrasi = $pasienRadio->tgl_registrasi;
                    $simpan->no_order = $pasienRadio->noorder;
                    $simpan->accession_no = $pasienRadio->ascension;
                    $simpan->encounter_id = $idEncounter;
                    $simpan->service_request_id = $dataResponse->id;
                    $simpan->save();
                }
            }

            KirimPasienLain:
        }
        // dd('done');

        $dataLog = ResponseRadiologiSatuSehat::whereDate('tgl_registrasi', $pasien_tanggal)->get();
        // dd($dataLog);

        return view('satu_sehat.client_radiologi', compact('dataLog'));
    }

    public static function checkPacs($accession)
    {
        $urlPacs = env('URL_APIPACS');
        $client = new \GuzzleHttp\Client(['base_uri' => "$urlPacs/freedomorderapi/api/v1/"]);
        try {
            $response = $client->request('GET', "order?accessionNo=$accession");
        } catch (BadResponseException $e) {
            // echo $e->getRequest();
            // echo $e->getResponse();
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                // dd($response);
                $test = json_decode($response->getBody());
                dd($test);
            }

            $message = "Error ambil status data PACS";

            Session::flash('error', $message);
        }

        $responseReport = json_decode($response->getBody());
        // $responseReport = (object) $responseReport;

        if ($responseReport->StudyDetails[0]->StudyInstanceUid != null) {
            // dd($responseReport->StudyDetails[0]->StudyInstanceUid);
            return $responseReport->StudyDetails[0]->StudyInstanceUid;
        } else {
            return null;
        }
    }

    public function getImagingStudy($accessionNo)
    {
        $idRS = env('IDRS');
        //Send data
        SatuSehatController::getTokenSehat();
        $access_token = Session::get('tokenSatuSehat');
        // dd($access_token);
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        try {
            $response = $client->request('GET', "fhir-r4/v1/ImagingStudy?identifier=http://sys-ids.kemkes.go.id/acsn/$idRS|$accessionNo", [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ]
            ]);
        } catch (ClientException $e) {
            // echo $e->getRequest();
            // echo $e->getResponse();
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                // dd($response);
                $test = json_decode($response->getBody());
                dd($test, $accessionNo, 'getImagingStudy');
            }

            $message = "Gagal get Image Study accession " . $accessionNo;

            Session::flash('error', $message);

            // goto KirimPasienLain;
        }

        $dataResponse = json_decode($response->getBody());
        // dd($dataResponse);
        if (!empty($dataResponse->entry[0]->resource)) {
            $dataEkstrak = $dataResponse->entry[0]->resource;
            // dd($dataEkstrak);

            if (!empty($dataEkstrak->id)) {
                $update = ResponseRadiologiSatuSehat::where('accession_no', $accessionNo)->first();
                // dd($update, $dataEkstrak);
                $update->imaging_study_id = $dataEkstrak->id;
                $update->save();
            }
        }
    }

    public function sendObservation($dataOrder)
    {
        $idRS = env('IDRS');
        $dataLog = ResponseRadiologiSatuSehat::where('accession_no', $dataOrder->ascension)->first();
        $idPasien = SatuSehatController::patientSehat($dataOrder->ktp_pasien);
        if (!empty($dataOrder->ktp_dokter)) {
            $idPractition = SatuSehatController::practitioner($dataOrder->ktp_dokter);
        }
        $mapping = RadiologiController::getMapping($dataOrder->noorder);
        $waktuRequest = $dataOrder->tgl_permintaan . ' ' . $dataOrder->jam_permintaan;
        $waktu_request = new Carbon($waktuRequest);
        $formatWaktuRequest = $waktu_request->setTimezone('UTC')->toW3cString();

        $dataObservation = [
            "resourceType" => "Observation",
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                    "value" => "$dataOrder->no_rawat"
                ]
            ],
            "status" => "final",
            "category" => [
                [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                            "code" => "imaging",
                            "display" => "Imaging"
                        ]
                    ]
                ]
            ],
            "code" => [
                "coding" => [
                    [
                        "system" => "http://loinc.org",
                        "code" => "$mapping->code",
                        "display" => "$mapping->display"
                    ]
                ]
            ],
            "subject" => [
                "reference" => "Patient/$idPasien"
            ],
            "encounter" => [
                "reference" => "Encounter/$dataLog->encounter_id"
            ],
            "effectiveDateTime" => "$formatWaktuRequest",
            "issued" => "$formatWaktuRequest",
            "performer" => [
                [
                    "reference" => "Practitioner/10002679509"
                ],
                [
                    "reference" => "Organization/$idRS"
                ]
            ],
            "basedOn" => [
                [
                    "reference" => "ServiceRequest/$dataLog->service_request_id"
                ]
            ],
            // "bodySite" => [
            //     "coding" => [
            //         [
            //             "system" => "http://snomed.info/sct",
            //             "code" => "80581009",
            //             "display" => "Upper abdomen structure"
            //         ]
            //     ]
            // ],
            "derivedFrom" => [
                [
                    "reference" => "ImagingStudy/$dataLog->imaging_study_id"
                ]
            ],
            "valueString" => "Tidak ditemukan kelainan dalam Upper CT Abdomen"
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
                'json' => $dataObservation
            ]);
        } catch (ClientException $e) {
            // echo $e->getRequest();
            // echo $e->getResponse();
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                // dd($response);
                $test = json_decode($response->getBody());
                dd($test, $dataObservation, 'sendObservation');
            }

            $message = "Gagal kirim Service Request pasien " . $dataOrder->no_rawat;

            Session::flash('error', $message);
        }

        // dd($response);
        $dataResponse = json_decode($response->getBody());

        if (!empty($dataResponse->id)) {
            $update = ResponseRadiologiSatuSehat::where('accession_no', $dataOrder->ascension)->first();
            $update->observation_id = $dataResponse->id;
            $update->save();
        } else {
            return null;
        }
    }

    public function sendDiagnosticReport($dataOrder)
    {
        $idRS = env('IDRS');
        $dataLog = ResponseRadiologiSatuSehat::where('accession_no', $dataOrder->ascension)->first();
        $idPasien = SatuSehatController::patientSehat($dataOrder->ktp_pasien);
        if (!empty($dataOrder->ktp_dokter)) {
            $idPractition = SatuSehatController::practitioner($dataOrder->ktp_dokter);
        }
        $mapping = RadiologiController::getMapping($dataOrder->noorder);
        $hasilDiagnosa = RadiologiController::getHasil($dataOrder->no_rawat);
        $waktuHasil = $dataOrder->tgl_hasil . ' ' . $dataOrder->jam_hasil;
        $waktu_hasil = new Carbon($waktuHasil);
        $formatWaktuHasil = $waktu_hasil->setTimezone('UTC')->toW3cString();

        $dataReport = [
            "resourceType" => "DiagnosticReport",
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/diagnostic/$idRS/rad",
                    "use" => "official",
                    "value" => "$dataOrder->no_rawat"
                ]
            ],
            "status" => "final",
            "category" => [
                [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/v2-0074",
                            "code" => "RAD",
                            "display" => "Radiology"
                        ]
                    ]
                ]
            ],
            "code" => [
                "coding" => [
                    [
                        "system" => "http://loinc.org",
                        "code" => "$mapping->code",
                        "display" => "$mapping->display"
                    ]
                ]
            ],
            "subject" => [
                "reference" => "Patient/$idPasien"
            ],
            "encounter" => [
                "reference" => "Encounter/$dataLog->encounter_id"
            ],
            "effectiveDateTime" => "$formatWaktuHasil",
            "issued" => "$formatWaktuHasil",
            "performer" => [
                [
                    "reference" => "Practitioner/10002679509"
                ],
                [
                    "reference" => "Organization/$idRS"
                ]
            ],
            "imagingStudy" => [
                [
                    "reference" => "ImagingStudy/$dataLog->imaging_study_id"
                ]
            ],
            "result" => [
                [
                    "reference" => "Observation/$dataLog->observation_id"
                ]
            ],
            "basedOn" => [
                [
                    "reference" => "ServiceRequest/$dataLog->service_request_id"
                ]
            ],
            "conclusion" => "$hasilDiagnosa->hasil"
        ];

        SatuSehatController::getTokenSehat();
        $access_token = Session::get('tokenSatuSehat');
        // dd($access_token);
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        try {
            $response = $client->request('POST', 'fhir-r4/v1/DiagnosticReport', [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ],
                'json' => $dataReport
            ]);
        } catch (ClientException $e) {
            // echo $e->getRequest();
            // echo $e->getResponse();
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                // dd($response);
                $test = json_decode($response->getBody());
                dd($test, $dataReport, 'sendObservation');
            }

            $message = "Gagal kirim Service Request pasien " . $dataOrder->no_rawat;

            Session::flash('error', $message);
        }

        // dd($response);
        $dataResponse = json_decode($response->getBody());

        if (!empty($dataResponse->id)) {
            $update = ResponseRadiologiSatuSehat::where('accession_no', $dataOrder->ascension)->first();
            $update->diagnostic_report_id = $dataResponse->id;
            $update->save();
        } else {
            return null;
        }
    }

    public function getMapping($dataOrder)
    {
        // dd($dataOrder);

        $data = DB::connection('mysqlkhanza')->table('fhir_rad')
            ->join('fhir_master_loinc_rad', 'fhir_master_loinc_rad.kd_loinc', '=', 'fhir_rad.kd_loinc')
            ->join('radiologi_ascension', 'radiologi_ascension.kd_jenis_prw', '=', 'fhir_rad.kd_jenis_prw')
            ->select(
                'fhir_master_loinc_rad.nama_pemeriksaan',
                'fhir_master_loinc_rad.code',
                'fhir_master_loinc_rad.display',
                'radiologi_ascension.ascension',
                'radiologi_ascension.noorder'
            )
            ->where('radiologi_ascension.noorder', $dataOrder)
            ->first();

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }

    public function getHasil($noRawat)
    {
        // dd($dataOrder);

        $data = DB::connection('mysqlkhanza')->table('hasil_radiologi')
            ->select(
                'hasil_radiologi.no_rawat',
                'hasil_radiologi.hasil'
            )
            ->where('hasil_radiologi.no_rawat', $noRawat)
            ->first();

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }
}
