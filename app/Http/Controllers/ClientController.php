<?php

namespace App\Http\Controllers;

use App\LogResponseBios;
use App\SaldoAwal;
use App\ScheduleUpdate;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Session;

// use GuzzleHttp\Psr7\Request;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function token()
    {
        $hariini = Carbon::now();
        $cache_day = new Carbon(session('tokenBiosDay'));
        // $cache_day = new Carbon('2023-1-29 12:30:30');
        $selisih = $cache_day->diff($hariini);

        // $jalan = intval($selisih->format('%S'));
        // dd($selisih, $selisih->d, $jalan);
        // dd($cache_day);

        if (session('tokenBiosDay') == null) {
            // session()->put('tokenBiosDay', $hariini);
            $setting = Setting::where('nama', 'bios')->first();

            session()->put('base_url_bios', $setting->base_url);
            try {
                $guzzleRequest['curl'] = [
                    CURLOPT_TCP_KEEPALIVE => 1,
                ];
                $client = new \GuzzleHttp\Client(['base_uri' => $setting->base_url]);
                $response = $client->request('POST', 'token', [
                    'form_params' => [
                        'satker' => $setting->satker,
                        'key' => $setting->key,
                    ]
                ]);
            } catch (BadResponseException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $test = json_decode((string) $response->getBody());
                }
                dd($test);
            }

            $data = json_decode($response->getBody());

            // dd($data);

            if ($data->status == "MSG20004") {
                session()->put('tokenBios', $data->token);
                session()->put('tokenBiosDay', $hariini);
            }
        } elseif ($selisih->d >= 1) {
            $setting = Setting::where('nama', 'bios')->first();

            session()->put('base_url_bios', $setting->base_url);
            try {
                $guzzleRequest['curl'] = [
                    CURLOPT_TCP_KEEPALIVE => 1,
                ];
                $client = new \GuzzleHttp\Client(['base_uri' => $setting->base_url]);
                $response = $client->request('POST', 'token', [
                    'form_params' => [
                        'satker' => $setting->satker,
                        'key' => $setting->key,
                    ]
                ]);
            } catch (BadResponseException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $test = json_decode((string) $response->getBody());
                }
                dd($test);

                return redirect()->back();
            }

            $data = json_decode($response->getBody());

            // dd($data);

            if ($data->status == "MSG20004") {
                session()->put('tokenBios', $data->token);
                session()->put('tokenBiosDay', $hariini);
            }
        }

        // dd(session('token'));
    }

    public function saldoawal()
    {
        session()->put('ibu', 'Client Saldo Awal');
        session()->forget('anak');

        ClientController::token();

        $tanggal = Carbon::now()->format('Y-m-d');
        $jam = Carbon::now()->format('H:i:s');
        $data = $tanggal_update = null;
        $cek_saldo = SaldoAwal::where('nama', '=', 'Kesehatan')->count();

        // Jika Saldo Awal tidak ada maka
        if ($cek_saldo == 0) {
            $tanggal_saldo = Carbon::now()->subDays(2)->format('Y-m-d');
            $saldo = SaldoAwalController::saldoAwalKesehatan($tanggal_saldo);

            // dd($saldo);

            foreach ($saldo as $data) {
                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                $response = $client->request('POST', 'ws/kesehatan/prod', [
                    'headers' => [
                        'token' => session('token'),
                    ],
                    'form_params' => [
                        'kelas' => $data->kelas,
                        'jml_pasien' => $data->jml_pasien,
                        'jml_hari' => $data->jml_hari,
                        'tgl_transaksi' => $data->tgl_transaksi,
                    ]
                ]);

                $data = json_decode($response->getBody());

                if ($data->status != 'MSG20003') {
                    Session::flash('error', $data->message);

                    return view('client_saldo');
                } else {
                    Session::flash('sukses', "$data->status, $data->message");

                    $tanggal_update = Carbon::now()->locale('id')->format('Y-m-d H:i:s');
                }
            }

            if ($data->status == 'MSG20003') {
                //update nilai saldo awal
                $update = new SaldoAwal();
                $update->nama = 'Kesehatan';
                $update->tanggal = Carbon::now()->subDays(2)->format('Y-m-d');
                $update->save();
            }

            //Ambil Rekap
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('POST', 'get/data/kesehatan', [
                'headers' => [
                    'token' => session('token'),
                ],
                'form_params' => [
                    'tgl_transaksi' => Carbon::parse($tanggal_saldo)->format('Y/m/d'),
                ]
            ]);

            $dataterkirim = json_decode($response->getBody());
        } else { //Saldo Awal sudah ada
            $tanggal_saldo = Carbon::now()->subDays(1)->format('Y-m-d');
            $saldo = SaldoAwalController::saldoKesehatan($tanggal_saldo);

            // dd($saldo);

            $schedule = ScheduleUpdate::all();

            foreach ($schedule as $jadwal) {
                if (($jam >= $jadwal->waktu_mulai) and ($jam <= $jadwal->waktu_selesai)) {
                    foreach ($saldo as $data) {
                        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                        $response = $client->request('POST', 'ws/kesehatan/prod', [
                            'headers' => [
                                'token' => session('token'),
                            ],
                            'form_params' => [
                                'kelas' => $data->kelas,
                                'jml_pasien' => $data->jml_pasien,
                                'jml_hari' => $data->jml_hari,
                                'tgl_transaksi' => $data->tgl_transaksi,
                            ]
                        ]);

                        $data = json_decode($response->getBody());

                        // dd($data, session('token'));

                        if ($data->status != 'MSG20003') {
                            Session::flash('error', $data->message);

                            return view('client_saldo');
                        } else {

                            Session::flash('sukses', "$data->status, $data->message");
                            $tanggal_update = Carbon::now()->locale('id')->format('Y-m-d H:i:s');
                        }
                    }
                }
            }

            //Ambil Rekap
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('POST', 'get/data/kesehatan', [
                'headers' => [
                    'token' => session('token'),
                ],
                'form_params' => [
                    'tgl_transaksi' => Carbon::parse($tanggal_saldo)->format('Y/m/d'),
                ]
            ]);

            $dataterkirim = json_decode($response->getBody());
        }

        // dd($dataterkirim);

        return view('client_saldo', compact('data', 'tanggal_update', 'dataterkirim'));
    }

    public function kesehatan()
    {
        session()->put('ibu', 'Client Layanan Kesehatan Harian');
        session()->forget('anak');

        ClientController::token();

        $tanggal = Carbon::now()->yesterday()->format('Y-m-d');

        // $tanggal = '2023-01-29';

        // dd($tanggal);

        //data Inap Tanggal sesuai tanggal
        $cekRawatInap = LogResponseBios::where('tanggal', $tanggal)
            ->where('nama_fungsi', 'sendRawatInap')
            ->first();
        if (empty($cekRawatInap)) {
            ClientController::sendRawatInap($tanggal);
        } elseif (($cekRawatInap->status_terkirim == false)) {
            ClientController::sendRawatInap($tanggal);
        }

        //data IGD sesuai tanggal
        $cekIgd = LogResponseBios::where('tanggal', $tanggal)
            ->where('nama_fungsi', 'sendIgd')
            ->first();
        if (empty($cekIgd)) {
            ClientController::sendIgd($tanggal);
        } elseif ($cekIgd->status_terkirim == false) {
            ClientController::sendIgd($tanggal);
        }

        //data Lab Sample sesuai tanggal
        $cekLabSample = LogResponseBios::where('tanggal', $tanggal)
            ->where('nama_fungsi', 'sendLabSample')
            ->first();
        if (empty($cekLabSample)) {
            ClientController::sendLabSample($tanggal);
        } elseif ($cekLabSample->status_terkirim == false) {
            ClientController::sendLabSample($tanggal);
        }

        //data Lab Parameter sesuai tanggal
        $cekLabParameter = LogResponseBios::where('tanggal', $tanggal)
            ->where('nama_fungsi', 'sendLabParameter')
            ->first();
        if (empty($cekLabParameter)) {
            ClientController::sendLabParameter($tanggal);
        } elseif ($cekLabParameter->status_terkirim == false) {
            ClientController::sendLabParameter($tanggal);
        }

        //data Tindakan Operasi sesuai tanggal
        $cekOperasi = LogResponseBios::where('tanggal', $tanggal)
            ->where('nama_fungsi', 'sendOperasi')
            ->first();
        if (empty($cekOperasi)) {
            ClientController::sendOperasi($tanggal);
        } elseif ($cekOperasi->status_terkirim == false) {
            ClientController::sendOperasi($tanggal);
        }

        //data Radiologi sesuai tanggal
        $cekRadiologi = LogResponseBios::where('tanggal', $tanggal)
            ->where('nama_fungsi', 'sendRadiologi')
            ->first();
        if (empty($cekRadiologi)) {
            ClientController::sendRadiologi($tanggal);
        } elseif ($cekRadiologi->status_terkirim == false) {
            ClientController::sendRadiologi($tanggal);
        }

        //data Rajal sesuai tanggal
        $cekRajal = LogResponseBios::where('tanggal', $tanggal)
            ->where('nama_fungsi', 'sendRajal')
            ->first();
        if (empty($cekRajal)) {
            ClientController::sendRajal($tanggal);
        } elseif ($cekRajal->status_terkirim == false) {
            ClientController::sendRajal($tanggal);
        }

        //data Rajal/Poli sesuai tanggal
        $cekRajalPoli = LogResponseBios::where('tanggal', $tanggal)
            ->where('nama_fungsi', 'sendRajalPoli')
            ->first();
        if (empty($cekRajalPoli)) {
            ClientController::sendRajalPoli($tanggal);
        } elseif ($cekRajalPoli->status_terkirim == false) {
            ClientController::sendRajalPoli($tanggal);
        }

        //data Jaminan sesuai tanggal
        $cekJaminan = LogResponseBios::where('tanggal', $tanggal)
            ->where('nama_fungsi', 'sendJaminan')
            ->first();
        if (empty($cekJaminan)) {
            ClientController::sendJaminan($tanggal);
        } elseif ($cekJaminan->status_terkirim == false) {
            ClientController::sendJaminan($tanggal);
        }

        //data Farmasi sesuai tanggal
        $cekFarmasi = LogResponseBios::where('tanggal', $tanggal)
            ->where('nama_fungsi', 'sendFarmasi')
            ->first();
        if (empty($cekFarmasi)) {
            ClientController::sendFarmasi($tanggal);
        } elseif ($cekFarmasi->status_terkirim == false) {
            ClientController::sendFarmasi($tanggal);
        }

        $dataLog = LogResponseBios::where('tanggal', $tanggal)
            ->where('periode', 'Harian')
            ->get();

        return view('bios.log_client', compact('dataLog'));
    }

    public function statistik()
    {
        session()->put('ibu', 'Client Layanan Kesehatan Bulanan');
        session()->forget('anak');

        ClientController::token();

        $tanggal = '2023-01-05';
        $pecahTanggal = explode('-', $tanggal);
        $tanggalCek = $pecahTanggal[2];

        if ($tanggalCek == '05') {
            //data BOR sesuai tanggal
            $cekBor = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendBor')
                ->first();
            if (empty($cekBor)) {
                ClientController::sendBor($tanggal);
            } elseif ($cekBor->status_terkirim == false) {
                ClientController::sendBor($tanggal);
            }

            //data TOI sesuai tanggal
            $cekToi = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendToi')
                ->first();
            if (empty($cekToi)) {
                ClientController::sendToi($tanggal);
            } elseif ($cekToi->status_terkirim == false) {
                ClientController::sendToi($tanggal);
            }

            //data ALOS sesuai tanggal
            $cekAlos = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendAlos')
                ->first();
            if (empty($cekAlos)) {
                ClientController::sendAlos($tanggal);
            } elseif ($cekAlos->status_terkirim == false) {
                ClientController::sendAlos($tanggal);
            }

            //data BTO sesuai tanggal
            $cekBto = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendBto')
                ->first();
            if (empty($cekBto)) {
                ClientController::sendBto($tanggal);
            } elseif ($cekBto->status_terkirim == false) {
                ClientController::sendBto($tanggal);
            }
        }

        $dataLog = LogResponseBios::where('tanggal', $tanggal)
            ->where('periode', 'Bulanan')
            ->get();

        return view('bios.log_client', compact('dataLog'));
    }

    public function sdm()
    {
        session()->put('ibu', 'Client Layanan Kesehatan Bulanan');
        session()->forget('anak');

        ClientController::token();

        $tanggal = '2023-01-30';
        $pecahTanggal = explode('-', $tanggal);
        $tanggalCek = $pecahTanggal[2];
        $BulanCek = $pecahTanggal[1];

        if ($tanggalCek == '30' and (($BulanCek == 01) or ($BulanCek == 07))) {
            //data Dokter Spesialis sesuai tanggal
            $cekSpesialis = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendDokterSpesialis')
                ->first();
            if (empty($cekSpesialis)) {
                ClientController::sendDokterSpesialis($tanggal);
            } elseif ($cekSpesialis->status_terkirim == false) {
                ClientController::sendDokterSpesialis($tanggal);
            }

            //data Dokter Gigi sesuai tanggal
            $cekGigi = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendDokterGigi')
                ->first();
            if (empty($cekGigi)) {
                ClientController::sendDokterGigi($tanggal);
            } elseif ($cekGigi->status_terkirim == false) {
                ClientController::sendDokterGigi($tanggal);
            }

            //data Dokter Umum sesuai tanggal
            $cekUmum = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendDokterUmum')
                ->first();
            if (empty($cekUmum)) {
                ClientController::sendDokterUmum($tanggal);
            } elseif ($cekUmum->status_terkirim == false) {
                ClientController::sendDokterUmum($tanggal);
            }

            //data Perawat sesuai tanggal
            $cekPerawat = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendPerawat')
                ->first();
            if (empty($cekPerawat)) {
                ClientController::sendPerawat($tanggal);
            } elseif ($cekPerawat->status_terkirim == false) {
                ClientController::sendPerawat($tanggal);
            }

            //data Bidan sesuai tanggal
            $cekBidan = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendBidan')
                ->first();
            if (empty($cekBidan)) {
                ClientController::sendBidan($tanggal);
            } elseif ($cekBidan->status_terkirim == false) {
                ClientController::sendBidan($tanggal);
            }

            //data Pranata Laboratorium sesuai tanggal
            $cekLaborat = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendPranataLab')
                ->first();
            if (empty($cekLaborat)) {
                ClientController::sendPranataLab($tanggal);
            } elseif ($cekLaborat->status_terkirim == false) {
                ClientController::sendPranataLab($tanggal);
            }

            //data Radiographer sesuai tanggal
            $cekRadiographer = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendRadiographer')
                ->first();
            if (empty($cekRadiographer)) {
                ClientController::sendRadiographer($tanggal);
            } elseif ($cekRadiographer->status_terkirim == false) {
                ClientController::sendRadiographer($tanggal);
            }

            //data Nutrisionist sesuai tanggal
            $cekNutrisionist = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendNutrisionist')
                ->first();
            if (empty($cekRadiographer)) {
                ClientController::sendNutrisionist($tanggal);
            } elseif ($cekRadiographer->status_terkirim == false) {
                ClientController::sendNutrisionist($tanggal);
            }

            //data Fisioterapis sesuai tanggal
            $cekNutrisionist = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendFisioterapis')
                ->first();
            if (empty($cekNutrisionist)) {
                ClientController::sendFisioterapis($tanggal);
            } elseif ($cekNutrisionist->status_terkirim == false) {
                ClientController::sendFisioterapis($tanggal);
            }

            //data Pharmacist sesuai tanggal
            $cekPharmacist = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendPharmacist')
                ->first();
            if (empty($cekPharmacist)) {
                ClientController::sendPharmacist($tanggal);
            } elseif ($cekPharmacist->status_terkirim == false) {
                ClientController::sendPharmacist($tanggal);
            }

            //data Tenaga Professional Lainnya sesuai tanggal
            $cekProlain = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendProfessionalLainnya')
                ->first();
            if (empty($cekProlain)) {
                ClientController::sendProfessionalLainnya($tanggal);
            } elseif ($cekProlain->status_terkirim == false) {
                ClientController::sendProfessionalLainnya($tanggal);
            }

            //data Tenaga Non-Medis sesuai tanggal
            $cekNonMedis = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendNonMedis')
                ->first();
            if (empty($cekNonMedis)) {
                ClientController::sendNonMedis($tanggal);
            } elseif ($cekNonMedis->status_terkirim == false) {
                ClientController::sendNonMedis($tanggal);
            }

            //data Tenaga Non-Medis-Administrasi sesuai tanggal
            $cekNonMedisAdmin = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendNonMedisAdmin')
                ->first();
            if (empty($cekNonMedisAdmin)) {
                ClientController::sendNonMedisAdmin($tanggal);
            } elseif ($cekNonMedisAdmin->status_terkirim == false) {
                ClientController::sendNonMedisAdmin($tanggal);
            }

            //data Tenaga Sanitarian sesuai tanggal
            $cekSanitarian = LogResponseBios::where('tanggal', $tanggal)
                ->where('nama_fungsi', 'sendSanitarian')
                ->first();
            if (empty($cekSanitarian)) {
                ClientController::sendSanitarian($tanggal);
            } elseif ($cekSanitarian->status_terkirim == false) {
                ClientController::sendSanitarian($tanggal);
            }
        }

        $dataLog = LogResponseBios::where('tanggal', $tanggal)
            ->where('periode', 'Semesteran')
            ->get();

        return view('bios.log_client', compact('dataLog'));
    }

    public function sendRawatInap($tanggal)
    {
        $inap = KesehatanController::ranap($tanggal);

        // dd($inap);
        //Kirim data Pelayanan Rawat Inap
        foreach ($inap as $datainap) {
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
            try {
                $response = $client->request('POST', 'ws/kesehatan/layanan/pasien_ranap', [
                    'headers' => [
                        'token' => session('tokenBios'),
                    ],
                    'form_params' => [
                        'tgl_transaksi' => $datainap->tgl_transaksi,
                        'kode_kelas' => $datainap->kode_kelas,
                        'jumlah' => $datainap->jumlah,
                    ]
                ]);
            } catch (ClientException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $test = json_decode($response->getBody());

                    // dd($test, 'sendrawatInap');
                    $update = LogResponseBios::updateOrCreate(
                        ['tanggal' => $tanggal, 'nama_fungsi' => 'sendRawatInap'],
                        ['status_terkirim' => false, 'periode' => 'Harian']
                    );
                }
            }

            $dataResponse = json_decode($response->getBody());

            // dd($dataResponse);
            if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendRawatInap'],
                    ['status_terkirim' => true, 'periode' => 'Harian']
                );
            }
        }
    }

    public function sendIgd($tanggal)
    {
        $data = KesehatanController::igd($tanggal);

        // dd($data);
        //Kirim data IGD
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/layanan/pasien_igd', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $data->tgl_transaksi,
                    'jumlah' => $data->jumlah,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send IGD');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendIgd'],
                    ['status_terkirim' => false, 'periode' => 'Harian']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendIgd'],
                ['status_terkirim' => true, 'periode' => 'Harian']
            );
        }
    }

    public function sendLabSample($tanggal)
    {
        $data = KesehatanController::labsample($tanggal);

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/layanan/laboratorium', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $data->tgl_transaksi,
                    'jumlah' => $data->jumlah,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                dd($test, 'Send Lab Sample');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendLabSample'],
                    ['status_terkirim' => false, 'periode' => 'Harian']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($data);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendLabSample'],
                ['status_terkirim' => true, 'periode' => 'Harian']
            );
        }
    }

    public function sendLabParameter($tanggal)
    {
        $data = KesehatanController::labparameter($tanggal);

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        foreach ($data as $listData) {
            // dd($listData);
            try {
                $response = $client->request('POST', 'ws/kesehatan/layanan/laboratorium_detail', [
                    'headers' => [
                        'token' => session('tokenBios'),
                    ],
                    'form_params' => [
                        'tgl_transaksi' => $listData->tgl_transaksi,
                        'nama_layanan' => $listData->nama_layanan,
                        'jumlah' => $listData->jumlah,
                    ]
                ]);
            } catch (BadResponseException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $test = json_decode($response->getBody());

                    // dd($test, 'Send data Lab Parameter');
                    $update = LogResponseBios::updateOrCreate(
                        ['tanggal' => $tanggal, 'nama_fungsi' => 'sendLabParameter'],
                        ['status_terkirim' => false, 'periode' => 'Harian']
                    );
                }
            }

            $dataResponse = json_decode($response->getBody());

            // dd($data);

            if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendLabParameter'],
                    ['status_terkirim' => true, 'periode' => 'Harian']
                );
            }
        }
    }

    public function sendOperasi($tanggal)
    {
        $data = KesehatanController::operasi($tanggal);

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        foreach ($data as $listData) {
            // dd($listData);
            try {
                $response = $client->request('POST', 'ws/kesehatan/layanan/operasi', [
                    'headers' => [
                        'token' => session('tokenBios'),
                    ],
                    'form_params' => [
                        'tgl_transaksi' => $listData->tgl_transaksi,
                        'klasifikasi_operasi' => $listData->klasifikasi_operasi,
                        'jumlah' => $listData->jumlah,
                    ]
                ]);
            } catch (BadResponseException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $test = json_decode($response->getBody());

                    // dd($test, 'Send data Lab Parameter');
                    $update = LogResponseBios::updateOrCreate(
                        ['tanggal' => $tanggal, 'nama_fungsi' => 'sendOperasi'],
                        ['status_terkirim' => false, 'periode' => 'Harian']
                    );
                }
            }

            $dataResponse = json_decode($response->getBody());

            // dd($data);

            if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendOperasi'],
                    ['status_terkirim' => true, 'periode' => 'Harian']
                );
            }
        }
    }

    public function sendRadiologi($tanggal)
    {
        $data = KesehatanController::radiologi($tanggal);

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/layanan/radiologi', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $data->tgl_transaksi,
                    'jumlah' => $data->jumlah,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data Radiologi');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendRadiologi'],
                    ['status_terkirim' => false, 'periode' => 'Harian']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($data);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendRadiologi'],
                ['status_terkirim' => true, 'periode' => 'Harian']
            );
        }
    }

    public function sendRajal($tanggal)
    {
        $data = KesehatanController::rajal($tanggal);

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/layanan/pasien_ralan', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $data->tgl_transaksi,
                    'jumlah' => $data->jumlah,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data Rajal');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendRajal'],
                    ['status_terkirim' => false, 'periode' => 'Harian']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($data);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendRajal'],
                ['status_terkirim' => true, 'periode' => 'Harian']
            );
        }
    }

    public function sendRajalPoli($tanggal)
    {
        $data = KesehatanController::rajalpoli($tanggal);

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        foreach ($data as $itemData) {
            try {
                $response = $client->request('POST', 'ws/kesehatan/layanan/pasien_ralan_poli', [
                    'headers' => [
                        'token' => session('tokenBios'),
                    ],
                    'form_params' => [
                        'tgl_transaksi' => $itemData->tgl_transaksi,
                        'nama_poli' => $itemData->nama_poli,
                        'jumlah' => $itemData->jumlah,
                    ]
                ]);
            } catch (BadResponseException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $test = json_decode($response->getBody());

                    // dd($test, 'Send data Rajal Poli');
                    $update = LogResponseBios::updateOrCreate(
                        ['tanggal' => $tanggal, 'nama_fungsi' => 'sendRajalPoli'],
                        ['status_terkirim' => false, 'periode' => 'Harian']
                    );
                }
            }

            $dataResponse = json_decode($response->getBody());

            // dd($data);

            if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendRajalPoli'],
                    ['status_terkirim' => true, 'periode' => 'Harian']
                );
            }
        }
    }

    public function sendJaminan($tanggal)
    {
        $data = KesehatanController::bpjs($tanggal);
        $data2 = KesehatanController::nonbpjs($tanggal);

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/layanan/bpjs_nonbpbjs', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $data->tgl_transaksi,
                    'jumlah_bpjs' => $data->jumlah,
                    'jumlah_non_bpjs' => $data2->jumlah,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data BPJS dan Non BPJS');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendJaminan'],
                    ['status_terkirim' => false, 'periode' => 'Harian']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendJaminan'],
                ['status_terkirim' => true, 'periode' => 'Harian']
            );
        }
    }

    public function sendFarmasi($tanggal)
    {
        $data = KesehatanController::farmasi($tanggal);

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/layanan/farmasi', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $data->tgl_transaksi,
                    'jumlah' => $data->jumlah,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data farmasi');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendFarmasi'],
                    ['status_terkirim' => false, 'periode' => 'Harian']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($data);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendFarmasi'],
                ['status_terkirim' => true, 'periode' => 'Harian']
            );
        }
    }

    public function sendBor($tanggal)
    {
        $data = BorController::bor($tanggal);

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/layanan/bor', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $data->tgl_transaksi,
                    'bor' => $data->bor,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendBor'],
                    ['status_terkirim' => false, 'periode' => 'Bulanan']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendBor'],
                ['status_terkirim' => true, 'periode' => 'Bulanan']
            );
        }
    }

    public function sendToi($tanggal)
    {
        $data = BorController::toi($tanggal);

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/layanan/toi', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $data->tgl_transaksi,
                    'toi' => $data->toi,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendToi'],
                    ['status_terkirim' => false, 'periode' => 'Bulanan']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendToi'],
                ['status_terkirim' => true, 'periode' => 'Bulanan']
            );
        }
    }

    public function sendAlos($tanggal)
    {
        $data = BorController::alos($tanggal);

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/layanan/alos', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $data->tgl_transaksi,
                    'alos' => $data->alos,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendAlos'],
                    ['status_terkirim' => false, 'periode' => 'Bulanan']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendAlos'],
                ['status_terkirim' => true, 'periode' => 'Bulanan']
            );
        }
    }

    public function sendBto($tanggal)
    {
        $data = BorController::bto($tanggal);

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/layanan/bto', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $data->tgl_transaksi,
                    'bto' => $data->bto,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendBto'],
                    ['status_terkirim' => false, 'periode' => 'Bulanan']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendBto'],
                ['status_terkirim' => true, 'periode' => 'Bulanan']
            );
        }
    }

    public function sendDokterSpesialis($tanggal)
    {
        $data = SDMController::profesi('DOKTER-SPESIALIS');

        // dd($data, $tanggal);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/sdm/dokter_spesialis', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $tanggal,
                    'pns' => $data->pns,
                    'pppk' => $data->pppk,
                    'non_pns_tetap' => $data->non_pns_tetap,
                    'kontrak' => $data->kontrak
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendDokterSpesialis'],
                    ['status_terkirim' => false, 'periode' => 'Semesteran']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendDokterSpesialis'],
                ['status_terkirim' => true, 'periode' => 'Semesteran']
            );
        }
    }

    public function sendDokterGigi($tanggal)
    {
        $data = SDMController::profesi('DOKTER-GIGI');

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/sdm/dokter_gigi', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $tanggal,
                    'pns' => $data->pns,
                    'pppk' => $data->pppk,
                    'non_pns_tetap' => $data->non_pns_tetap,
                    'kontrak' => $data->kontrak
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendDokterGigi'],
                    ['status_terkirim' => false, 'periode' => 'Semesteran']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendDokterGigi'],
                ['status_terkirim' => true, 'periode' => 'Semesteran']
            );
        }
    }

    public function sendDokterUmum($tanggal)
    {
        $data = SDMController::profesi('DOKTER-UMUM');

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/sdm/dokter_umum', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $tanggal,
                    'pns' => $data->pns,
                    'pppk' => $data->pppk,
                    'non_pns_tetap' => $data->non_pns_tetap,
                    'kontrak' => $data->kontrak
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendDokterUmum'],
                    ['status_terkirim' => false, 'periode' => 'Semesteran']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendDokterUmum'],
                ['status_terkirim' => true, 'periode' => 'Semesteran']
            );
        }
    }

    public function sendPerawat($tanggal)
    {
        $data = SDMController::profesi('PERAWAT');

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/sdm/perawat', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $tanggal,
                    'pns' => $data->pns,
                    'pppk' => $data->pppk,
                    'non_pns_tetap' => $data->non_pns_tetap,
                    'kontrak' => $data->kontrak
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendPerawat'],
                    ['status_terkirim' => false, 'periode' => 'Semesteran']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendPerawat'],
                ['status_terkirim' => true, 'periode' => 'Semesteran']
            );
        }
    }

    public function sendBidan($tanggal)
    {
        $data = SDMController::profesi('BIDAN');

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/sdm/bidan', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $tanggal,
                    'pns' => $data->pns,
                    'pppk' => $data->pppk,
                    'non_pns_tetap' => $data->non_pns_tetap,
                    'kontrak' => $data->kontrak
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendBidan'],
                    ['status_terkirim' => false, 'periode' => 'Semesteran']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendBidan'],
                ['status_terkirim' => true, 'periode' => 'Semesteran']
            );
        }
    }

    public function sendPranataLab($tanggal)
    {
        $data = SDMController::profesi('PRANATA-LABORATORIUM');

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/sdm/pranata_laboratorium', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $tanggal,
                    'pns' => $data->pns,
                    'pppk' => $data->pppk,
                    'non_pns_tetap' => $data->non_pns_tetap,
                    'kontrak' => $data->kontrak
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendPranataLab'],
                    ['status_terkirim' => false, 'periode' => 'Semesteran']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendPranataLab'],
                ['status_terkirim' => true, 'periode' => 'Semesteran']
            );
        }
    }

    public function sendRadiographer($tanggal)
    {
        $data = SDMController::profesi('RADIOGRAPHER');

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/sdm/radiographer', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $tanggal,
                    'pns' => $data->pns,
                    'pppk' => $data->pppk,
                    'non_pns_tetap' => $data->non_pns_tetap,
                    'kontrak' => $data->kontrak
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendRadiographer'],
                    ['status_terkirim' => false, 'periode' => 'Semesteran']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendRadiographer'],
                ['status_terkirim' => true, 'periode' => 'Semesteran']
            );
        }
    }

    public function sendNutrisionist($tanggal)
    {
        $data = SDMController::profesi('NUTRITIONIST');

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/sdm/nutritionist', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $tanggal,
                    'pns' => $data->pns,
                    'pppk' => $data->pppk,
                    'non_pns_tetap' => $data->non_pns_tetap,
                    'kontrak' => $data->kontrak
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendNutrisionist'],
                    ['status_terkirim' => false, 'periode' => 'Semesteran']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendNutrisionist'],
                ['status_terkirim' => true, 'periode' => 'Semesteran']
            );
        }
    }

    public function sendFisioterapis($tanggal)
    {
        $data = SDMController::profesi('FISIOTERAPIS');

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/sdm/fisioterapis', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $tanggal,
                    'pns' => $data->pns,
                    'pppk' => $data->pppk,
                    'non_pns_tetap' => $data->non_pns_tetap,
                    'kontrak' => $data->kontrak
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendFisioterapis'],
                    ['status_terkirim' => false, 'periode' => 'Semesteran']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendFisioterapis'],
                ['status_terkirim' => true, 'periode' => 'Semesteran']
            );
        }
    }

    public function sendPharmacist($tanggal)
    {
        $data = SDMController::profesi('PHARMACIST');

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/sdm/pharmacist', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $tanggal,
                    'pns' => $data->pns,
                    'pppk' => $data->pppk,
                    'non_pns_tetap' => $data->non_pns_tetap,
                    'kontrak' => $data->kontrak
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendPharmacist'],
                    ['status_terkirim' => false, 'periode' => 'Semesteran']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendPharmacist'],
                ['status_terkirim' => true, 'periode' => 'Semesteran']
            );
        }
    }

    public function sendProfessionalLainnya($tanggal)
    {
        $data = SDMController::profesi('PROFESIONAL-LAIN');

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/sdm/profesional_lain', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $tanggal,
                    'pns' => $data->pns,
                    'pppk' => $data->pppk,
                    'non_pns_tetap' => $data->non_pns_tetap,
                    'kontrak' => $data->kontrak
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendProfessionalLainnya'],
                    ['status_terkirim' => false, 'periode' => 'Semesteran']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendProfessionalLainnya'],
                ['status_terkirim' => true, 'periode' => 'Semesteran']
            );
        }
    }

    public function sendNonMedis($tanggal)
    {
        $data = SDMController::profesi('NON-MEDIS');

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/sdm/non_medis', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $tanggal,
                    'pns' => $data->pns,
                    'pppk' => $data->pppk,
                    'non_pns_tetap' => $data->non_pns_tetap,
                    'kontrak' => $data->kontrak
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendNonMedis'],
                    ['status_terkirim' => false, 'periode' => 'Semesteran']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendNonMedis'],
                ['status_terkirim' => true, 'periode' => 'Semesteran']
            );
        }
    }

    public function sendNonMedisAdmin($tanggal)
    {
        $data = SDMController::profesi('NON-MEDIS-ADMINISTRASI');

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/sdm/non_medis_administrasi', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $tanggal,
                    'pns' => $data->pns,
                    'pppk' => $data->pppk,
                    'non_pns_tetap' => $data->non_pns_tetap,
                    'kontrak' => $data->kontrak,
                    'keterangan' => 'umum, keuangan, sdm, humas, bmn'
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendNonMedisAdmin'],
                    ['status_terkirim' => false, 'periode' => 'Semesteran']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendNonMedisAdmin'],
                ['status_terkirim' => true, 'periode' => 'Semesteran']
            );
        }
    }

    public function sendSanitarian($tanggal)
    {
        $data = SDMController::profesi('SANITARIAN');

        // dd($data);
        //Kirim data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        try {
            $response = $client->request('POST', 'ws/kesehatan/sdm/sanitarian', [
                'headers' => [
                    'token' => session('tokenBios'),
                ],
                'form_params' => [
                    'tgl_transaksi' => $tanggal,
                    'pns' => $data->pns,
                    'pppk' => $data->pppk,
                    'non_pns_tetap' => $data->non_pns_tetap,
                    'kontrak' => $data->kontrak
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());

                // dd($test, 'Send data bor error');
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendSanitarian'],
                    ['status_terkirim' => false, 'periode' => 'Semesteran']
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendSanitarian'],
                ['status_terkirim' => true, 'periode' => 'Semesteran']
            );
        }
    }
}
