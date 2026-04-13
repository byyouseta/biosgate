<?php

namespace App\Http\Controllers;

use App\LogErrorSatuSehat;
use App\PasienSehat;
use App\PraktisiSehat;
use App\ResponseIgdSatuSehat;
use App\ResponseMedicationSatuSehat;
use App\ResponseRanapSatuSehat;
use App\ResponseSatuSehat;
use Carbon\Carbon;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ObatSehatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Farmasi');
        session()->put('cucu', 'Summary Farmasi');

        if (empty($request->get('tanggal_awal'))) {
            $tanggal_awal = Carbon::now();
            $tanggal_akhir = Carbon::now();
        } else {
            $tanggal_awal = new Carbon($request->get('tanggal_awal'));
            $tanggal_akhir = new Carbon($request->get('tanggal_akhir'));

            // pastikan tanggal akhir tidak kurang dari awal
            if ($tanggal_akhir->lessThan($tanggal_awal)) {
                $tanggal_akhir = $tanggal_awal->copy();
            }

            // batasi maksimal 7 hari
            if ($tanggal_awal->diffInDays($tanggal_akhir) > 7) {
                return back()->with('error', 'Maksimal rentang tanggal 7 hari');
            }
        }

        $dataPengunjung = DB::connection('mysqlkhanza')->table('detail_pemberian_obat')
            ->join('resep_obat', function ($join) {
                $join->on('resep_obat.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
                    ->on('resep_obat.tgl_perawatan', '=', 'detail_pemberian_obat.tgl_perawatan')
                    ->on('resep_obat.jam', '=', 'detail_pemberian_obat.jam');
            })
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
            ->join('databarang', 'databarang.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
            ->join('golongan_barang', 'golongan_barang.kode', '=', 'databarang.kode_golongan')
            ->join('pegawai', 'pegawai.nik', '=', 'resep_obat.kd_dokter')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->leftJoin('fhir_farmasi', 'fhir_farmasi.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
            ->select(
                'resep_obat.*',
                'detail_pemberian_obat.kode_brng',
                'detail_pemberian_obat.status as status_obat',
                'databarang.nama_brng',
                'databarang.expire',
                'golongan_barang.kode as kode_golongan',
                'golongan_barang.nama as nama_golongan',
                'fhir_farmasi.id_ihs as kfa_code',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.status_bayar',
                'reg_periksa.kd_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'poliklinik.nm_poli',
                'pegawai.nama as nama_dokter',
                'pegawai.no_ktp as ktp_dokter'
            )
            ->whereBetween('detail_pemberian_obat.tgl_perawatan', [
                $tanggal_awal->format('Y-m-d'),
                $tanggal_akhir->format('Y-m-d')
            ])
            ->where('golongan_barang.nama', 'like', '%Obat%')
            ->get();

        $dataRawat = $dataPengunjung->pluck('no_rawat')->toArray();

        if (!empty($dataRawat)) {
            $dataEncounterRajal = ResponseSatuSehat::whereIn('noRawat', $dataRawat)
                ->get()
                ->keyBy('noRawat');
            $dataEncounterIgd = ResponseIgdSatuSehat::whereIn('noRawat', $dataRawat)
                ->get()
                ->keyBy('noRawat');
            $dataEncounterRanap = ResponseRanapSatuSehat::whereIn('noRawat', $dataRawat)
                ->get()
                ->keyBy('noRawat');
        }

        $ktpList = $dataPengunjung->pluck('ktp_pasien')->unique();
        $idSehatMap = \App\PasienSehat::whereIn('nik', $ktpList)->pluck('satu_sehat_id', 'nik');

        foreach ($dataPengunjung as $key => $list) {
            if ($list->status_obat == 'Ralan') {
                $isIgd = stripos($list->nm_poli, 'igd') !== false;

                if ($isIgd) {
                    $list->dataEncounter = $dataEncounterIgd[$list->no_rawat] ?? null;
                } else {
                    $list->dataEncounter = $dataEncounterRajal[$list->no_rawat] ?? null;
                }
            } elseif ($list->status_obat == 'Ranap') {
                $list->dataEncounter = $dataEncounterRanap[$list->no_rawat] ?? null;
            }
            $list->idSehat = $idSehatMap[$list->ktp_pasien] ?? null;
        }

        $sentKeys = ResponseMedicationSatuSehat::whereIn('noRawat', $dataRawat)
            ->pluck('noResep')
            ->flip();

        foreach ($dataPengunjung as $item) {

            $datetime = Carbon::parse($item->tgl_perawatan . ' ' . $item->jam)
                ->format('YmdHis');

            $key = $item->no_resep . '-' . $item->kode_brng . '-' . $datetime;

            $item->status_kirim = isset($sentKeys[$key]);
        }

        // dd($dataPengunjung->where('status_obat', 'Ranap'));

        return view('satu_sehat.summary_obat', compact('dataPengunjung'));
    }

    public function sendMedication($dataPemberianObat, $idPasien, $idDokter, $idLokasi)
    {
        $idRS = env('IDRS');
        //Resep Obat Racikan tabel resep_dokter_racikan
        // $listObatRacik = SatuSehatController::getListObatRacik($dataPemberianObat->no_resep);
        // $detailRacikan = SatuSehatController::getDetailRacikan($dataPemberianObat->no_resep);

        $timestamp_resep = Carbon::parse($dataPemberianObat->tgl_perawatan . ' ' . $dataPemberianObat->jam)->format('YmdHis');
        $noresep = "$dataPemberianObat->no_resep-$dataPemberianObat->kode_brng-$timestamp_resep";

        //Obat Jadi di Kirim dl
        $cekKiriman = ResponseMedicationSatuSehat::where('noResep', $noresep)->first();

        //Get Id Obat
        $mappingObat = SatuSehatController::getIdObat($dataPemberianObat->kode_brng);

        if ((empty($cekKiriman)) && (!empty($mappingObat)) && (!empty($dataPemberianObat->dataEncounter))) {

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
                        "value" => "$dataPemberianObat->no_resep"
                    ]
                ],
                "code" => [
                    "coding" => [ //Iki dinggo mapping obate
                        [
                            "system" => "http://sys-ids.kemkes.go.id/kfa",
                            "code" => "$mappingObat->id_ihs",
                            "display" => "$dataPemberianObat->nama_brng"
                        ]
                    ]
                ],
                "status" => "active",
                "form" => [
                    "coding" => [ //Iki dinggo medication form tipe obate opo
                        [
                            "system" => "$mappingObat->form_coding_system",
                            "code" => "$mappingObat->kode_medication",
                            "display" => "$mappingObat->form_display"
                        ]
                    ]
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

            // //Waktu Request Obat
            $waktuRequest = Carbon::parse($dataPemberianObat->tgl_peresepan . ' ' . $dataPemberianObat->jam_peresepan);
            $formatWaktuRequest = $waktuRequest->setTimezone('UTC')->toW3cString();
            //Waktu Pemberian
            $waktuPenyerahan = Carbon::parse($dataPemberianObat->tgl_penyerahan . ' ' . $dataPemberianObat->jam_penyerahan);
            $formatWaktuPenyerahan = $waktuPenyerahan->setTimezone('UTC')->toW3cString();

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

                    dd($test, 'kirim medication');

                    if (!empty($errorCode['issue'][0])) {
                        $pesan = $errorCode['issue'][0]->details->text;

                        $message = "Medication 1 error $pesan";

                        dd($message, 'medication1');

                        $cek = LogErrorSatuSehat::where('subject', 'Obat Medication1')
                            ->where('keterangan', 'like', '%' . $dataPengunjung->no_rawat . '%')
                            ->whereDate('created_at', Carbon::now())
                            ->get();
                        if ($cek->count() < 1) {
                            $error = new LogErrorSatuSehat();
                            $error->subject = 'Obat Medication1';
                            $error->keterangan = $dataPengunjung->no_rawat . ' error kirim ' . $dataPemberianObat->nama_brng . ' pesan ' . $pesan;
                            $error->save();
                        }
                    } else {
                        dd($errorCode['fault']->faultstring, 'medication1');

                        $cek = LogErrorSatuSehat::where('subject', 'Obat Medication1')
                            ->where('keterangan', 'like', '%' . $dataPengunjung->no_rawat . '%')
                            ->whereDate('created_at', Carbon::now())
                            ->get();
                        if ($cek->count() < 1) {
                            $error = new LogErrorSatuSehat();
                            $error->subject = 'Obat Medication1';
                            $error->keterangan = $dataPengunjung->no_rawat . ' error kirim ' . $dataPemberianObat->nama_brng . ' pesan ' . $errorCode['fault']->faultstring;
                            $error->save();
                        }
                    }
                }
            }

            $data = json_decode($response->getBody());

            if (!empty($data->id) && $data->resourceType == "Medication") {

                $simpan = new ResponseMedicationSatuSehat();
                $simpan->noRawat = $dataPemberianObat->no_rawat;
                $simpan->tgl_registrasi = $dataPemberianObat->tgl_registrasi;
                $simpan->noResep = $noresep;
                $simpan->medication1 = $data->id;
                $simpan->save();

                //Off ini dulu buat pakai langsung dari inisialisasi idMedication1 saja
                $idMedication1 = $data->id;
                //Waktu Registrasi
                $waktuRegis = Carbon::parse($dataPemberianObat->tgl_registrasi . ' ' . $dataPemberianObat->jam_reg);
                $formatWaktuRegis = $waktuRegis->setTimezone('UTC')->toW3cString();
                //Waktu Request Obat
                $waktuRequest = Carbon::parse($dataPemberianObat->tgl_perawatan . ' ' . $dataPemberianObat->jam);
                $formatWaktuRequest = $waktuRequest->setTimezone('UTC')->toW3cString();
                // //Waktu Pemberian
                // $waktuPenyerahan = Carbon::parse($dataPemberianObat->tgl_penyerahan . ' ' . $dataPemberianObat->jam_penyerahan);
                // $formatWaktuPenyerahan = $waktuPenyerahan->setTimezone('UTC')->toW3cString();
                // if ($waktuPenyerahan < $waktuRequest) {
                //     $formatWaktuPenyerahan = $waktuRequest->copy()->addMinutes(5)->toW3cString();
                //     // dd($formatWaktuPenyerahan, 'waktu penyerahan diubah karena lebih awal dari waktu request');
                // }
                // Waktu Request
                $waktuRequest = Carbon::parse($dataPemberianObat->tgl_perawatan . ' ' . $dataPemberianObat->jam);

                // Waktu Penyerahan (safe)
                if (!empty($dataPemberianObat->tgl_penyerahan) && !empty($dataPemberianObat->jam_penyerahan)) {

                    $waktuPenyerahan = Carbon::parse(
                        $dataPemberianObat->tgl_penyerahan . ' ' . $dataPemberianObat->jam_penyerahan
                    );

                    if ($waktuPenyerahan < $waktuRequest) {
                        $waktuPenyerahan = $waktuRequest->copy()->addMinutes(5);
                    }
                } else {

                    // fallback kalau belum ada
                    $waktuPenyerahan = $waktuRequest->copy()->addMinutes(5);
                }

                // format akhir
                $formatWaktuPenyerahan = $waktuPenyerahan->setTimezone('UTC')->toW3cString();

                $medicationRequest = [
                    "resourceType" => "MedicationRequest",
                    "identifier" => [
                        [
                            "system" => "http://sys-ids.kemkes.go.id/prescription/$idRS",
                            "use" => "official",
                            "value" => "$dataPemberianObat->no_resep"
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
                        "display" => "$dataPemberianObat->nama_brng"
                    ],
                    "subject" => [
                        "reference" => "Patient/$idPasien",
                        "display" => "$dataPemberianObat->nm_pasien"
                    ],
                    "encounter" => [
                        "reference" => "Encounter/$dataPemberianObat->dataEncounter"
                    ],
                    "authoredOn" => "$formatWaktuRegis",
                    "requester" => [
                        "reference" => "Practitioner/$idDokter",
                        "display" => "$dataPemberianObat->nama_dokter"
                    ],
                    "dosageInstruction" => [
                        [
                            "sequence" => 1,
                            "text" => $dataPemberianObat->aturan_pakai,
                            "route" => [
                                "coding" => [
                                    [
                                        "system" => $mappingObat->route_system,
                                        "code" => $mappingObat->kode_route,
                                        "display" => $mappingObat->route_display
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "dispenseRequest" => [
                        "validityPeriod" => [ //optional Waktu Peresepan
                            "start" => "$formatWaktuRequest",
                            "end" => "$formatWaktuPenyerahan"
                        ],
                        "numberOfRepeatsAllowed" => 0, //optional
                        "quantity" => [ //wajib
                            "value" => $dataPemberianObat->jml_obat,
                            "unit" => "$mappingObat->kode_ingredient",
                            "system" => "$mappingObat->ingredient_system",
                            "code" => "$mappingObat->kode_ingredient"
                        ],
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

                        dd($test, 'medicationRequest', $medicationRequest);
                        $errorCode = (array) $test;
                        if (!empty($errorCode['issue'][0])) {
                            $pesan = $errorCode['issue'][0]->details->text;

                            $message = "Medication Request error $pesan";
                        } else {
                            $message = $errorCode['fault']->faultstring;
                        }
                    }
                }

                $data = json_decode($response->getBody());

                //Update data di table respone medication request
                $update = ResponseMedicationSatuSehat::where('medication1', $idMedication1)->first();
                $update->medicationRequest = $data->id;
                $update->save();

                $idMedicationRequest = $data->id;
                if (!empty($idMedicationRequest)) {
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
                                "value" => "$dataPemberianObat->no_resep"
                            ]
                        ],
                        "code" => [
                            "coding" => [ //Iki dinggo mapping obate
                                [
                                    "system" => "http://sys-ids.kemkes.go.id/kfa",
                                    "code" => "$mappingObat->id_ihs",
                                    "display" => "$dataPemberianObat->nama_brng"
                                ]
                            ]
                        ],
                        "status" => "active",
                        "form" => [
                            "coding" => [ //Iki dinggo medication form tipe obate opo
                                [
                                    "system" => "$mappingObat->form_coding_system",
                                    "code" => "$mappingObat->kode_medication",
                                    "display" => "$mappingObat->form_display"
                                ]
                            ]
                        ],

                        "batch" => [
                            "lotNumber" => "-",
                            "expirationDate" => "$dataPemberianObat->expire"
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

                                // Session::flash('error', $message);
                            } else {
                                // Session::flash('error', $errorCode['fault']->faultstring);
                            }
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
                    $waktuAwal = $dataPemberianObat->tgl_perawatan . ' ' . $dataPemberianObat->jam;
                    $waktu_mulai = new Carbon($waktuAwal);
                    $formatWaktuMulai = $waktu_mulai->setTimezone('UTC')->toW3cString();
                    // $waktuSelesai = $dataPemberianObat->tgl_penyerahan . ' ' . $dataPemberianObat->jam_penyerahan;
                    // $waktu_selesai = new Carbon($waktuSelesai);
                    // $formatWaktuSelesai = $waktu_selesai->setTimezone('UTC')->toW3cString();
                    $waktuRequest = Carbon::parse($dataPemberianObat->tgl_perawatan . ' ' . $dataPemberianObat->jam);

                    // Waktu Penyerahan (safe)
                    if (!empty($dataPemberianObat->tgl_penyerahan) && !empty($dataPemberianObat->jam_penyerahan)) {

                        $waktuPenyerahan = Carbon::parse(
                            $dataPemberianObat->tgl_penyerahan . ' ' . $dataPemberianObat->jam_penyerahan
                        );

                        if ($waktuPenyerahan < $waktuRequest) {
                            $waktuPenyerahan = $waktuRequest->copy()->addMinutes(5);
                        }
                    } else {

                        // fallback kalau belum ada
                        $waktuPenyerahan = $waktuRequest->copy()->addMinutes(5);
                    }

                    // format akhir
                    $formatWaktuSelesai = $waktuPenyerahan->setTimezone('UTC')->toW3cString();


                    //Cek Obat yang diberikan
                    $obatPasien = SatuSehatController::obatDiberikan($dataPemberianObat->no_rawat, $dataPemberianObat->kode_brng);

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
                                    "value" => "$dataPemberianObat->no_resep"
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
                                "display" => "$dataPemberianObat->nama_brng" //free text
                            ],
                            "subject" => [ //wajib
                                "reference" => "Patient/$idPasien",
                                "display" => "$dataPemberianObat->nm_pasien"
                            ],
                            "context" => [ //wajib
                                "reference" => "Encounter/$dataPemberianObat->dataEncounter"
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
                                    "sequence" => 1,
                                    "text" => $aturanObatPasien
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

                                // dd($test, 'medication dispance', $medicationDispense);
                                if (!empty($errorCode['issue'][0])) {
                                    $pesan = $errorCode['issue'][0]->details->text;

                                    $message = "Medication Dispance error $pesan";

                                    // Session::flash('error', $message);
                                } else {
                                    // Session::flash('error', $errorCode['fault']->faultstring);
                                }
                            }
                        }

                        $data = json_decode($response->getBody());

                        //Update data di table respone medication request
                        $update = ResponseMedicationSatuSehat::where('medication1', $idMedication1)->first();
                        $update->medicationDispence = $data->id;
                        $update->save();
                    }
                }
            }
        } else {
            $cek = LogErrorSatuSehat::where('subject', 'Obat Medication1')
                ->where('keterangan', 'like', '%' . $dataPemberianObat->no_rawat . '%')
                ->whereDate('created_at', Carbon::now())
                ->get();
            if ($cek->count() < 1) {
                $error = new LogErrorSatuSehat();
                $error->subject = 'Obat Medication1';
                $error->keterangan = $dataPemberianObat->no_rawat . ' tidak ditemukan mapping obat untuk ' . $dataPemberianObat->nama_brng;
                $error->save();
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
    }

    public function medicationPeriod(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Farmasi');
        session()->put('cucu', 'Send Farmasi');

        if (empty($request->get('tanggal'))) {
            $tanggal = Carbon::now();
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
        }

        $dataPemberianObat = DB::connection('mysqlkhanza')->table('detail_pemberian_obat')
            ->join('resep_obat', function ($join) {
                $join->on('resep_obat.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
                    ->on('resep_obat.tgl_perawatan', '=', 'detail_pemberian_obat.tgl_perawatan')
                    ->on('resep_obat.jam', '=', 'detail_pemberian_obat.jam');
            })
            ->leftJoin('aturan_pakai', function ($join) {
                $join->on('aturan_pakai.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
                    ->on('aturan_pakai.tgl_perawatan', '=', 'detail_pemberian_obat.tgl_perawatan')
                    ->on('aturan_pakai.jam', '=', 'detail_pemberian_obat.jam')
                    ->on('aturan_pakai.no_rawat', '=', 'detail_pemberian_obat.no_rawat');
            })
            ->join('databarang', 'databarang.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
            ->join('golongan_barang', 'golongan_barang.kode', '=', 'databarang.kode_golongan')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
            ->join('pegawai', 'pegawai.nik', '=', 'resep_obat.kd_dokter')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select(
                'resep_obat.*',
                'databarang.nama_brng',
                'databarang.expire',
                'golongan_barang.nama as nama_golongan',
                'detail_pemberian_obat.kode_brng',
                'detail_pemberian_obat.status as status_obat',
                'detail_pemberian_obat.jml as jml_obat',
                'aturan_pakai.aturan as aturan_pakai',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'poliklinik.nm_poli',
                'pegawai.nama as nama_dokter',
                'pegawai.no_ktp as ktp_dokter'
            )
            ->where('resep_obat.tgl_penyerahan', '!=', '0000-00-00')
            ->where('resep_obat.tgl_perawatan', $tanggal->format('Y-m-d'))
            ->where('golongan_barang.nama', 'like', '%Obat%')
            ->orderBy('resep_obat.no_resep', 'asc')
            ->get();

        //check data sudah terkirim atau belum
        $terkirim = ResponseMedicationSatuSehat::whereYear('tgl_registrasi', Carbon::parse($tanggal)->format('Y'))
            ->whereMonth('tgl_registrasi', Carbon::parse($tanggal)->format('m'))
            ->selectRaw("SUBSTRING_INDEX(noResep, '-', 1) as resep")
            ->pluck('resep')->unique()->toArray();

        $dataPemberianObat = $dataPemberianObat->filter(function ($item) use ($terkirim) {
            return !in_array($item->no_resep, $terkirim);
        });

        $dataRawat = $dataPemberianObat->pluck('no_rawat')->unique()->toArray();

        if (!empty($dataRawat)) {
            $dataEncounterRajal = ResponseSatuSehat::whereIn('noRawat', $dataRawat)
                ->get()
                ->keyBy('noRawat');
            $dataEncounterIgd = ResponseIgdSatuSehat::whereIn('noRawat', $dataRawat)
                ->get()
                ->keyBy('noRawat');
            $dataEncounterRanap = ResponseRanapSatuSehat::whereIn('noRawat', $dataRawat)
                ->get()
                ->keyBy('noRawat');
        }

        $ktpList = $dataPemberianObat->pluck('ktp_pasien')->unique();
        $idSehatMap = \App\PasienSehat::whereIn('nik', $ktpList)->pluck('satu_sehat_id', 'nik');

        foreach ($dataPemberianObat as $key => $list) {
            if ($list->status_obat == 'Ralan') {
                $isIgd = stripos($list->nm_poli, 'igd') !== false;

                if ($isIgd) {
                    $list->dataEncounter = $dataEncounterIgd[$list->no_rawat]->encounter_id ?? null;
                } else {
                    $list->dataEncounter = $dataEncounterRajal[$list->no_rawat]->encounter_id ?? null;
                }
            } elseif ($list->status_obat == 'Ranap') {
                $list->dataEncounter = $dataEncounterRanap[$list->no_rawat]->encounter_id ?? null;
            }
            $list->idSehat = $idSehatMap[$list->ktp_pasien] ?? null;
        }

        $noresep = null;
        $limit = 0;

        foreach ($dataPemberianObat as $item) {
            $idPasien = $item->idSehat;
            $idDokter = SatuSehatController::practitioner($item->ktp_dokter);
            $idLokasi = SatuSehatController::getIdPoli($item->kd_poli);

            if (!empty($item->dataEncounter) && !empty($item->aturan_pakai) && !empty($idPasien) && !empty($idDokter) && !empty($idLokasi)) {
                if ($noresep !== $item->no_resep) {
                    $noresep = $item->no_resep;
                    ++$limit;

                    if ($limit > 10) {
                        break;
                    }
                }
                ObatSehatController::sendMedication($item, $idPasien, $idDokter, $idLokasi);
            } else {
                LogErrorSatuSehat::create([
                    'subject' => 'Kirim Medication Satu Sehat',
                    'keterangan' => 'Data pemberian obat tidak lengkap untuk dikirim ke Satu Sehat dengan no resep ' . $item->no_resep
                ]);
            }
        }

        if ($tanggal->isToday()) {
            $dataLog = ResponseMedicationSatuSehat::whereDate('created_at', $tanggal)
                ->orWhereDate('updated_at', $tanggal)
                ->get();
        } else {
            $dataLog = ResponseMedicationSatuSehat::whereDate('tgl_registrasi', $tanggal)
                ->orWhereDate('updated_at', Carbon::now())
                ->get();
        }

        return view('satu_sehat.client_apotek', compact('dataLog'));
    }

    public function medicationDetail($id)
    {
        $resep = Crypt::decrypt($id);
        $pecah = explode('-', $resep);

        $no_rawat = $pecah[0];
        $no_resep = $pecah[1];
        $kode_barang = $pecah[2];

        $dataPengunjung = DB::connection('mysqlkhanza')->table('detail_pemberian_obat')
            ->join('resep_obat', function ($join) {
                $join->on('resep_obat.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
                    ->on('resep_obat.tgl_perawatan', '=', 'detail_pemberian_obat.tgl_perawatan')
                    ->on('resep_obat.jam', '=', 'detail_pemberian_obat.jam');
            })
            ->leftJoin('aturan_pakai', function ($join) {
                $join->on('aturan_pakai.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
                    ->on('aturan_pakai.tgl_perawatan', '=', 'detail_pemberian_obat.tgl_perawatan')
                    ->on('aturan_pakai.jam', '=', 'detail_pemberian_obat.jam')
                    ->on('aturan_pakai.no_rawat', '=', 'detail_pemberian_obat.no_rawat');
            })
            ->join('databarang', 'databarang.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
            ->join('pegawai', 'pegawai.nik', '=', 'resep_obat.kd_dokter')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select(
                'resep_obat.*',
                'databarang.nama_brng',
                'databarang.expire',
                'detail_pemberian_obat.kode_brng',
                'detail_pemberian_obat.status as status_obat',
                'detail_pemberian_obat.jml as jml_obat',
                'aturan_pakai.aturan as aturan_pakai',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'poliklinik.nm_poli',
                'pegawai.nama as nama_dokter',
                'pegawai.no_ktp as ktp_dokter'
            )
            ->where('resep_obat.no_rawat', $no_rawat)
            ->where('resep_obat.no_resep', $no_resep)
            ->where('detail_pemberian_obat.kode_brng', $kode_barang)
            ->first();

        if ($dataPengunjung->status_obat == 'Ralan') {
            $isIgd = stripos($dataPengunjung->nm_poli, 'igd') !== false;

            if ($isIgd) {
                $dataEncounter  = ResponseIgdSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
            } else {
                $dataEncounter  = ResponseSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
            }
        } elseif ($dataPengunjung->status_obat == 'Ranap') {
            $dataEncounter = ResponseRanapSatuSehat::where('noRawat', $dataPengunjung->no_rawat)->first();
        }

        $idSehat = PasienSehat::where('nik', $dataPengunjung->ktp_pasien)->first();
        $idSehatPractition = PraktisiSehat::where('nik', $dataPengunjung->ktp_dokter)->first();

        $dataPengunjung->dataEncounter = $dataEncounter->encounter_id ?? null;
        $dataPengunjung->idSehat = $idSehat->satu_sehat_id ?? null;
        $dataPengunjung->idSehatPractition = $idSehatPractition->satu_sehat_id ?? null;


        $timestamp_resep = Carbon::parse($dataPengunjung->tgl_perawatan . ' ' . $dataPengunjung->jam)->format('YmdHis');
        $noResep = "$dataPengunjung->no_resep-$dataPengunjung->kode_brng-$timestamp_resep";

        $dataResponse = ResponseMedicationSatuSehat::where('noResep', $noResep)
            ->first();

        $dataMapping = DB::connection('mysqlkhanza')->table('fhir_farmasi')
            ->join('databarang', 'databarang.kode_brng', '=', 'fhir_farmasi.kode_brng')
            ->leftJoin('fhir_master_kfa', 'fhir_master_kfa.kd_kfa', '=', 'fhir_farmasi.id_ihs')
            ->leftJoin('fhir_master_medicationform', 'fhir_master_medicationform.kd_medication', '=', 'fhir_farmasi.kode_medication')
            ->leftJoin('fhir_master_ucum', 'fhir_master_ucum.kd_ucum', '=', 'fhir_farmasi.kode_ucum')
            ->leftJoin('fhir_master_ingredient', 'fhir_master_ingredient.kd_ingredient', '=', 'fhir_farmasi.kode_ingredient')
            ->leftJoin('fhir_master_route', 'fhir_master_route.kd_route', '=', 'fhir_farmasi.kode_route')
            ->select(
                'fhir_farmasi.*',
                'databarang.nama_brng',
                'databarang.expire',
                'databarang.status as status_aktif_obat',
                'fhir_master_kfa.display as kfa_display',
                'fhir_master_medicationform.display as medicationform_display',
                'fhir_master_medicationform.coding_system as medicationform_system',
                'fhir_master_ucum.system as ucum_system',
                'fhir_master_ucum.name as ucum_name',
                'fhir_master_ucum.sinonim as ucum_sinonim',
                'fhir_master_ingredient.display as ingredient_display',
                'fhir_master_ingredient.system as ingredient_system',
                'fhir_master_route.system as route_system',
                'fhir_master_route.display as route_display',
                'fhir_master_route.keterangan as route_keterangan'
            )
            ->where('fhir_farmasi.kode_brng', $dataPengunjung->kode_brng)
            ->first();
        // dd($dataPengunjung, $dataResponse, $dataMapping);

        return view('satu_sehat.detail_obat', compact('dataPengunjung', 'dataResponse', 'dataMapping'));
    }

    public function kirimUlang(Request $request)
    {
        $dataPemberianObat = DB::connection('mysqlkhanza')->table('detail_pemberian_obat')
            ->join('resep_obat', function ($join) {
                $join->on('resep_obat.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
                    ->on('resep_obat.tgl_perawatan', '=', 'detail_pemberian_obat.tgl_perawatan')
                    ->on('resep_obat.jam', '=', 'detail_pemberian_obat.jam');
            })
            ->leftJoin('aturan_pakai', function ($join) {
                $join->on('aturan_pakai.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
                    ->on('aturan_pakai.tgl_perawatan', '=', 'detail_pemberian_obat.tgl_perawatan')
                    ->on('aturan_pakai.jam', '=', 'detail_pemberian_obat.jam')
                    ->on('aturan_pakai.no_rawat', '=', 'detail_pemberian_obat.no_rawat');
            })
            ->join('databarang', 'databarang.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
            ->join('pegawai', 'pegawai.nik', '=', 'resep_obat.kd_dokter')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select(
                'resep_obat.*',
                'databarang.nama_brng',
                'databarang.expire',
                'detail_pemberian_obat.kode_brng',
                'detail_pemberian_obat.status as status_obat',
                'detail_pemberian_obat.jml as jml_obat',
                'aturan_pakai.aturan as aturan_pakai',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'poliklinik.nm_poli',
                'pegawai.nama as nama_dokter',
                'pegawai.no_ktp as ktp_dokter'
            )
            ->where('resep_obat.no_rawat', $request->no_rawat)
            ->where('resep_obat.no_resep', $request->no_resep)
            ->where('detail_pemberian_obat.kode_brng', $request->kode_barang)
            ->first();

        // dd($dataPemberianObat);

        $idRS = env('IDRS');

        $timestamp_resep = Carbon::parse($dataPemberianObat->tgl_perawatan . ' ' . $dataPemberianObat->jam)->format('YmdHis');
        $noresep = "$dataPemberianObat->no_resep-$dataPemberianObat->kode_brng-$timestamp_resep";

        //Get Id Obat
        $mappingObat = SatuSehatController::getIdObat($dataPemberianObat->kode_brng);
        $dataPemberianObat->dataEncounter = $request->idEncounter;
        $idPasien = $request->idSehatPasien;
        $idDokter = $request->idIhsDokter;

        if ((!empty($mappingObat)) && (!empty($dataPemberianObat->dataEncounter))) {
            $cekKiriman = ResponseMedicationSatuSehat::where('noResep', $noresep)->first();

            if (empty($cekKiriman->medication1)) {
                goto medication1Step;
            } elseif (empty($cekKiriman->medication1)) {
                goto medicationRequestStep;
            } elseif (empty($cekKiriman->medication1)) {
                goto medication2Step;
            } elseif (empty($cekKiriman->medication1)) {
                goto medicationDispanceStep;
            } else {
                Session::flash('sukses', 'Data sudah pernah dikirim');

                return redirect()->back();
            }

            medication1Step:

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
                        "value" => "$dataPemberianObat->no_resep"
                    ]
                ],
                "code" => [
                    "coding" => [ //Iki dinggo mapping obate
                        [
                            "system" => "http://sys-ids.kemkes.go.id/kfa",
                            "code" => "$mappingObat->id_ihs",
                            "display" => "$dataPemberianObat->nama_brng"
                        ]
                    ]
                ],
                "status" => "active",
                "form" => [
                    "coding" => [ //Iki dinggo medication form tipe obate opo
                        [
                            "system" => "$mappingObat->form_coding_system",
                            "code" => "$mappingObat->kode_medication",
                            "display" => "$mappingObat->form_display"
                        ]
                    ]
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
            // //Waktu Request Obat
            $waktuRequest = Carbon::parse($dataPemberianObat->tgl_peresepan . ' ' . $dataPemberianObat->jam_peresepan);
            $formatWaktuRequest = $waktuRequest->setTimezone('UTC')->toW3cString();
            //Waktu Pemberian
            $waktuPenyerahan = Carbon::parse($dataPemberianObat->tgl_penyerahan . ' ' . $dataPemberianObat->jam_penyerahan);
            $formatWaktuPenyerahan = $waktuPenyerahan->setTimezone('UTC')->toW3cString();

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

                    dd($test, 'kirim medication');

                    if (!empty($errorCode['issue'][0])) {
                        $pesan = $errorCode['issue'][0]->details->text;

                        $message = "Medication 1 error $pesan";

                        dd($message, 'medication1');

                        $cek = LogErrorSatuSehat::where('subject', 'Obat Medication1')
                            ->where('keterangan', 'like', '%' . $dataPengunjung->no_rawat . '%')
                            ->whereDate('created_at', Carbon::now())
                            ->get();
                        if ($cek->count() < 1) {
                            $error = new LogErrorSatuSehat();
                            $error->subject = 'Obat Medication1';
                            $error->keterangan = $dataPengunjung->no_rawat . ' error kirim ' . $dataPemberianObat->nama_brng . ' pesan ' . $pesan;
                            $error->save();
                        }
                    } else {
                        dd($errorCode['fault']->faultstring, 'medication1');

                        $cek = LogErrorSatuSehat::where('subject', 'Obat Medication1')
                            ->where('keterangan', 'like', '%' . $dataPengunjung->no_rawat . '%')
                            ->whereDate('created_at', Carbon::now())
                            ->get();
                        if ($cek->count() < 1) {
                            $error = new LogErrorSatuSehat();
                            $error->subject = 'Obat Medication1';
                            $error->keterangan = $dataPengunjung->no_rawat . ' error kirim ' . $dataPemberianObat->nama_brng . ' pesan ' . $errorCode['fault']->faultstring;
                            $error->save();
                        }
                    }
                }
            }

            $data = json_decode($response->getBody());

            if (!empty($data->id) && $data->resourceType == "Medication") {

                $simpan = new ResponseMedicationSatuSehat();
                $simpan->noRawat = $dataPemberianObat->no_rawat;
                $simpan->tgl_registrasi = $dataPemberianObat->tgl_registrasi;
                $simpan->noResep = $noresep;
                $simpan->medication1 = $data->id;
                $simpan->save();

                //Off ini dulu buat pakai langsung dari inisialisasi idMedication1 saja
                $idMedication1 = $data->id;

                medicationRequestStep:
                if (empty($idMedication1)) {
                    $idMedication1 = $cekKiriman->medication1;
                }
                //Waktu Registrasi
                $waktuRegis = Carbon::parse($dataPemberianObat->tgl_registrasi . ' ' . $dataPemberianObat->jam_reg);
                $formatWaktuRegis = $waktuRegis->setTimezone('UTC')->toW3cString();
                //Waktu Request Obat
                $waktuRequest = Carbon::parse($dataPemberianObat->tgl_perawatan . ' ' . $dataPemberianObat->jam);
                $formatWaktuRequest = $waktuRequest->setTimezone('UTC')->toW3cString();
                // //Waktu Pemberian
                // $waktuPenyerahan = Carbon::parse($dataPemberianObat->tgl_penyerahan . ' ' . $dataPemberianObat->jam_penyerahan);
                // $formatWaktuPenyerahan = $waktuPenyerahan->setTimezone('UTC')->toW3cString();
                // Waktu Request
                $waktuRequest = Carbon::parse($dataPemberianObat->tgl_perawatan . ' ' . $dataPemberianObat->jam);

                // Waktu Penyerahan (safe)
                if (!empty($dataPemberianObat->tgl_penyerahan) && !empty($dataPemberianObat->jam_penyerahan)) {

                    $waktuPenyerahan = Carbon::parse(
                        $dataPemberianObat->tgl_penyerahan . ' ' . $dataPemberianObat->jam_penyerahan
                    );

                    if ($waktuPenyerahan < $waktuRequest) {
                        $waktuPenyerahan = $waktuRequest->copy()->addMinutes(5);
                    }
                } else {

                    // fallback kalau belum ada
                    $waktuPenyerahan = $waktuRequest->copy()->addMinutes(5);
                }

                // format akhir
                $formatWaktuPenyerahan = $waktuPenyerahan->setTimezone('UTC')->toW3cString();

                $medicationRequest = [
                    "resourceType" => "MedicationRequest",
                    "identifier" => [
                        [
                            "system" => "http://sys-ids.kemkes.go.id/prescription/$idRS",
                            "use" => "official",
                            "value" => "$dataPemberianObat->no_resep"
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
                        "display" => "$dataPemberianObat->nama_brng"
                    ],
                    "subject" => [
                        "reference" => "Patient/$idPasien",
                        "display" => "$dataPemberianObat->nm_pasien"
                    ],
                    "encounter" => [
                        "reference" => "Encounter/$dataPemberianObat->dataEncounter"
                    ],
                    "authoredOn" => "$formatWaktuRegis",
                    "requester" => [
                        "reference" => "Practitioner/$idDokter",
                        "display" => "$dataPemberianObat->nama_dokter"
                    ],
                    "dosageInstruction" => [
                        [
                            "sequence" => 1,
                            "text" => $dataPemberianObat->aturan_pakai,
                            "route" => [
                                "coding" => [
                                    [
                                        "system" => $mappingObat->route_system,
                                        "code" => $mappingObat->kode_route,
                                        "display" => $mappingObat->route_display
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "dispenseRequest" => [
                        "validityPeriod" => [ //optional Waktu Peresepan
                            "start" => "$formatWaktuRequest",
                            "end" => "$formatWaktuPenyerahan"
                        ],
                        "numberOfRepeatsAllowed" => 0, //optional
                        "quantity" => [ //wajib
                            "value" => $dataPemberianObat->jml_obat,
                            "unit" => "$mappingObat->kode_ingredient",
                            "system" => "$mappingObat->ingredient_system",
                            "code" => "$mappingObat->kode_ingredient"
                        ],
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

                        dd($test, 'medicationRequest', $medicationRequest);
                        $errorCode = (array) $test;
                        if (!empty($errorCode['issue'][0])) {
                            $pesan = $errorCode['issue'][0]->details->text;

                            $message = "Medication Request error $pesan";
                        } else {
                            $message = $errorCode['fault']->faultstring;
                        }
                    }
                }

                $data = json_decode($response->getBody());

                //Update data di table respone medication request
                $update = ResponseMedicationSatuSehat::where('medication1', $idMedication1)->first();
                $update->medicationRequest = $data->id;
                $update->save();

                $idMedicationRequest = $data->id;
                medication2Step:

                if (empty($idMedicationRequest)) {
                    $idMedicationRequest = $cekKiriman->medicationRequest;
                }

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
                                "value" => "$dataPemberianObat->no_resep"
                            ]
                        ],
                        "code" => [
                            "coding" => [ //Iki dinggo mapping obate
                                [
                                    "system" => "http://sys-ids.kemkes.go.id/kfa",
                                    "code" => "$mappingObat->id_ihs",
                                    "display" => "$dataPemberianObat->nama_brng"
                                ]
                            ]
                        ],
                        "status" => "active",
                        "form" => [
                            "coding" => [ //Iki dinggo medication form tipe obate opo
                                [
                                    "system" => "$mappingObat->form_coding_system",
                                    "code" => "$mappingObat->kode_medication",
                                    "display" => "$mappingObat->form_display"
                                ]
                            ]
                        ],

                        "batch" => [
                            "lotNumber" => "-",
                            "expirationDate" => "$dataPemberianObat->expire"
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

                                // Session::flash('error', $message);
                            } else {
                                // Session::flash('error', $errorCode['fault']->faultstring);
                            }
                        }
                    }

                    $data = json_decode($response->getBody());

                    //Update data di table respone medication2
                    $update = ResponseMedicationSatuSehat::where('medication1', $idMedication1)->first();
                    $update->medication2 = $data->id;
                    $update->save();

                    //variabel dinamis
                    $idMedication2 = $data->id;

                    medicationDispanceStep:
                    if (empty($idMedicationRequest)) {
                        $idMedicationRequest = $cekKiriman->medicationRequest;
                    }
                    if (empty($idMedication2)) {
                        $idMedication2 = $cekKiriman->medication2;
                    }


                    // $apoteker = SatuSehatController::practitioner('3309090909870004');
                    $apoteker = "10007445367"; //Pak Wahid
                    $lokasiApotek = '5ca46bfc-9c51-4ed5-b160-bbabd1a50163';
                    //Waktu
                    $waktuAwal = $dataPemberianObat->tgl_perawatan . ' ' . $dataPemberianObat->jam;
                    $waktu_mulai = new Carbon($waktuAwal);
                    $formatWaktuMulai = $waktu_mulai->setTimezone('UTC')->toW3cString();
                    // $waktuSelesai = $dataPemberianObat->tgl_penyerahan . ' ' . $dataPemberianObat->jam_penyerahan;
                    // $waktu_selesai = new Carbon($waktuSelesai);
                    // $formatWaktuSelesai = $waktu_selesai->setTimezone('UTC')->toW3cString();
                    // Waktu Request
                    $waktuRequest = Carbon::parse($dataPemberianObat->tgl_perawatan . ' ' . $dataPemberianObat->jam);

                    // Waktu Penyerahan (safe)
                    if (!empty($dataPemberianObat->tgl_penyerahan) && !empty($dataPemberianObat->jam_penyerahan)) {

                        $waktuPenyerahan = Carbon::parse(
                            $dataPemberianObat->tgl_penyerahan . ' ' . $dataPemberianObat->jam_penyerahan
                        );

                        if ($waktuPenyerahan < $waktuRequest) {
                            $waktuPenyerahan = $waktuRequest->copy()->addMinutes(5);
                        }
                    } else {

                        // fallback kalau belum ada
                        $waktuPenyerahan = $waktuRequest->copy()->addMinutes(5);
                    }

                    // format akhir
                    $formatWaktuSelesai = $waktuPenyerahan->setTimezone('UTC')->toW3cString();

                    //Cek Obat yang diberikan
                    $obatPasien = SatuSehatController::obatDiberikan($dataPemberianObat->no_rawat, $dataPemberianObat->kode_brng);

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
                                    "value" => "$dataPemberianObat->no_resep"
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
                                "display" => "$dataPemberianObat->nama_brng" //free text
                            ],
                            "subject" => [ //wajib
                                "reference" => "Patient/$idPasien",
                                "display" => "$dataPemberianObat->nm_pasien"
                            ],
                            "context" => [ //wajib
                                "reference" => "Encounter/$dataPemberianObat->dataEncounter"
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
                            "whenPrepared" => "$formatWaktuMulai", //optional
                            "whenHandedOver" => "$formatWaktuSelesai", //optional
                            "dosageInstruction" => [
                                [
                                    "sequence" => 1,
                                    "text" => $aturanObatPasien
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

                                    // Session::flash('error', $message);
                                } else {
                                    // Session::flash('error', $errorCode['fault']->faultstring);
                                }
                            }
                        }

                        $data = json_decode($response->getBody());

                        //Update data di table respone medication request
                        $update = ResponseMedicationSatuSehat::where('medication1', $idMedication1)->first();
                        $update->medicationDispence = $data->id;
                        $update->save();
                    }

                    Session::flash('sukses', 'Data berhasil dikirim');

                    return redirect()->back();
                }
            }
        } else {
            $cek = LogErrorSatuSehat::where('subject', 'Obat Medication1')
                ->where('keterangan', 'like', '%' . $dataPemberianObat->no_rawat . '%')
                ->whereDate('created_at', Carbon::now())
                ->get();
            if ($cek->count() < 1) {
                $error = new LogErrorSatuSehat();
                $error->subject = 'Obat Medication1';
                $error->keterangan = $dataPemberianObat->no_rawat . ' tidak ditemukan mapping obat untuk ' . $dataPemberianObat->nama_brng;
                $error->save();
            }

            Session::flash('error', 'Terjadi kesalahan dalam pengiriman data obat');

            return redirect()->back();
        }
    }

    public function saveKfa(Request $request)
    {
        $cekObat = DB::connection('mysqlkhanza')->table('fhir_master_kfa')
            ->where('kd_kfa', $request->kode_kfa)
            ->first();

        if ($cekObat) {
            Session::flash('error', 'Data obat KFA sudah ada!');

            return redirect()->back();
        } else {
            // $idRS = env('IDRS');
            // //Send data
            // $access_token = SatuSehatController::getTokenSehat();
            // $client = new \GuzzleHttp\Client(['base_uri' => env('URL_APIKFA')]);
            // try {
            //     $response = $client->request('GET', "/kfa-v2/products?identifier=kfa&code=" . $request->kode_kfa, [
            //         'headers' => [
            //             'Authorization' => "Bearer {$access_token}"
            //         ]
            //     ]);
            // } catch (ClientException $e) {
            //     if ($e->hasResponse()) {
            //         $response = $e->getResponse();
            //         $test = json_decode($response->getBody());
            //         dd($test);
            //     }

            //     $message = "Gagal melakukan pencarian " . $cari;
            // }

            // $dataResponse = json_decode($response->getBody());

            // dd($dataResponse);

            $simpan = DB::connection('mysqlkhanza')
                ->table('fhir_master_kfa')
                ->insert([
                    'kd_kfa' => $request->kode_kfa,
                    'display' => $request->display
                ]);

            if ($simpan) {
                Session::flash('sukses', 'Data berhasil disimpan');

                return redirect()->back();
            } else {
                Session::flash('error', 'Data gagal disimpan');

                return redirect()->back();
            }
        }
    }
}
