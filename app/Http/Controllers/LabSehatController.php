<?php

namespace App\Http\Controllers;

use App\LogErrorSatuSehat;
use App\ResponseIgdSatuSehat;
use App\ResponseLabSatuSehat;
use App\ResponseObservationLab;
use App\ResponseRanapSatuSehat;
use App\ResponseSatuSehat;
use Carbon\Carbon;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LabSehatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Laboratorium');
        session()->put('cucu', 'Summary Laboratorium');
        set_time_limit(0);

        if (empty($request->get('tanggal_awal'))) {
            $tanggal_awal = Carbon::now();
            $tanggal_akhir = Carbon::now();
        } else {
            $tanggal_awal = new Carbon($request->get('tanggal_awal'));
            $tanggal_akhir = new Carbon($request->get('tanggal_akhir'));
        }

        $dataPermintaanLab = DB::connection('mysqlkhanza')->table('permintaan_lab')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_lab.no_rawat')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('permintaan_pemeriksaan_lab', 'permintaan_pemeriksaan_lab.noorder', '=', 'permintaan_lab.noorder')
            ->leftJoin('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'permintaan_pemeriksaan_lab.kd_jenis_prw')
            ->select(
                'permintaan_lab.no_rawat',
                'permintaan_lab.noorder',
                'permintaan_lab.tgl_permintaan',
                'permintaan_lab.status',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'jns_perawatan_lab.kd_jenis_prw',
                'jns_perawatan_lab.nm_perawatan',
                'poliklinik.nm_poli'
            )
            ->whereBetween('tgl_permintaan', [$tanggal_awal, $tanggal_akhir])
            ->get();

        $dataRawat = $dataPermintaanLab->pluck('no_rawat')->toArray();
        $dataLog = collect();

        if (!empty($dataRawat)) {
            $dataLog = ResponseLabSatuSehat::whereIn('noRawat', $dataRawat)
                ->get()
                ->keyBy(function ($item) {
                    return $item->noOrder . '-' . $item->kd_jenis_prw;
                });

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

        $ktpList = $dataPermintaanLab->pluck('ktp_pasien')->unique();
        $idSehatMap = \App\PasienSehat::whereIn('nik', $ktpList)->pluck('satu_sehat_id', 'nik');

        foreach ($dataPermintaanLab as $list) {
            $list->dataResponse = $dataLog[$list->noorder . '-' . $list->kd_jenis_prw] ?? null;
            if ($list->status == 'ralan') {
                $isIgd = stripos($list->nm_poli, 'igd') !== false;

                if ($isIgd) {
                    $list->dataEncounter = $dataEncounterIgd[$list->no_rawat] ?? null;
                } else {
                    $list->dataEncounter = $dataEncounterRajal[$list->no_rawat] ?? null;
                }
            } elseif ($list->status == 'ranap') {
                $list->dataEncounter = $dataEncounterRanap[$list->no_rawat] ?? null;
            }
            $list->idSehat = $idSehatMap[$list->ktp_pasien] ?? null;
        }

        $formAction = route('satuSehat.summaryLab');

        return view('satu_sehat.summary_lab', compact('dataLog', 'dataPermintaanLab', 'formAction'));
    }

    public function sendLab(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Laboratorium');
        session()->put('cucu', 'Send Laboratorium');
        set_time_limit(0);

        $idRS = Env('IDRS');

        if (empty($request->get('tanggal'))) {
            $pasien_tanggal = Carbon::now()->format('Y-m-d');
            $kemarin = Carbon::yesterday()->format('Y-m-d');
        } else {
            $pasien_tanggal = $request->get('tanggal');
            $kemarin = Carbon::parse($request->get('tanggal'))->subDays(1)->format('Y-m-d');
        }

        $sudahKirim = ResponseLabSatuSehat::whereBetween('tgl_registrasi', [$kemarin, $pasien_tanggal])->get();
        $listSudahKirim = $sudahKirim->pluck('noRawat')->toArray();

        // $dataPengunjung = DB::connection('mysqlkhanza')->table('reg_periksa')
        //     ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
        //     ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
        //     ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
        //     ->select(
        //         'reg_periksa.no_rkm_medis',
        //         'reg_periksa.no_rawat',
        //         'reg_periksa.tgl_registrasi',
        //         'reg_periksa.jam_reg',
        //         'reg_periksa.kd_dokter',
        //         'reg_periksa.status_lanjut',
        //         'reg_periksa.stts',
        //         'reg_periksa.kd_poli',
        //         'reg_periksa.kd_pj',
        //         'pasien.nm_pasien',
        //         'pasien.no_ktp as ktp_pasien',
        //         'pasien.tgl_lahir',
        //         'pasien.jk',
        //         'pegawai.no_ktp as ktp_dokter',
        //         'pegawai.nama as nama_dokter',
        //         'poliklinik.nm_poli'
        //     )
        //     ->where('reg_periksa.status_lanjut', 'Ralan')
        //     ->where('reg_periksa.stts', 'Sudah')
        //     ->where('reg_periksa.kd_poli', '!=', 'LAB')
        //     ->whereNotIn('reg_periksa.no_rawat', $listSudahKirim)
        //     ->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
        //     ->orWhere('reg_periksa.tgl_registrasi', $kemarin)
        //     ->get();

        $dataPermintaan = DB::connection('mysqlkhanza')->table('permintaan_lab')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_lab.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'permintaan_lab.dokter_perujuk')
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
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter'
            )
            // ->where('permintaan_lab.status', 'ralan')
            ->where('permintaan_lab.jam_hasil', '!=', '00:00:00')
            ->whereNotIn('permintaan_lab.no_rawat', $listSudahKirim)
            ->where(function ($query) use ($pasien_tanggal, $kemarin) {
                $query->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
                    ->orWhere('reg_periksa.tgl_registrasi', $kemarin);
            })
            ->get();

        foreach ($dataPermintaan as $pasienLab) {
            // $cekLab = DB::connection('mysqlkhanza')->table('permintaan_lab')
            //     ->join('pegawai', 'pegawai.nik', '=', 'permintaan_lab.dokter_perujuk')
            //     ->select(
            //         'permintaan_lab.noorder',
            //         'permintaan_lab.no_rawat',
            //         'permintaan_lab.tgl_permintaan',
            //         'permintaan_lab.jam_permintaan',
            //         'permintaan_lab.tgl_sampel',
            //         'permintaan_lab.jam_sampel',
            //         'permintaan_lab.tgl_hasil',
            //         'permintaan_lab.jam_hasil',
            //         'permintaan_lab.dokter_perujuk',
            //         'permintaan_lab.status',
            //         'pegawai.no_ktp as ktp_dokter',
            //         'pegawai.nama as nama_dokter'
            //     )
            //     ->where('no_rawat', $pasienLab->no_rawat)
            //     ->where('permintaan_lab.status', 'ralan')
            //     ->where('permintaan_lab.jam_hasil', '!=', '00:00:00')
            //     ->first();

            $idCounter = SatuSehatController::getEncounterId($pasienLab->no_rawat);
            $dokterPerujuk = SatuSehatController::practitioner($pasienLab->ktp_dokter);
            $idPasien = SatuSehatController::patientSehat($pasienLab->ktp_pasien);

            if ((!empty($pasienLab)) && (!empty($idCounter)) && (!empty($dokterPerujuk)) && (!empty($idPasien))) {
                //Cek apakah sudah pernah kirim data
                $cekResponseLab = ResponseLabSatuSehat::where('noOrder', $pasienLab->noorder)->first();

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
                                        "value" => "$pasienLab->noorder"
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
                                    "display" => "$pasienLab->nama_dokter"
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
                                    //Jika Duplicate
                                    if ($test && $test->issue[0]->code == 'duplicate') {
                                        try {
                                            $response = $client->request('GET', 'fhir-r4/v1/ServiceRequest?encounter=' . $idCounter->encounter_id, [
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

                                            dd($message, $test->issue[0], $idCounter->encounter_id, 'error service request Lab on duplicate');
                                        }

                                        $dataResponse = json_decode($response->getBody()->getContents());

                                        // dd($dataResponse, 'data response duplicate service request Lab');
                                        if ($dataResponse && $dataResponse->entry[0]->resource->id) {

                                            $idServiceRequest = $dataResponse->entry[0]->resource->id;
                                            $cekServiceRequest = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                                            if (empty($cekServiceRequest)) {

                                                $simpan = new ResponseLabSatuSehat();
                                                $simpan->noRawat = $pasienLab->no_rawat;
                                                $simpan->tgl_registrasi =  $PeriksaLab->tgl_periksa;
                                                $simpan->noOrder = $pasienLab->noorder;
                                                $simpan->kd_jenis_prw = $PeriksaLab->kd_jenis_prw;
                                                $simpan->serviceRequest_id = $idServiceRequest;
                                                $simpan->save();
                                            }
                                        }

                                        goto kirimDataSpesimen;
                                    }
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
                                $simpan->tgl_registrasi =  $pasienLab->tgl_registrasi;
                                $simpan->noOrder = $pasienLab->noorder;
                                $simpan->kd_jenis_prw = $PeriksaLab->kd_jenis_prw;
                                $simpan->serviceRequest_id = $idServiceRequest;
                                $simpan->save();

                                kirimDataSpesimen:
                                //ambil kode spesimen
                                $mapingSpecimen = SatuSehatController::getSpecimen($mappingLoinc->kd_loinc);
                                //Waktu Sampel
                                $waktuSampel = $pasienLab->tgl_sampel . ' ' . $pasienLab->jam_sampel;
                                $waktu_sampel = new Carbon($waktuSampel);
                                $formatWaktuSampel = $waktu_sampel->setTimezone('UTC')->toW3cString();

                                $Specimen = [
                                    "resourceType" => "Specimen",
                                    "identifier" => [
                                        [
                                            "system" => "http://sys-ids.kemkes.go.id/specimen/$idRS",
                                            "value" => "$pasienLab->noorder-$PeriksaLab->kd_jenis_prw",
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

                                        dd($test, 'Specimen Lab', $Specimen);
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
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$pasienLab->noorder-$DetailLab->id_template"
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
                                                                "effectiveDateTime" => $formatWaktuHasil,
                                                                "issued" => $formatWaktuHasil,
                                                                "performer" => [
                                                                    [
                                                                        "reference" => "Practitioner/$petugasLab"
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
                                                                        "value" => "$pasienLab->noorder-$DetailLab->id_template"
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
                                                                        "reference" => "Practitioner/$petugasLab"
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
                                                        if (!empty($answerList)) {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$pasienLab->noorder-$DetailLab->id_template"
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
                                                                        "reference" => "Practitioner/$petugasLab"
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
                                                                        "value" => "$pasienLab->noorder-$DetailLab->id_template"
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
                                                                        "reference" => "Practitioner/$petugasLab"
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
                                                                    "value" => "$pasienLab->noorder-$DetailLab->id_template"
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
                                                                    "reference" => "Practitioner/$petugasLab"
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
                                                    } elseif ($dataHasil->tipe_hasil_pemeriksaan == "Narative") { //Hasilnya berupa narasi tidak bisa masuk ke valueCodeableConcept, valueQuantity atau valueString karena tidak sesuai dengan standar answer list yang ada di Satu Sehat jadi masuk ke jenis narasi dengan valueString
                                                        $Observation = [
                                                            "resourceType" => "Observation",
                                                            "identifier" => [
                                                                [
                                                                    "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                    "value" => "$pasienLab->noorder-$DetailLab->id_template"
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
                                                                    "reference" => "Practitioner/$petugasLab"
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
                                                                        "value" => "$pasienLab->noorder-$DetailLab->kd_jenis_prw"
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
                                                                        "reference" => "Practitioner/$petugasLab"
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
                                                                        "value" => "$pasienLab->noorder-$DetailLab->kd_jenis_prw"
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
                                                                        "reference" => "Practitioner/$petugasLab"
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
                                                        if (!empty($answerList)) {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$pasienLab->noorder-$DetailLab->kd_jenis_prw"
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
                                                                        "reference" => "Practitioner/$petugasLab"
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
                                                                        "value" => "$pasienLab->noorder"
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
                                                                        "reference" => "Practitioner/$petugasLab"
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
                                                                    "value" => "$pasienLab->noorder-$DetailLab->kd_jenis_prw"
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
                                                                    "reference" => "Practitioner/$petugasLab"
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
                                                                    "value" => "$pasienLab->noorder-$DetailLab->kd_jenis_prw"
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
                                                                    "reference" => "Practitioner/$petugasLab"
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
                                    dd($responseSpecimen, 'response specimen kosong');
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
                                                "value" => "$pasienLab->noorder-$PeriksaLab->kd_jenis_prw"
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
                                        "effectiveDateTime" => "$formatWaktuHasil",
                                        "issued" => $formatWaktuHasil,
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
                                        if ($e->hasResponse()) {
                                            $response = $e->getResponse();

                                            // dd($response);
                                            $body = (string) $response->getBody();
                                            $test = json_decode($body);
                                            dd($test, 'Error Kirim Diagnostic Report Lab');
                                        }

                                        $message = "Error Kirim Report lab id service request " . $idServiceRequest;

                                        LogErrorSatuSehat::create([
                                            'subject' => 'Kirim Diagnostic Report Lab',
                                            'keterangan' => $message
                                        ]);

                                        goto nextPasienLab;
                                    }

                                    $responseReport = json_decode($response->getBody());
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
            ->whereBetween('tgl_permintaan', [$kemarin, $pasien_tanggal])
            ->get();

        $dataRawat = $dataPermintaanLab->pluck('no_rawat')->toArray();
        $dataLog = collect();

        if (!empty($dataRawat)) {
            $dataLog = ResponseLabSatuSehat::whereIn('noRawat', $dataRawat)
                ->get()
                // ->keyBy('noOrder');
                ->keyBy(function ($item) {
                    return $item->noOrder . '-' . $item->kd_jenis_prw;
                });

            $dataEncounterRajal = ResponseSatuSehat::whereIn('noRawat', $dataRawat)
                ->get()
                ->keyBy('noRawat');
            $dataEncounterRanap = ResponseRanapSatuSehat::whereIn('noRawat', $dataRawat)
                ->get()
                ->keyBy('noRawat');
        }

        foreach ($dataPermintaanLab as $list) {
            // $list->idSehat = $idSehatMap[$list->ktp_pasien] ?? null;
            $list->dataResponse = $dataLog[$list->noorder . '-' . $list->kd_jenis_prw] ?? null;
            if ($list->status == 'ralan') {
                $list->dataEncounter = $dataEncounterRajal[$list->no_rawat] ?? null;
            } elseif ($list->status == 'ranap') {
                $list->dataEncounter = $dataEncounterRanap[$list->no_rawat] ?? null;
            }
        }

        return view('satu_sehat.client_rujuklab', compact('dataLog', 'dataPermintaanLab'));
    }

    public function bundleLab(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Laboratorium');
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

                    //Send data
                    $access_token = SatuSehatController::getTokenSehat();
                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
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
                        }
                        $message = "Error Kirim Encounter No Rawat" . $pengunjung->no_rawat;

                        Session::flash('error', $message);

                        goto KirimEncounterLainnya;
                    }

                    $data = json_decode($response->getBody());

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

                    $loop = 0;
                    foreach ($periksaLab as $PeriksaLab) {
                        //ambil data mapping Loinc
                        $mappingLoinc = SatuSehatController::getLoinc($PeriksaLab->kd_jenis_prw);
                        $waktuPeriksaLab = new Carbon("$PeriksaLab->tgl_periksa $PeriksaLab->jam");
                        $formatPeriksaLab = $waktuPeriksaLab->setTimezone('UTC')->toW3cString();
                        $waktuSampel = new Carbon("$cekLab->tgl_sampel $cekLab->jam_sampel");
                        $formatWaktuSampel = $waktuSampel->setTimezone('UTC')->toW3cString();

                        //Cek apakah sudah ada mapping belum
                        if (!empty($mappingLoinc) && $loop <= 10) {
                            //data JSON
                            $ServiceRequest = [
                                "resourceType" => "ServiceRequest",
                                "identifier" => [
                                    [
                                        "system" => "http://sys-ids.kemkes.go.id/servicerequest/$idRS",
                                        "value" => "$cekLab->noorder-$PeriksaLab->kd_jenis_prw"
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

                            //Kirim/Create Service Request
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

                                    dd($test, "Error Kirim Service Request $PeriksaLab->kd_jenis_prw $PeriksaLab->no_rawat");
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
                                            "value" => "$cekLab->noorder-$PeriksaLab->kd_jenis_prw",
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
                                        dd($test, 'Error Kirim Specimen dengan id service ' . $idServiceRequest);
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
                                                //Seharusnya cek dulu ini paket atau tidak hasilnya juga di foreach tp ini lurus2 aja dulu
                                                //dah diatas ya dicek

                                                $dataHasil = SatuSehatController::getTemplateLoinc($DetailLab->id_template);

                                                if (!empty($dataHasil)) {
                                                    if ($dataHasil->tipe_hasil_pemeriksaan == "Nominal") { //Answer List diperlukan
                                                        //Get AnswerList Loinc
                                                        $answerList = SatuSehatController::getAnswerLoinc($dataHasil->code, $DetailLab->nilai);

                                                        //Answer List harus sesuai dengan standart jika tidak akan null dan masuk ke jenis narasi
                                                        if (!empty($answerList)) {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder-$DetailLab->id_template"
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
                                                                        "value" => "$cekLab->noorder-$DetailLab->id_template"
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
                                                        if (!empty($answerList)) {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder-$DetailLab->id_template"
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
                                                                        "value" => "$cekLab->noorder-$DetailLab->id_template"
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
                                                                    "value" => "$cekLab->noorder-$DetailLab->id_template"
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

                                                        $Observation = [
                                                            "resourceType" => "Observation",
                                                            "identifier" => [
                                                                [
                                                                    "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                    "value" => "$cekLab->noorder-$DetailLab->id_template"
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
                                                    $access_token = SatuSehatController::getTokenSehat();
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

                                                            $body = (string) $response->getBody();
                                                            $test = json_decode($body);
                                                        }

                                                        $message = "Error Kirim Observation Lab id service " . $idServiceRequest;

                                                        Session::flash('error', $message);

                                                        goto Selesai;
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
                                                //Seharusnya cek dulu ini paket atau tidak hasilnya juga di foreach tp ini lurus2 aja dulu
                                                //dah diatas ya dicek

                                                $dataHasil = SatuSehatController::getLoinc($DetailLab->kd_jenis_prw);

                                                if (!empty($dataHasil)) {
                                                    if ($dataHasil->tipe_hasil_pemeriksaan == "Nominal") { //Answer List diperlukan
                                                        //Get AnswerList Loinc
                                                        $answerList = SatuSehatController::getAnswerLoinc($dataHasil->code, $DetailLab->nilai);

                                                        //Answer List harus sesuai dengan standart jika tidak akan null dan masuk ke jenis narasi
                                                        if (!empty($answerList)) {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder-$DetailLab->id_template"
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
                                                                        "value" => "$cekLab->noorder-$DetailLab->id_template"
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
                                                        if (!empty($answerList)) {
                                                            $Observation = [
                                                                "resourceType" => "Observation",
                                                                "identifier" => [
                                                                    [
                                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                        "value" => "$cekLab->noorder-$DetailLab->id_template"
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
                                                                        "value" => "$cekLab->noorder-$DetailLab->id_template"
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
                                                                    "value" => "$cekLab->noorder-$DetailLab->id_template"
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

                                                        $Observation = [
                                                            "resourceType" => "Observation",
                                                            "identifier" => [
                                                                [
                                                                    "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                                    "value" => "$cekLab->noorder-$DetailLab->id_template"
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
                                                    $access_token = SatuSehatController::getTokenSehat();
                                                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
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
                                                        }

                                                        $message = "Error Kirim Observation Lab id service " . $idServiceRequest;

                                                        Session::flash('error', $message);

                                                        goto KirimPasienLain;
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
                                    }
                                } else {
                                    dd($responseSpecimen, "cek response specimen 2");
                                }

                                //Diagnostic Report
                                //Cek dulu Observasinya ada berapa hasil
                                $cekID = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                                $cekObservation = ResponseObservationLab::where('response_lab_satu_sehat_id', $cekID->id)->get();

                                if (count($cekObservation) > 0) {

                                    $arrObservation = array($cekObservation);
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
                                        dd($cekObservation, "memasukkan observation ke diagnostic report");
                                    }

                                    $Report = [
                                        "resourceType" => "DiagnosticReport",
                                        "identifier" => [
                                            [
                                                "system" => "http://sys-ids.kemkes.go.id/diagnostic/$idRS/lab",
                                                "use" => "official",
                                                "value" => "$cekLab->noorder-$PeriksaLab->kd_jenis_prw"
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

                                    //Kirim/Create Diagnostic Report
                                    $access_token = SatuSehatController::getTokenSehat();
                                    $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                                    try {
                                        $response = $client->request('POST', 'fhir-r4/v1/DiagnosticReport', [
                                            'headers' => [
                                                'Authorization' => "Bearer {$access_token}"
                                            ],
                                            'json' => $Report
                                        ]);
                                    } catch (BadResponseException $e) {
                                        if ($e->hasResponse()) {
                                            $response = $e->getResponse();

                                            $body = (string) $response->getBody();
                                            $test = json_decode($body);
                                            dd($test, "error kirim diagnostic report 2");
                                        }

                                        $message = "Error Kirim Report lab id service request " . $idServiceRequest;

                                        Session::flash('error', $message);

                                        goto KirimPasienLain;
                                    }

                                    $responseReport = json_decode($response->getBody());
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

        return view('satu_sehat.client_rujuklab', compact('dataLog'));
    }

    public function kirimServiceRequest(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Laboratorium');
        session()->put('cucu', 'Send Laboratorium');
        set_time_limit(0);

        if (empty($request->get('tanggal'))) {
            $pasien_tanggal = Carbon::now()->format('Y-m-d');
            $kemarin = Carbon::yesterday()->format('Y-m-d');
        } else {
            $pasien_tanggal = $request->get('tanggal');
            $kemarin = Carbon::parse($request->get('tanggal'))->subDays(1)->format('Y-m-d');
        }

        $sudahKirim = ResponseLabSatuSehat::whereBetween('tgl_registrasi', [$kemarin, $pasien_tanggal])->get();
        $listSudahKirim = $sudahKirim->pluck('noOrder')->toArray();

        $dataPermintaan = DB::connection('mysqlkhanza')->table('permintaan_lab')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_lab.no_rawat')
            ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'permintaan_lab.dokter_perujuk')
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
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli',
                'poliklinik.kd_poli'
            )
            // ->where('permintaan_lab.status', 'ralan')
            ->where('permintaan_lab.jam_hasil', '!=', '00:00:00')
            ->whereNotIn('permintaan_lab.no_rawat', $listSudahKirim)
            ->where(function ($query) use ($pasien_tanggal, $kemarin) {
                $query->where('reg_periksa.tgl_registrasi', $pasien_tanggal)
                    ->orWhere('reg_periksa.tgl_registrasi', $kemarin);
            })
            ->get();

        foreach ($dataPermintaan as $permintaan) {
            if ($permintaan->status == 'ralan') {
                $isIgd = stripos($permintaan->nm_poli, 'igd') !== false;

                if ($isIgd) {
                    $idCounter = ResponseIgdSatuSehat::where('noRawat', $permintaan->no_rawat)->first();
                } else {
                    $idCounter = SatuSehatController::getEncounterId($permintaan->no_rawat);
                }
            } elseif ($permintaan->status == 'ranap') {
                $idCounter = ResponseRanapSatuSehat::where('noRawat', $permintaan->no_rawat)->first();
            }
            $dokterPerujuk = SatuSehatController::practitioner($permintaan->ktp_dokter);
            $idPasien = SatuSehatController::patientSehat($permintaan->ktp_pasien);

            if ((!empty($idCounter)) && (!empty($dokterPerujuk)) && (!empty($idPasien))) {
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
                    ->where('no_rawat', $permintaan->no_rawat)
                    ->get();

                foreach ($periksaLab as $periksa) {
                    $mappingLoinc = SatuSehatController::getLoinc($periksa->kd_jenis_prw);
                    if ($periksa->ktp_petugas_lab == null || $periksa->ktp_petugas_lab == '-' || $periksa->ktp_petugas_lab == '') {
                        $petugasLab = null;
                    } else {
                        $petugasLab = SatuSehatController::practitioner($periksa->ktp_petugas_lab);
                    }

                    if ((!empty($petugasLab)) && (!empty($mappingLoinc))) {
                        //Kirim Service Request
                        $kirimServiceRequest = LabSehatController::kirimServiceRequestLab($permintaan, $periksa, $idCounter, $idPasien, $dokterPerujuk, $mappingLoinc, $petugasLab);
                    }
                }
            }
        }

        //Menyajikan data log pengiriman
        $dataPermintaanLab = DB::connection('mysqlkhanza')->table('permintaan_lab')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_lab.no_rawat')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('permintaan_pemeriksaan_lab', 'permintaan_pemeriksaan_lab.noorder', '=', 'permintaan_lab.noorder')
            ->leftJoin('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'permintaan_pemeriksaan_lab.kd_jenis_prw')
            ->select(
                'permintaan_lab.no_rawat',
                'permintaan_lab.noorder',
                'permintaan_lab.status',
                'pasien.nm_pasien',
                'jns_perawatan_lab.kd_jenis_prw',
                'jns_perawatan_lab.nm_perawatan',
                'poliklinik.nm_poli'
            )
            // ->where('permintaan_lab.status', 'ralan')
            ->whereBetween('tgl_permintaan', [$kemarin, $pasien_tanggal])
            ->get();

        $dataRawat = $dataPermintaanLab->pluck('no_rawat')->toArray();
        $dataLog = collect();

        if (!empty($dataRawat)) {
            $dataLog = ResponseLabSatuSehat::whereIn('noRawat', $dataRawat)
                ->get()
                // ->keyBy('noOrder');
                ->keyBy(function ($item) {
                    return $item->noOrder . '-' . $item->kd_jenis_prw;
                });

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

        foreach ($dataPermintaanLab as $list) {
            // $list->idSehat = $idSehatMap[$list->ktp_pasien] ?? null;
            $list->dataResponse = $dataLog[$list->noorder . '-' . $list->kd_jenis_prw] ?? null;
            if ($list->status == 'ralan') {
                $isIgd = stripos($list->nm_poli, 'igd') !== false;
                if ($isIgd) {
                    $list->dataEncounter = $dataEncounterIgd[$list->no_rawat] ?? null;
                } else {
                    $list->dataEncounter = $dataEncounterRajal[$list->no_rawat] ?? null;
                }
            } elseif ($list->status == 'ranap') {
                $list->dataEncounter = $dataEncounterRanap[$list->no_rawat] ?? null;
            }
        }

        $formAction = route('satuSehat.kirimServiceRequest');

        return view('satu_sehat.client_rujuklab', compact('dataLog', 'dataPermintaanLab', 'formAction'));
    }

    public function kirimServiceRequestLab($permintaan, $periksaLab, $idCounter, $idPasien, $dokterPerujuk, $mappingLoinc, $petugasLab)
    {
        $idRS = Env('IDRS');
        $waktuPerawatan = $periksaLab->tgl_periksa . ' ' . $periksaLab->jam;
        $waktu_perawatan = new Carbon($waktuPerawatan);
        $formatWaktuPerawatan = $waktu_perawatan->setTimezone('UTC')->toW3cString();
        //data JSON
        $ServiceRequest = [
            "resourceType" => "ServiceRequest",
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/servicerequest/$idRS",
                    "value" => "$permintaan->noorder-$periksaLab->kd_jenis_prw"
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
                "text" => "$periksaLab->nm_perawatan"
            ],
            "subject" => [
                "reference" => "Patient/$idPasien"
            ],
            "encounter" => [
                "reference" => "Encounter/$idCounter->encounter_id",
                "display" => "Permintaan $periksaLab->nm_perawatan pada $periksaLab->tgl_periksa pukul $periksaLab->jam WIB"
            ],
            "occurrenceDateTime" => $formatWaktuPerawatan,
            "requester" => [
                "reference" => "Practitioner/$dokterPerujuk",
                "display" => "$permintaan->nama_dokter"
            ],
            "performer" => [
                [
                    "reference" => "Practitioner/$petugasLab",
                    "display" => "$periksaLab->nama_petugas_lab"
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
                //Jika Duplicate
                if ($test && $test->issue[0]->code == 'duplicate') {
                    try {
                        $response = $client->request('GET', 'fhir-r4/v1/ServiceRequest?encounter=' . $idCounter->encounter_id, [
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

                        dd($message, $test->issue[0], $idCounter->encounter_id, 'error service request Lab new on duplicate');
                    }

                    $dataResponse = json_decode($response->getBody()->getContents());

                    if ($dataResponse && $dataResponse->entry[0]->resource->id) {
                        $idServiceRequest = $dataResponse->entry[0]->resource->id;

                        $cekServiceRequest = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                        if (empty($cekServiceRequest)) {

                            $simpan = new ResponseLabSatuSehat();
                            $simpan->noRawat = $permintaan->no_rawat;
                            $simpan->tgl_registrasi =  $permintaan->tgl_permintaan;
                            $simpan->noOrder = $permintaan->noorder;
                            $simpan->kd_jenis_prw = $periksaLab->kd_jenis_prw;
                            $simpan->serviceRequest_id = $idServiceRequest;
                            $simpan->save();
                        }
                    }
                }
            }

            $message = "Error Kirim Service Request kode tindakan $periksaLab->kd_jenis_prw, no order $permintaan->noorder, no rawat $periksaLab->no_rawat";

            LogErrorSatuSehat::create([
                'subject' => 'Kirim Service Request Lab',
                'keterangan' => $message
            ]);

            return;
        }

        $data = json_decode($response->getBody());

        if (!empty($data->id) && $data->resourceType == "ServiceRequest") {
            $idServiceRequest = $data->id;

            $simpan = new ResponseLabSatuSehat();
            $simpan->noRawat = $permintaan->no_rawat;
            $simpan->tgl_registrasi =  $permintaan->tgl_permintaan;
            $simpan->noOrder = $permintaan->noorder;
            $simpan->kd_jenis_prw = $periksaLab->kd_jenis_prw;
            $simpan->serviceRequest_id = $idServiceRequest;
            $simpan->save();
        } else {
            $message = "Error Response Service Request kode tindakan $periksaLab->kd_jenis_prw, no order $permintaan->noorder, no rawat $periksaLab->no_rawat";

            LogErrorSatuSehat::create([
                'subject' => 'Kirim Service Request Lab',
                'keterangan' => $message
            ]);
        }

        return;
    }

    public function closingLab(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Laboratorium');
        session()->put('cucu', 'Close Laboratorium');
        set_time_limit(0);

        if (empty($request->get('tanggal'))) {
            $pasien_tanggal = Carbon::now()->format('Y-m-d');
            $kemarin = Carbon::yesterday()->format('Y-m-d');
        } else {
            $pasien_tanggal = $request->get('tanggal');
            $kemarin = Carbon::parse($request->get('tanggal'))->subDays(1)->format('Y-m-d');
        }

        $dataSisir = ResponseLabSatuSehat::whereBetween('tgl_registrasi', [$kemarin, $pasien_tanggal])
            ->whereNotNull('serviceRequest_id')
            ->get();

        foreach ($dataSisir as $data) {
            if (empty($data->specimen_id)) {
                //Kirim spesimen dan Observation
                LabSehatController::sendSpecimen($data);
            }
            if (empty($data->report_id) && !empty($data->specimen_id)) {
                LabSehatController::sendReport($data);
            }
        }

        //Menyajikan data log pengiriman
        $dataPermintaanLab = DB::connection('mysqlkhanza')->table('permintaan_lab')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_lab.no_rawat')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('permintaan_pemeriksaan_lab', 'permintaan_pemeriksaan_lab.noorder', '=', 'permintaan_lab.noorder')
            ->leftJoin('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'permintaan_pemeriksaan_lab.kd_jenis_prw')
            ->select(
                'permintaan_lab.no_rawat',
                'permintaan_lab.noorder',
                'permintaan_lab.status',
                'pasien.nm_pasien',
                'jns_perawatan_lab.kd_jenis_prw',
                'jns_perawatan_lab.nm_perawatan',
                'poliklinik.nm_poli'
            )
            // ->where('permintaan_lab.status', 'ralan')
            ->whereBetween('tgl_permintaan', [$kemarin, $pasien_tanggal])
            ->get();

        $dataRawat = $dataPermintaanLab->pluck('no_rawat')->toArray();
        $dataLog = collect();

        if (!empty($dataRawat)) {
            $dataLog = ResponseLabSatuSehat::whereIn('noRawat', $dataRawat)
                ->get()
                // ->keyBy('noOrder');
                ->keyBy(function ($item) {
                    return $item->noOrder . '-' . $item->kd_jenis_prw;
                });

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

        foreach ($dataPermintaanLab as $list) {
            // $list->idSehat = $idSehatMap[$list->ktp_pasien] ?? null;
            $list->dataResponse = $dataLog[$list->noorder . '-' . $list->kd_jenis_prw] ?? null;
            if ($list->status == 'ralan') {
                $isIgd = stripos($list->nm_poli, 'igd') !== false;
                if ($isIgd) {
                    $list->dataEncounter = $dataEncounterIgd[$list->no_rawat] ?? null;
                } else {
                    $list->dataEncounter = $dataEncounterRajal[$list->no_rawat] ?? null;
                }
            } elseif ($list->status == 'ranap') {
                $list->dataEncounter = $dataEncounterRanap[$list->no_rawat] ?? null;
            }
        }

        $formAction = route('satuSehat.closingServiceRequest');

        return view('satu_sehat.client_rujuklab', compact('dataLog', 'dataPermintaanLab', 'formAction'));
    }

    public function sendSpecimen($data)
    {
        $idRS = Env('IDRS');
        $idServiceRequest = $data->serviceRequest_id;

        $permintaan = DB::connection('mysqlkhanza')->table('permintaan_lab')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_lab.no_rawat')
            ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'permintaan_lab.dokter_perujuk')
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
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            ->where('permintaan_lab.jam_hasil', '!=', '00:00:00')
            ->where('permintaan_lab.noorder', $data->noOrder)
            ->first();

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
            ->where('periksa_lab.no_rawat', $data->noRawat)
            ->where('periksa_lab.kd_jenis_prw', $data->kd_jenis_prw)
            ->get();

        if ($permintaan) {
            foreach ($periksaLab as $PeriksaLab) {
                $mappingLoinc = SatuSehatController::getLoinc($PeriksaLab->kd_jenis_prw);
                $waktuPerawatan = $PeriksaLab->tgl_periksa . ' ' . $PeriksaLab->jam;
                $waktu_perawatan = new Carbon($waktuPerawatan);
                $formatWaktuPerawatan = $waktu_perawatan->setTimezone('UTC')->toW3cString();
                $petugasLab = SatuSehatController::practitioner($PeriksaLab->ktp_petugas_lab);
                $mapingSpecimen = SatuSehatController::getSpecimen($mappingLoinc->kd_loinc);

                if ($permintaan->status == 'ralan') {
                    $isIgd = stripos($permintaan->nm_poli, 'igd') !== false;

                    if ($isIgd) {
                        $idCounter = ResponseIgdSatuSehat::where('noRawat', $permintaan->no_rawat)->first();
                    } else {
                        $idCounter = SatuSehatController::getEncounterId($permintaan->no_rawat);
                    }
                } elseif ($permintaan->status == 'ranap') {
                    $idCounter = ResponseRanapSatuSehat::where('noRawat', $permintaan->no_rawat)->first();
                }
                $dokterPerujuk = SatuSehatController::practitioner($permintaan->ktp_dokter);
                $idPasien = SatuSehatController::patientSehat($permintaan->ktp_pasien);
                //Waktu Sampel
                $waktuSampel = $permintaan->tgl_sampel . ' ' . $permintaan->jam_sampel;
                $waktu_sampel = new Carbon($waktuSampel);
                $formatWaktuSampel = $waktu_sampel->setTimezone('UTC')->toW3cString();

                if (!empty($mappingLoinc) && !empty($mapingSpecimen)) {
                    $Specimen = [
                        "resourceType" => "Specimen",
                        "identifier" => [
                            [
                                "system" => "http://sys-ids.kemkes.go.id/specimen/$idRS",
                                "value" => "$permintaan->noorder-$PeriksaLab->kd_jenis_prw",
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
                            "display" => "$permintaan->nm_pasien"
                        ],
                        "request" => [
                            [
                                "reference" => "ServiceRequest/$idServiceRequest"
                            ]
                        ],
                        "receivedTime" => $formatWaktuSampel
                    ];

                    //Kirim/Create Specimen
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

                            if ($test && $test->issue[0]->code == 'duplicate') {
                                try {
                                    $response = $client->request('GET', 'fhir-r4/v1/Specimen?request=' . $idServiceRequest, [
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

                                    dd($message, $test->issue[0], $idServiceRequest, 'error specimen Lab on duplicate');
                                }

                                $dataResponse = json_decode($response->getBody()->getContents());

                                if ($dataResponse && $dataResponse->entry[0]->resource->id) {

                                    $responseSpecimen = $dataResponse->entry[0]->resource->id;
                                    $cekServiceRequest = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                                    if (!empty($cekServiceRequest)) {
                                        $cekServiceRequest->specimen_id = $responseSpecimen;
                                        $cekServiceRequest->save();

                                        goto KirimObservation;
                                    }
                                }
                            }
                        }

                        $message = "Error Kirim Specimen dengan id service " . $idServiceRequest;

                        LogErrorSatuSehat::create([
                            'subject' => 'Kirim Specimen Lab',
                            'keterangan' => $message
                        ]);
                    }

                    $responseSpecimen = json_decode($response->getBody());

                    if (!empty($responseSpecimen->id) && $responseSpecimen->resourceType == "Specimen") {
                        //Update data di table respone medication request
                        $update = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                        $update->specimen_id = $responseSpecimen->id;
                        $update->save();

                        KirimObservation:

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
                            ->where('detail_periksa_lab.no_rawat', $permintaan->no_rawat)
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
                                                $Observation = [
                                                    "resourceType" => "Observation",
                                                    "identifier" => [
                                                        [
                                                            "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                            "value" => "$permintaan->noorder-$DetailLab->id_template"
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
                                                            "reference" => "Practitioner/$petugasLab"
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
                                                            "value" => "$permintaan->noorder-$DetailLab->id_template"
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
                                                            "reference" => "Practitioner/$petugasLab"
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
                                            if (!empty($answerList)) {
                                                $Observation = [
                                                    "resourceType" => "Observation",
                                                    "identifier" => [
                                                        [
                                                            "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                            "value" => "$permintaan->noorder-$DetailLab->id_template"
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
                                                            "reference" => "Practitioner/$petugasLab"
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
                                                            "value" => "$permintaan->noorder-$DetailLab->id_template"
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
                                                            "reference" => "Practitioner/$petugasLab"
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
                                                        "value" => "$permintaan->noorder-$DetailLab->id_template"
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
                                                        "reference" => "Practitioner/$petugasLab"
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
                                        } elseif ($dataHasil->tipe_hasil_pemeriksaan == "Narative") { //Hasilnya berupa narasi tidak bisa masuk ke valueCodeableConcept, valueQuantity atau valueString karena tidak sesuai dengan standar answer list yang ada di Satu Sehat jadi masuk ke jenis narasi dengan valueString
                                            $Observation = [
                                                "resourceType" => "Observation",
                                                "identifier" => [
                                                    [
                                                        "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                        "value" => "$permintaan->noorder-$DetailLab->id_template"
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
                                                        "reference" => "Practitioner/$petugasLab"
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

                                                dd($test, 'kirim observation lab error', $Observation);
                                            }

                                            $message = "Error Kirim Observation Lab id service " . $idServiceRequest;

                                            LogErrorSatuSehat::create([
                                                'subject' => 'Kirim Observation Lab',
                                                'keterangan' => $message
                                            ]);
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
                                                $Observation = [
                                                    "resourceType" => "Observation",
                                                    "identifier" => [
                                                        [
                                                            "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                            "value" => "$permintaan->noorder-$DetailLab->kd_jenis_prw"
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
                                                            "reference" => "Practitioner/$petugasLab"
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
                                                            "value" => "$permintaan->noorder-$DetailLab->kd_jenis_prw"
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
                                                            "reference" => "Practitioner/$petugasLab"
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
                                            if (!empty($answerList)) {
                                                $Observation = [
                                                    "resourceType" => "Observation",
                                                    "identifier" => [
                                                        [
                                                            "system" => "http://sys-ids.kemkes.go.id/observation/$idRS",
                                                            "value" => "$permintaan->noorder-$DetailLab->kd_jenis_prw"
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
                                                            "reference" => "Practitioner/$petugasLab"
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
                                                            "value" => "$permintaan->noorder"
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
                                                            "reference" => "Practitioner/$petugasLab"
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
                                                        "value" => "$permintaan->noorder-$DetailLab->kd_jenis_prw"
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
                                                        "reference" => "Practitioner/$petugasLab"
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
                                                        "value" => "$permintaan->noorder-$DetailLab->kd_jenis_prw"
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
                                                        "reference" => "Practitioner/$petugasLab"
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

                                                dd($test, 'Kirim Observation Lab Error');
                                            }

                                            $message = "Error Kirim Observation Lab id service " . $idServiceRequest;

                                            LogErrorSatuSehat::create([
                                                'subject' => 'Kirim Observation Lab',
                                                'keterangan' => $message
                                            ]);
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
                        }
                    } else {
                        dd($responseSpecimen, 'Gagal buat specimen Lab');
                    }
                }
            }
        }

        return;
    }

    public function sendReport($data)
    {
        $idRS = Env('IDRS');
        $idServiceRequest = $data->serviceRequest_id;

        $permintaan = DB::connection('mysqlkhanza')->table('permintaan_lab')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_lab.no_rawat')
            ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'permintaan_lab.dokter_perujuk')
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
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            ->where('permintaan_lab.jam_hasil', '!=', '00:00:00')
            ->where('permintaan_lab.noorder', $data->noOrder)
            ->first();

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
            ->where('periksa_lab.no_rawat', $data->noRawat)
            ->where('periksa_lab.kd_jenis_prw', $data->kd_jenis_prw)
            ->get();

        if ($permintaan) {
            foreach ($periksaLab as $PeriksaLab) {
                $cekObservation = ResponseObservationLab::where('response_lab_satu_sehat_id', $data->id)->get();

                if (count($cekObservation) > 0) {

                    $arrObservation = array($cekObservation);
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
                        dd($cekObservation, "cek observation untuk dimasukkan ke diagnostic report 3");
                    }

                    $mappingLoinc = SatuSehatController::getLoinc($PeriksaLab->kd_jenis_prw);
                    $waktuHasil = $PeriksaLab->tgl_periksa . ' ' . $PeriksaLab->jam;
                    $waktu_hasil = new Carbon($waktuHasil);
                    $formatWaktuHasil = $waktu_hasil->setTimezone('UTC')->toW3cString();

                    if ($permintaan->status == 'ralan') {
                        $isIgd = stripos($permintaan->nm_poli, 'igd') !== false;

                        if ($isIgd) {
                            $idCounter = ResponseIgdSatuSehat::where('noRawat', $permintaan->no_rawat)->first();
                        } else {
                            $idCounter = SatuSehatController::getEncounterId($permintaan->no_rawat);
                        }
                    } else {
                        $idCounter = ResponseRanapSatuSehat::where('noRawat', $permintaan->no_rawat)->first();
                    }
                    $idPasien = SatuSehatController::patientSehat($permintaan->ktp_pasien);
                    $Report = [
                        "resourceType" => "DiagnosticReport",
                        "identifier" => [
                            [
                                "system" => "http://sys-ids.kemkes.go.id/diagnostic/$idRS/lab",
                                "use" => "official",
                                "value" => "$permintaan->noorder-$PeriksaLab->kd_jenis_prw"
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
                        "effectiveDateTime" => "$formatWaktuHasil",
                        "issued" => $formatWaktuHasil,
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
                                "reference" => "Specimen/$data->specimen_id"
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
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();

                            $body = (string) $response->getBody();
                            $test = json_decode($body);
                            dd($test, "Error kirim diagnostic report 3");
                        }

                        $message = "Error Kirim Report lab id service request " . $idServiceRequest;

                        LogErrorSatuSehat::create([
                            'subject' => 'Kirim Diagnostic Report Lab',
                            'keterangan' => $message
                        ]);
                    }

                    $responseReport = json_decode($response->getBody());
                    if (!empty($responseReport->id) && $responseReport->resourceType == "DiagnosticReport") {
                        //Update data di table respone lab
                        $update = ResponseLabSatuSehat::where('serviceRequest_id', $idServiceRequest)->first();
                        $update->report_id = $responseReport->id;
                        $update->save();
                    }
                }
            }
        }

        return;
    }
}
