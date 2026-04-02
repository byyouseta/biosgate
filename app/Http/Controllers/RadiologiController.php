<?php

namespace App\Http\Controllers;

use App\LogErrorSatuSehat;
use App\ResponseIgdSatuSehat;
use App\ResponseRadiologiSatuSehat;
use App\ResponseRanapSatuSehat;
use App\ResponseSatuSehat;
use Carbon\Carbon;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
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
        session()->put('anak', 'Radiologi');
        session()->put('cucu', 'Send Radiologi');
        set_time_limit(0);

        if (empty($request->get('tanggal'))) {
            $pasien_tanggal = Carbon::now()->format('Y-m-d');
            $kemarin = Carbon::yesterday()->format('Y-m-d');
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
            $pasien_tanggal = Carbon::parse($tanggal)->format('Y-m-d');
        }

        $idRS = env('IDRS');

        $dataPengunjung = DB::connection('mysqlkhanza')->table('permintaan_radiologi')
            ->join('pegawai', 'pegawai.nik', '=', 'permintaan_radiologi.dokter_perujuk')
            ->join('radiologi_ascension', 'radiologi_ascension.noorder', '=', 'permintaan_radiologi.noorder')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_radiologi.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('permintaan_pemeriksaan_radiologi', 'permintaan_pemeriksaan_radiologi.noorder', '=', 'permintaan_radiologi.noorder')
            ->leftJoin('jns_perawatan_radiologi', 'jns_perawatan_radiologi.kd_jenis_prw', '=', 'permintaan_pemeriksaan_radiologi.kd_jenis_prw')
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
                'permintaan_radiologi.status',
                'jns_perawatan_radiologi.kd_jenis_prw',
                'jns_perawatan_radiologi.nm_perawatan',
                'radiologi_ascension.ascension'
            )
            // ->where('reg_periksa.status_lanjut', 'Ralan')
            ->where('permintaan_radiologi.tgl_permintaan', $pasien_tanggal)
            ->get();

        foreach ($dataPengunjung as $pasienRadio) {
            $dataSehat = ResponseRadiologiSatuSehat::where('noRawat', $pasienRadio->no_rawat)
                ->where('no_order', $pasienRadio->noorder)
                ->first();
            $mapping = RadiologiController::getMapping($pasienRadio->noorder);

            if ($dataSehat && !empty($mapping)) {
                // $dataSehat = $checkService->first();

                if (($dataSehat->service_request_id != null) && ($dataSehat->imaging_study_id == null)) {
                    RadiologiController::getImagingStudy($pasienRadio->ascension);
                }
                if (($dataSehat->imaging_study_id != null) && ($dataSehat->observation_id == null)) {
                    RadiologiController::sendObservation($pasienRadio, $mapping);
                }
                if (($dataSehat->imaging_study_id != null) && ($dataSehat->observation_id != null) && ($dataSehat->diagnostic_report_id == null)) {
                    RadiologiController::sendDiagnosticReport($pasienRadio, $mapping);
                }
                sleep(1);
            }

            //Ceking Service Request
            $checkPacs = RadiologiController::checkPacs($pasienRadio->ascension);
            if ($pasienRadio->status == 'ralan') {
                $dataEncounter = SatuSehatController::getEncounterId($pasienRadio->no_rawat);
                if (empty($dataEncounter)) {
                    $dataEncounter = ResponseIgdSatuSehat::where('noRawat', $pasienRadio->no_rawat)->first();
                }
            } elseif ($pasienRadio->status == 'ranap') {
                $dataEncounter = ResponseRanapSatuSehat::where('noRawat', $pasienRadio->no_rawat)->first();
            } else {
                $dataEncounter = null;
            }
            $idPasien = SatuSehatController::patientSehat($pasienRadio->ktp_pasien);

            if (!empty($dataEncounter) && !empty($dataEncounter->encounter_id)) {
                $idEncounter = $dataEncounter->encounter_id;

                if (empty($dataSehat)) {
                    //Simpan Encounter
                    $simpan = new ResponseRadiologiSatuSehat();
                    $simpan->noRawat = $pasienRadio->no_rawat;
                    $simpan->tgl_registrasi = $pasienRadio->tgl_registrasi;
                    $simpan->no_order = $pasienRadio->noorder;
                    $simpan->accession_no = $pasienRadio->ascension;
                    $simpan->encounter_id = $idEncounter;
                    $simpan->save();
                }
            } else {
                $idEncounter = null;
            }

            if (!empty($idEncounter) && (!empty($checkPacs)) && (!empty($mapping)) && (!empty($idPasien)) && (!empty($dataSehat)) && (empty($dataSehat->service_request_id))) {
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
                                "code" => "$mapping->code", #service request
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

                //Send data
                $access_token = SatuSehatController::getTokenSehat();
                $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                try {
                    $response = $client->request('POST', 'fhir-r4/v1/ServiceRequest', [
                        'headers' => [
                            'Authorization' => "Bearer {$access_token}"
                        ],
                        'json' => $dataService
                    ]);
                } catch (ClientException $e) {
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();

                        $test = json_decode($response->getBody(true));
                        $message = $test->issue[0]->details->text;

                        if ($test && $test->issue[0]->code == 'duplicate') {
                            try {
                                $response = $client->request('GET', 'fhir-r4/v1/ServiceRequest?encounter=' . $idEncounter, [
                                    'headers' => [
                                        'Authorization' => "Bearer {$access_token}"
                                    ]
                                ]);
                            } catch (ClientException $e) {
                                if ($e->hasResponse()) {
                                    $response = $e->getResponse();
                                }
                                $test = json_decode($response->getBody(true));
                                $message = $test->issue[0]->details->text;

                                dd($message, $test->issue[0], $idEncounter, 'error service request radiologi on duplicate');
                            }

                            $dataResponse = json_decode($response->getBody()->getContents());
                            if ($dataResponse && $dataResponse->entry[0]->resource->id) {
                                $simpan = ResponseRadiologiSatuSehat::where('noRawat', $pasienRadio->no_rawat)
                                    ->where('no_order', $pasienRadio->noorder)
                                    ->first();
                                if (!$simpan) {
                                    $simpan = new ResponseRadiologiSatuSehat();
                                    $simpan->noRawat = $pasienRadio->no_rawat;
                                    $simpan->tgl_registrasi = $pasienRadio->tgl_registrasi;
                                    $simpan->no_order = $pasienRadio->noorder;
                                    $simpan->accession_no = $pasienRadio->ascension;
                                    $simpan->encounter_id = $idEncounter;
                                }
                                $simpan->service_request_id = $dataResponse->entry[0]->resource->id;
                                $simpan->save();
                            }
                        }
                    } else {
                        $message = "Error tidak ada response";
                    }

                    LogErrorSatuSehat::create([
                        'subject' => 'Kirim Service Request Radiologi',
                        'keterangan' => "Pengiriman data Service Request Radiologi pasien no rawat : $pasienRadio->no_rawat, pesan : (" . $message . ")"
                    ]);

                    goto KirimPasienLain;
                }

                $dataResponse = json_decode($response->getBody());

                if ($dataResponse && $dataResponse->id) {
                    $simpan = ResponseRadiologiSatuSehat::where('noRawat', $pasienRadio->no_rawat)
                        ->where('no_order', $pasienRadio->noorder)
                        ->first();
                    if (!$simpan) {
                        $simpan = new ResponseRadiologiSatuSehat();
                        $simpan->noRawat = $pasienRadio->no_rawat;
                        $simpan->tgl_registrasi = $pasienRadio->tgl_registrasi;
                        $simpan->no_order = $pasienRadio->noorder;
                        $simpan->accession_no = $pasienRadio->ascension;
                        $simpan->encounter_id = $idEncounter;
                    }
                    $simpan->service_request_id = $dataResponse->id;
                    $simpan->save();
                }
            }

            KirimPasienLain:
        }

        $dataNoOrder = $dataPengunjung->pluck('noorder')->unique();

        $dataLog = ResponseRadiologiSatuSehat::whereIn('no_order', $dataNoOrder)
            ->get();
        // ->keyBy('noRawat');

        $dataError = LogErrorSatuSehat::where('subject', 'like', '%Radiologi%')
            ->where(function ($query) use ($pasien_tanggal) {
                $query->whereDate('created_at', $pasien_tanggal)
                    ->orWhereDate('created_at', Carbon::today());
            })
            ->orderBy('created_at', 'DESC')
            ->limit(100)
            ->get();

        return view('satu_sehat.client_radiologi', compact('dataLog', 'dataPengunjung', 'dataError'));
    }

    public function summary(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Radiologi');
        session()->put('cucu', 'Summary Radiologi');
        set_time_limit(0);

        if (empty($request->get('tanggal_awal'))) {
            $tanggal_awal = Carbon::now();
            $tanggal_akhir = Carbon::now();
        } else {
            $tanggal_awal = new Carbon($request->get('tanggal_awal'));
            $tanggal_akhir = new Carbon($request->get('tanggal_akhir'));
        }

        $idRS = env('IDRS');

        $dataPengunjung = DB::connection('mysqlkhanza')->table('permintaan_radiologi')
            ->join('pegawai', 'pegawai.nik', '=', 'permintaan_radiologi.dokter_perujuk')
            ->join('radiologi_ascension', 'radiologi_ascension.noorder', '=', 'permintaan_radiologi.noorder')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_radiologi.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('permintaan_pemeriksaan_radiologi', 'permintaan_pemeriksaan_radiologi.noorder', '=', 'permintaan_radiologi.noorder')
            ->leftJoin('jns_perawatan_radiologi', 'jns_perawatan_radiologi.kd_jenis_prw', '=', 'permintaan_pemeriksaan_radiologi.kd_jenis_prw')
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
                'permintaan_radiologi.status',
                'jns_perawatan_radiologi.kd_jenis_prw',
                'jns_perawatan_radiologi.nm_perawatan',
                'radiologi_ascension.ascension'
            )
            ->whereBetween('permintaan_radiologi.tgl_permintaan', [$tanggal_awal, $tanggal_akhir])
            ->groupBy('noorder')
            ->get();

        $dataNoOrder = $dataPengunjung->pluck('noorder')->unique();
        $dataLog = ResponseRadiologiSatuSehat::whereIn('no_order', $dataNoOrder)
            ->get()
            ->keyBy('no_order');

        $ktpList = $dataPengunjung->pluck('ktp_pasien')->unique();
        $idSehatMap = \App\PasienSehat::whereIn('nik', $ktpList)->pluck('satu_sehat_id', 'nik');

        foreach ($dataPengunjung as $list) {
            $list->idSehat = $idSehatMap[$list->ktp_pasien] ?? null;
            $list->dataResponse = $dataLog[$list->noorder] ?? null;
        }

        return view('satu_sehat.summary_radiologi', compact('dataLog', 'dataPengunjung'));
    }

    public static function checkPacs($accession)
    {
        $urlPacs = env('URL_APIPACS');
        $client = new \GuzzleHttp\Client(['base_uri' => "$urlPacs/freedomorderapi/api/v1/"]);
        try {
            $response = $client->request('GET', "order?accessionNo=$accession");
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                $test = json_decode($response->getBody());
                // dd($test, 'ambil data response pacs');
            }

            $message = "Error ambil status data PACS";

            Session::flash('error', $message);

            return;
        }

        $responseReport = json_decode($response->getBody());
        // $responseReport = (object) $responseReport;

        if ($responseReport->StudyDetails[0]->StudyInstanceUid != null) {
            return $responseReport->StudyDetails[0]->StudyInstanceUid;
        } else {
            return null;
        }
    }

    public function getImagingStudy($accessionNo)
    {
        $ping = SatuSehatController::pingSatuSehat();
        if ($ping != TRUE) {
            $message = "Gagal koneksi ke server satu sehat";

            LogErrorSatuSehat::create([
                'subject' => 'Get Imaging Study Radiologi',
                'keterangan' => "Pengambilan data Imaging Study Radiologi accession No : $accessionNo, pesan : (" . $message . ")"
            ]);

            return null;
        }
        $idRS = env('IDRS');
        //Send data
        $access_token = SatuSehatController::getTokenSehat();

        $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
        try {
            $response = $client->request('GET', "fhir-r4/v1/ImagingStudy?identifier=http://sys-ids.kemkes.go.id/acsn/$idRS|$accessionNo", [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ]
            ]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());
            }

            $message = "Gagal get Image Study accession " . $accessionNo;

            logErrorSatuSehat::create([
                'subject' => 'Get Imaging Study Radiologi',
                'keterangan' => "Pengambilan data Imaging Study Radiologi accession No : $accessionNo, pesan : (" . $message . ")"
            ]);
        } catch (RequestException $e) {

            if ($e->hasResponse()) {

                $statusCode = $e->getResponse()->getStatusCode();
                $responseBody = $e->getResponse()->getBody()->getContents();

                if ($statusCode == 429) {
                    // RATE LIMIT
                    LogErrorSatuSehat::create([
                        'subject' => 'SatuSehat Rate Limit',
                        'keterangan' => "Response Body: $responseBody"
                    ]);

                    return 'RATE_LIMIT';
                }

                LogErrorSatuSehat::create([
                    'subject' => 'SatuSehat Error',
                    'keterangan' => "Status Code: $statusCode, Response Body: $responseBody"
                ]);
            }

            return 'ERROR';
        }

        $dataResponse = json_decode($response->getBody());
        if (!empty($dataResponse->entry[0]->resource)) {
            $dataEkstrak = $dataResponse->entry[0]->resource;
            if (!empty($dataEkstrak->id)) {
                $update = ResponseRadiologiSatuSehat::where('accession_no', $accessionNo)->first();
                $update->imaging_study_id = $dataEkstrak->id;
                $update->save();
            }
        }
    }

    public function sendObservation($dataOrder, $mapping)
    {
        $idRS = env('IDRS');
        $dataLog = ResponseRadiologiSatuSehat::where('accession_no', $dataOrder->ascension)->first();
        $idPasien = SatuSehatController::patientSehat($dataOrder->ktp_pasien);
        if (!empty($dataOrder->ktp_dokter)) {
            $idPractition = SatuSehatController::practitioner($dataOrder->ktp_dokter);
        }
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
                        "code" => "$mapping->code", //observation
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
            ]
            // ,
            // "valueString" => "Tidak ditemukan kelainan dalam Upper CT Abdomen"
        ];

        $access_token = SatuSehatController::getTokenSehat();
        $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
        try {
            $response = $client->request('POST', 'fhir-r4/v1/Observation', [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ],
                'json' => $dataObservation
            ]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                $test = json_decode($response->getBody());
                dd($test, $dataObservation, 'sendObservation');
            }

            $message = "Gagal kirim Service Request pasien " . $dataOrder->no_rawat;

            Session::flash('error', $message);
        }

        $dataResponse = json_decode($response->getBody());

        if (!empty($dataResponse->id)) {
            $update = ResponseRadiologiSatuSehat::where('accession_no', $dataOrder->ascension)->first();
            $update->observation_id = $dataResponse->id;
            $update->save();
        } else {
            return null;
        }
    }

    public function sendDiagnosticReport($dataOrder, $mapping)
    {
        $idRS = env('IDRS');
        $dataLog = ResponseRadiologiSatuSehat::where('accession_no', $dataOrder->ascension)->first();
        $idPasien = SatuSehatController::patientSehat($dataOrder->ktp_pasien);
        if (!empty($dataOrder->ktp_dokter)) {
            $idPractition = SatuSehatController::practitioner($dataOrder->ktp_dokter);
        }
        if (empty($mapping)) {
            dd('mapping diagnostic report radiologi tidak ditemukan', $dataOrder);
        }
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
                        "code" => "$mapping->code", //report
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

        $access_token = SatuSehatController::getTokenSehat();
        $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
        try {
            $response = $client->request('POST', 'fhir-r4/v1/DiagnosticReport', [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ],
                'json' => $dataReport
            ]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                $test = json_decode($response->getBody());
                // dd($test, $dataReport, 'sendObservation');
                //Jika Duplicate
                if ($test && $test->issue[0]->code == 'duplicate') {
                    try {
                        $response = $client->request('GET', 'fhir-r4/v1/DiagnosticReport?encounter=' . $dataLog->encounter_id, [
                            'headers' => [
                                'Authorization' => "Bearer {$access_token}"
                            ]
                        ]);
                    } catch (ClientException $e) {
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();
                        }
                        $test = json_decode($response->getBody(true));
                        $message = $test->issue[0]->details->text;

                        dd($message, $test->issue[0], $dataLog->encounter_id, 'error diagnostic report radiologi on duplicate');
                    }

                    $dataResponse = json_decode($response->getBody()->getContents());
                    if ($dataResponse && $dataResponse->entry[0]->resource->id) {
                        $update = ResponseRadiologiSatuSehat::where('accession_no', $dataOrder->ascension)->first();
                        if ($update) {
                            $update->diagnostic_report_id = $dataResponse->entry[0]->resource->id;
                            $update->save();
                        }
                    }
                }
            }

            $message = "Gagal kirim Service Request pasien " . $dataOrder->no_rawat;

            Session::flash('error', $message);
        }

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
