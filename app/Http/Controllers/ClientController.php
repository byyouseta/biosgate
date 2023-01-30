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
use phpDocumentor\Reflection\PseudoTypes\False_;

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
        session()->put('ibu', 'Client Saldo Awal');
        session()->forget('anak');

        ClientController::token();

        // $tanggal = Carbon::now()->locale('id')->format('Y-m-d');

        $tanggal = '2023-01-25';

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
        } elseif ($cekRajal->status_terkirim == false) {
            ClientController::sendJaminan($tanggal);
        }

        //data Farmasi sesuai tanggal
        $cekFarmasi = LogResponseBios::where('tanggal', $tanggal)
            ->where('nama_fungsi', 'cekFarmasi')
            ->first();
        if (empty($cekFarmasi)) {
            ClientController::sendFarmasi($tanggal);
        } elseif ($cekFarmasi->status_terkirim == false) {
            ClientController::sendFarmasi($tanggal);
        }


        $dataLog = LogResponseBios::where('tanggal', $tanggal)->get();

        return view('bios.log_client_kesehatan', compact('dataLog'));
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
                        ['status_terkirim' => false]
                    );
                }
            }

            $dataResponse = json_decode($response->getBody());

            // dd($dataResponse);
            if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendRawatInap'],
                    ['status_terkirim' => true]
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
                    ['status_terkirim' => false]
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendIgd'],
                ['status_terkirim' => true]
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
                    ['status_terkirim' => false]
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($data);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendLabSample'],
                ['status_terkirim' => true]
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
                        ['status_terkirim' => false]
                    );
                }
            }

            $dataResponse = json_decode($response->getBody());

            // dd($data);

            if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendLabParameter'],
                    ['status_terkirim' => true]
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
                        ['status_terkirim' => false]
                    );
                }
            }

            $dataResponse = json_decode($response->getBody());

            // dd($data);

            if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendOperasi'],
                    ['status_terkirim' => true]
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
                    ['status_terkirim' => false]
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($data);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendRadiologi'],
                ['status_terkirim' => true]
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
                    ['status_terkirim' => false]
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($data);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendRajal'],
                ['status_terkirim' => true]
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
                        ['status_terkirim' => false]
                    );
                }
            }

            $dataResponse = json_decode($response->getBody());

            // dd($data);

            if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
                $update = LogResponseBios::updateOrCreate(
                    ['tanggal' => $tanggal, 'nama_fungsi' => 'sendRajalPoli'],
                    ['status_terkirim' => true]
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
                    ['status_terkirim' => false]
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($data);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendJaminan'],
                ['status_terkirim' => true]
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
                    ['status_terkirim' => false]
                );
            }
        }

        $dataResponse = json_decode($response->getBody());

        // dd($data);

        if (!empty($dataResponse) and ($dataResponse->status == 'MSG20003')) {
            $update = LogResponseBios::updateOrCreate(
                ['tanggal' => $tanggal, 'nama_fungsi' => 'sendFarmasi'],
                ['status_terkirim' => true]
            );
        }
    }
}
