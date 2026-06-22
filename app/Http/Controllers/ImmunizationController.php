<?php

namespace App\Http\Controllers;

use App\ImmunizationFhir;
use App\LogErrorSatuSehat;
use App\PasienSehat;
use App\ResponseImmunizationSatuSehat;
use App\ResponseSatuSehat;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ImmunizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Imunisasi');
        session()->put('cucu', 'Summary Imunisasi');

        if (empty($request->get('tanggal_awal'))) {
            $tanggal_awal = Carbon::now();
            $tanggal_akhir = Carbon::now();
        } else {
            $tanggal_awal = new Carbon($request->get('tanggal_awal'));
            $tanggal_akhir = new Carbon($request->get('tanggal_akhir'));
        }

        $kunjungan = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('rawat_jl_drpr', 'reg_periksa.no_rawat', '=', 'rawat_jl_drpr.no_rawat')
            ->leftJoin('jns_perawatan', 'rawat_jl_drpr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
            ->leftJoin('pegawai', 'rawat_jl_drpr.kd_dokter', '=', 'pegawai.nik')
            ->select(
                'reg_periksa.*',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'poliklinik.nm_poli',
                'penjab.png_jawab',
                'rawat_jl_drpr.kd_jenis_prw',
                'jns_perawatan.nm_perawatan',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nm_dokter'
            )
            ->where('poliklinik.nm_poli', 'like', '%mcu%')
            ->where('jns_perawatan.nm_perawatan', 'like', '%vaksin%')
            ->whereBetween('reg_periksa.tgl_registrasi', [$tanggal_awal->format('Y-m-d'), $tanggal_akhir->format('Y-m-d')])
            ->get();

        // dd($kunjungan);

        //Ambil no_rawat
        $mapRawat = $kunjungan->pluck('no_rawat')->toArray();
        $mapKtp = $kunjungan->pluck('ktp_pasien')->toArray();
        $mapKfa = $kunjungan->pluck('kd_jenis_prw')->toArray();

        $dataEncounter = ResponseSatuSehat::whereIn('noRawat', $mapRawat)
            ->get()
            ->keyBy('noRawat');

        $dataKfa = ImmunizationFhir::whereIn('kd_jenis_prw', $mapKfa)
            ->get()
            ->keyBy('kd_jenis_prw');

        $dataIhs = PasienSehat::whereIn('nik', $mapKtp)
            ->get()
            ->keyBy('nik');

        $responseImunitation = ResponseImmunizationSatuSehat::whereIn('noRawat', $mapRawat)
            ->get()
            ->keyBy('noRawat');

        // dd($responseImunitation);

        foreach ($kunjungan as $list) {
            $list->encounter = $dataEncounter[$list->no_rawat]->encounter_id ?? null;
            $list->kfa = $dataKfa[$list->kd_jenis_prw]->kode_kfa ?? null;
            $list->imunisasi = $responseImunitation[$list->no_rawat] ?? null;
            $list->ihs = $dataIhs[$list->ktp_pasien]->satu_sehat_id ?? null;
        }

        // dd($kunjungan, $dataEncounter);

        return view('satu_sehat.summary_vaksin', compact('kunjungan'));
    }

    public function sendImmunization($data)
    {
        $pecah = explode('-', Crypt::decrypt($data));
        $no_rawat = $pecah[0];
        $kd_tindakan = $pecah[1];

        $kunjungan = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('rawat_jl_drpr', 'reg_periksa.no_rawat', '=', 'rawat_jl_drpr.no_rawat')
            ->leftJoin('riwayat_imunisasi_detail', 'reg_periksa.no_rawat', '=', 'riwayat_imunisasi_detail.no_rawat')
            ->leftJoin('jns_perawatan', 'rawat_jl_drpr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
            ->leftJoin('pegawai', 'rawat_jl_drpr.kd_dokter', '=', 'pegawai.nik')
            ->select(
                'reg_periksa.*',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'rawat_jl_drpr.kd_jenis_prw',
                'rawat_jl_drpr.tgl_perawatan',
                'rawat_jl_drpr.jam_rawat',
                'riwayat_imunisasi_detail.no_imunisasi',
                'riwayat_imunisasi_detail.no_batch',
                'jns_perawatan.nm_perawatan',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nm_dokter'
            )
            ->where('jns_perawatan.kd_jenis_prw', $kd_tindakan)
            ->where('reg_periksa.no_rawat', $no_rawat)
            ->first();

        if (empty($kunjungan->no_batch)) {
            return back()->with('error', 'Data Imunisasi tidak ditemukan');
        }

        $idPasien = SatuSehatController::patientSehat($kunjungan->ktp_pasien);
        $idDokter = SatuSehatController::practitioner($kunjungan->ktp_dokter);

        $waktuAwal = $kunjungan->tgl_perawatan . ' ' . $kunjungan->jam_rawat;
        $waktu_mulai = new Carbon($waktuAwal);
        $formatWaktuMulai = $waktu_mulai->setTimezone('UTC')->toW3cString();

        $dataEncounter = ResponseSatuSehat::where('noRawat', $no_rawat)
            ->first();

        if (!$dataEncounter) {
            return back()->with('error', 'Data Encounter tidak ditemukan');
        }

        $dataKfa = ImmunizationFhir::where('kd_jenis_prw', $kd_tindakan)
            ->first();

        if (!$dataKfa) {
            return back()->with('error', 'Data Mapping KFA tidak ditemukan');
        }

        $detailVaksin = DB::connection('mysqlkhanza')->table('databarang')
            ->where('kode_brng', $dataKfa->kode_barang)
            ->first();


        // $dataImunisasi = DB::connection('mysqlkhanza')->table('riwayat_imunisasi_detail')
        //     ->where('riwayat_imunisasi_detail.no_rawat', $no_rawat)
        //     ->first();

        // if (!$dataImunisasi) {
        //     return back()->with('error', 'Data Imunisasi tidak ditemukan');
        // }

        // dd($kunjungan, $dataImunisasi);

        $json_imunisasi = [
            "resourceType" => "Immunization",
            "status" => "completed",
            "vaccineCode" => [
                "coding" => [
                    [
                        "system" => "http://sys-ids.kemkes.go.id/kfa",
                        "code" => "$dataKfa->kode_kfa",
                        "display" => "$dataKfa->display_kfa"
                    ]
                ]
            ],
            "patient" => [
                "reference" => "Patient/$idPasien",
                "display" => "$kunjungan->nm_pasien"
            ],
            "encounter" => [
                "reference" => "Encounter/$dataEncounter->encounter_id"
            ],
            "occurrenceDateTime" => "$formatWaktuMulai",
            "recorded" => "$formatWaktuMulai",
            "primarySource" => true,
            "expirationDate" => "$detailVaksin->expire",
            "lotNumber" => "$kunjungan->no_batch", //gak ada kayake
            "performer" => [
                [
                    "function" => [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/v2-0443",
                                "code" => "AP",
                                "display" => "Administering Provider"
                            ]
                        ]
                    ],
                    "actor" => [
                        "reference" => "Practitioner/$idDokter"
                    ]
                ]
            ],
            "reasonCode" => [ // perlu cek lagi
                [
                    "coding" => [
                        [
                            "system" => $dataKfa->alasanImunisasi->system,
                            "code" => $dataKfa->alasanImunisasi->code,
                            "display" => $dataKfa->alasanImunisasi->display
                        ]
                    ]
                ]
            ],

            "protocolApplied" => [ // perlu cek lagi
                [
                    "doseNumberPositiveInt" => $kunjungan->no_imunisasi
                ]
            ]
        ];

        // dd($json_imunisasi, $dataKfa->alasanImunisasi->display);

        $access_token = SatuSehatController::getTokenSehat();
        $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
        try {
            $response = $client->request('POST', 'fhir-r4/v1/Immunization', [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ],
                'json' => $json_imunisasi
            ]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                //duplicate
                if ($test && $test->issue[0]->code) {
                    $pesan = $test->issue[0];

                    if ($pesan->code == 'duplicate') {
                        $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                        try {
                            // $response = $client->request('GET', 'fhir-r4/v1/Immunization?encounter=' . $dataEncounter->encounter_id, [
                            $response = $client->request('GET', 'fhir-r4/v1/Immunization?patient=' . $idPasien, [
                                'headers' => [
                                    'Authorization' => "Bearer {$access_token}"
                                ]
                            ]);
                        } catch (ClientException $e) {
                            if ($e->hasResponse()) {
                                $response = $e->getResponse();
                                $body = (string) $response->getBody();
                                $test = json_decode($body);

                                dd($test, 'kembalian kirim data duplicate');
                                if ($test && $test->issue[0]) {
                                    $message = $test->issue[0]->details->text;
                                } else {
                                    $message = 'error other';
                                }

                                LogErrorSatuSehat::create([
                                    'subject' => 'Imunization Duplicate',
                                    'keterangan' => "Pengiriman data imunization pasien no rawat : $no_rawat (" . $message . ")"
                                ]);

                                return;
                            }
                        }

                        $bodyResponse = json_decode($response->getBody());

                        // dd($bodyResponse, $dataEncounter->encounter_id,);

                        if (!empty($bodyResponse->entry)) {

                            foreach ($bodyResponse->entry as $entry) {

                                $resource = $entry->resource;

                                if (
                                    isset($resource->encounter->reference)
                                    && $resource->encounter->reference == "Encounter/" . $dataEncounter->encounter_id
                                ) {

                                    $kodeKfa = null;

                                    if (
                                        isset($resource->vaccineCode->coding[0])
                                        && $resource->vaccineCode->coding[0]->code == $dataKfa->kode_kfa
                                    ) {
                                        $kodeKfa = $resource->vaccineCode->coding[0]->code;
                                    }

                                    $simpan = ResponseImmunizationSatuSehat::updateOrCreate(
                                        [
                                            'encounter_id' => $dataEncounter->encounter_id,
                                            'kode_kfa' => $kodeKfa
                                        ],
                                        [
                                            'noRawat' => $no_rawat,
                                            'immunization_id' => $resource->id,
                                            'tgl_registrasi' => $kunjungan->tgl_registrasi,
                                            'patient_id' => $idPasien,
                                            'practitioner_id' => $idDokter,
                                            'kode_barang' => $dataKfa->kode_barang,
                                            'kd_jenis_prw' => $kunjungan->kd_jenis_prw,
                                            'display_kfa' => $dataKfa->display_kfa,
                                            'tanggal_kirim' => now(),
                                            'response_raw' => json_encode($resource)

                                        ]
                                    );
                                }
                            }

                            if ($simpan) {
                                return back()->with('sukses', 'Data berhasil disimpan');
                            }
                        } else {
                            $pesan = 'pola baru error';

                            Session::flash('error', $pesan);
                        }

                        return redirect()->back();
                    }

                    if ($test && $test->issue[0]) {
                        $pesan = $test->issue[0]->details->text;
                    }
                } else {
                    $pesan = 'pola baru error';
                }

                return back()->with('error', $test);
            }

            return redirect()->back();
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse->id)) {
            $simpan = ResponseImmunizationSatuSehat::updateOrCreate(
                [
                    'noRawat' => $kunjungan->no_rawat,
                    'kode_barang' => $dataKfa->kode_barang,
                    'kd_jenis_prw' => $kunjungan->kd_jenis_prw
                ],
                [
                    'tgl_registrasi' => $kunjungan->tgl_registrasi,
                    'encounter_id' => $dataEncounter->encounter_id,
                    'patient_id' => $idPasien,
                    'practitioner_id' => $idDokter,
                    'immunization_id' => $dataResponse->id,
                    'kode_kfa' => $dataKfa->kode_kfa,
                    'display_kfa' => $dataKfa->display_kfa,
                    'tanggal_kirim' => now(),
                    'response_raw' => json_encode($dataResponse)
                ]
            );


            if ($simpan) {
                return back()->with('sukses', 'Data berhasil disimpan');
            } else {
                return back()->with('error', 'Gagal menyimpan data');
            }
        } else {
            return back()->with('error', 'Terjadi masalah dalam pengiriman data');
        };
    }
}
