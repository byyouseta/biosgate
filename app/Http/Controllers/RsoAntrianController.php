<?php

namespace App\Http\Controllers;

use App\RsoAntrian;
use App\RsoTask;
use App\Setting;
use Carbon\Carbon;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RsoAntrianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        session()->put('ibu', 'RS Online');
        session()->put('anak', 'Antrian');
        session()->forget('cucu');

        if (empty($request->get('tanggal'))) {
            $tanggal = Carbon::now()->format('Y-m-d');
            $pencarianTask = Carbon::parse($tanggal)->format('Y/m/d');
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
            $pencarianTask = Carbon::parse($tanggal)->format('Y/m/d');
        }


        $data = RsoAntrian::whereDate('tglPeriksa', $tanggal)
            ->get();

        $dataTask = RsoTask::where('kodeBooking', 'like', "%$pencarianTask%")->get();

        return view('antrian.summary', compact('data', 'dataTask'));
    }

    public function clientAdd()
    {
        session()->put('ibu', 'RS Online');
        session()->put('anak', 'Antrian');
        session()->put('cucu', 'Client Tambah Antrian');
        set_time_limit(0);

        if (session('base_url_antrial') == null) {
            $setting = Setting::where('nama', 'antrian_rso')->first();
            session()->put('base_url_antrian', $setting->base_url);
            session()->put('akun_antrian', $setting->satker);
            session()->put('pass_antrian', $setting->key);
        }

        $tanggalKunjungan = Carbon::now()->format('Y-m-d');
        // $tanggalKunjungan = '2023-01-02';

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select(
                'reg_periksa.no_reg',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.stts_daftar',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'reg_periksa.kd_dokter',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                // 'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli',
                'poliklinik.kd_poli'

            )
            ->where('reg_periksa.status_lanjut', 'Ralan')
            // ->where('reg_periksa.no_rawat', '=', '2023/04/18/000042')
            ->where('reg_periksa.tgl_registrasi', $tanggalKunjungan)
            // ->orWhere('reg_periksa.tgl_registrasi', $kemarin)
            // ->where('poliklinik.kd_poli', '!=', 'IGDK')
            ->where(function ($query) {
                $query->where('poliklinik.kd_poli', '=', 'THT')
                    // ->orWhere('poliklinik.kd_poli', '=', 'THT')
                    ->orWhere('poliklinik.kd_poli', '=', 'NEU');
            })
            ->orderBy('reg_periksa.tgl_registrasi', 'ASC')
            ->get();

        // dd($data);
        $loop = 0;


        foreach ($data as $pasien) {
            $cek = RsoAntrian::where('kodeBooking', $pasien->no_rawat)->first();
            $cekLab = DB::connection('mysqlkhanza')->table('periksa_lab')
                ->select('no_rawat', 'tgl_periksa', 'jam')
                ->where('no_rawat', '=', $pasien->no_rawat)
                ->first();
            $cekRad = DB::connection('mysqlkhanza')->table('periksa_radiologi')
                ->select('no_rawat', 'tgl_periksa', 'jam')
                ->where('no_rawat', '=', $pasien->no_rawat)
                ->first();
            $cekObat = DB::connection('mysqlkhanza')->table('resep_obat')
                ->select('no_rawat', 'tgl_perawatan', 'jam')
                ->where('no_rawat', '=', $pasien->no_rawat)
                ->first();

            if ((empty($cek)) && (empty($cekLab)) && (empty($cekRad)) && (!empty($cekObat))) {

                //Jenis Pasien
                if ($pasien->kd_pj == 'BPJ') {
                    $jenis_pasien = 'JKN';
                } else {
                    $jenis_pasien = 'NON JKN';
                }

                //pasien baru
                if ($pasien->stts_daftar == 'Baru') {
                    $pasien_baru = 1;
                } else {
                    $pasien_baru = 0;
                }

                //kodePoli
                $poli = RsoAntrianController::getPoliBpj($pasien->kd_poli);
                //KodeDokter
                $dokter = RsoAntrianController::getDokterBpj($pasien->kd_dokter);
                //Jam Praktek
                $jadwal = RsoAntrianController::getJadwal($pasien->kd_dokter, $pasien->tgl_registrasi);
                //Kunjungan
                $kunjungan = RsoAntrianController::getKunjungan($pasien->no_rawat);
                $estimasiMulaiDokter = new Carbon($pasien->tgl_registrasi . ' ' . $jadwal->jam_mulai);
                $estimasiAntrian = intval($pasien->no_reg) * 10;
                $estimasiPelayanan = $estimasiMulaiDokter->addMinutes($estimasiAntrian)->timestamp;
                // dd($cek, $pasien, $poli, $dokter, $jadwal, $kunjungan, $estimasiAntrian, $estimasiPelayanan);
                $keterangan = 'Pasien dimohon hadir lebih awal 30 menit dari waktu estimasi pelayanan';

                $dataAntrian = [
                    "kodebooking" => "$pasien->no_rawat",
                    "jenispasien" => "$jenis_pasien",
                    "nik" => "$pasien->ktp_pasien",
                    "kodepoli" => "$poli->kd_poli_bpjs", //ambil
                    "namapoli" => "$poli->nm_poli_bpjs", //ambil
                    "pasienbaru" => "$pasien_baru",
                    "norm" => "$pasien->no_rkm_medis",
                    "tanggalperiksa" => "$pasien->tgl_registrasi",
                    "kodedokter" => "$dokter->kd_dokter_bpjs", //ambil
                    "namadokter" => "$dokter->nm_dokter_bpjs", //ambil
                    "jampraktek" => "$jadwal->jam_mulai", //ambil
                    "jeniskunjungan" => "$kunjungan->asal_rujukan", //ambil
                    "nomorreferensi" => "$kunjungan->no_rujukan", //ambil
                    "nomorantrean" => "$pasien->no_reg",
                    "estimasidilayani" => "$estimasiPelayanan",
                    "keterangan" => "$keterangan"
                ];

                $xtime = Carbon::now()->locale('UTC')->timestamp;
                // dd(json_decode(json_encode($dataAntrian)), $xtime, $setting->base_url);

                $client = new \GuzzleHttp\Client(['base_uri' => $setting->base_url]);
                $response = $client->request('POST', 'index.php/Antrian', [
                    'headers' => [
                        'X-rs-id' => "$setting->satker",
                        'X-Timestamp' => "$xtime",
                        'X-pass' => "$setting->key"
                    ],
                    'json' => $dataAntrian
                ]);

                $data = json_decode($response->getBody());

                $pesan = (array)($data->antrian[0]->message);
                $pesan = $pesan[0];
                $pecahPesan = explode(' ', $pesan);

                // if (in_array("disimpan", $pecahPesan)) {
                //     echo "Got Irix";
                //     dd($data, $pesan, $pecahPesan);
                // } else {
                //     dd('tidak ada');
                // }

                if (!empty($pesan)) {
                    if ((in_array('disimpan', $pecahPesan)) || (in_array('ada.', $pecahPesan))) {
                        $simpan = new RsoAntrian();
                        $simpan->kodeBooking = $pasien->no_rawat;
                        $simpan->jenisPasien = $jenis_pasien;
                        $simpan->nik = $pasien->ktp_pasien;
                        $simpan->kodePoli = $poli->kd_poli_bpjs;
                        $simpan->namaPoli = $poli->nm_poli_bpjs;
                        $simpan->pasienBaru = $pasien_baru;
                        $simpan->noRm = $pasien->no_rkm_medis;
                        $simpan->tglPeriksa = $pasien->tgl_registrasi;
                        $simpan->kodeDokter = $dokter->kd_dokter_bpjs;
                        $simpan->namaDokter = $dokter->nm_dokter_bpjs;
                        $simpan->jamPraktek = $jadwal->jam_mulai;
                        $simpan->jenisKunjungan = $kunjungan->asal_rujukan;
                        $simpan->nomorReferensi = $kunjungan->no_rujukan;
                        $simpan->nomorAntrean = $pasien->no_reg;
                        $simpan->angkaAntrean = '';
                        $simpan->estimasi = $estimasiPelayanan;
                        $simpan->keterangan = $keterangan;
                        $simpan->statusKirim = true;
                        $simpan->save();

                        $task = new RsoTask();
                        $task->kodeBooking = $pasien->no_rawat;
                        $task->statusKirim = false;
                        $task->save();
                    } else {
                        $simpan = new RsoAntrian();
                        $simpan->kodeBooking = $pasien->no_rawat;
                        $simpan->jenisPasien = $jenis_pasien;
                        $simpan->nik = $pasien->ktp_pasien;
                        $simpan->kodePoli = $poli->kd_poli_bpjs;
                        $simpan->namaPoli = $poli->nm_poli_bpjs;
                        $simpan->pasienBaru = $pasien_baru;
                        $simpan->noRm = $pasien->no_rkm_medis;
                        $simpan->tglPeriksa = $pasien->tgl_registrasi;
                        $simpan->kodeDokter = $dokter->kd_dokter_bpjs;
                        $simpan->namaDokter = $dokter->nm_dokter_bpjs;
                        $simpan->jamPraktek = $jadwal->jam_mulai;
                        $simpan->jenisKunjungan = $kunjungan->asal_rujukan;
                        $simpan->nomorReferensi = $kunjungan->no_rujukan;
                        $simpan->nomorAntrean = $pasien->no_reg;
                        $simpan->angkaAntrean = '';
                        $simpan->estimasi = $estimasiPelayanan;
                        $simpan->keterangan = $keterangan;
                        $simpan->statusKirim = false;
                        $simpan->save();

                        $task = new RsoTask();
                        $task->kodeBooking = $pasien->no_rawat;
                        $task->statusKirim = false;
                        $task->save();
                    }
                    $loop++;
                }


                // if ($loop == 10) {
                //     goto Selesai;
                // }
                // dd($data->antrian[0]->message, 'data');
            }
        }
        Selesai:
        //update Task
        $listTask = RsoTask::where('statusKirim', false)
            ->get();
        // dd($listTask);
        foreach ($listTask as $dataTask) {
            if (empty($dataTask->taskid3)) {
                //update sini
                RsoAntrianController::pushTask3($dataTask->kodeBooking);
                // dd($dataTask, $kirim);
            }
            if (empty($dataTask->taskid4)) {
                RsoAntrianController::pushTask4($dataTask->kodeBooking);
            }
            if (empty($dataTask->taskid5)) {
                RsoAntrianController::pushTask5($dataTask->kodeBooking);
            }
            if (empty($dataTask->taskid6)) {
                RsoAntrianController::pushTask6($dataTask->kodeBooking);
            }
            if (empty($dataTask->taskid7)) {
                RsoAntrianController::pushTask7($dataTask->kodeBooking);
            }

            //Task Batal
            if (empty($dataTask->taskid99)) {
                RsoAntrianController::pushTask99($dataTask->kodeBooking);
            }

            //Ceking Tanggal kunjungan kemarin
            $tanggalCek = new Carbon(substr($dataTask->kodeBooking, 0, 10));
            $tanggalSekarang = Carbon::now();
            $tanggalSekarang = new Carbon($tanggalSekarang->format('Y-m-d 00:00:00'));
            if ($tanggalCek->timestamp < $tanggalSekarang->timestamp) {
                $update = RsoTask::where('kodeBooking', $dataTask->kodeBooking)
                    ->first();
                $update->statusKirim = true;
                $update->save();
            }
        }

        // $data = RsoAntrian::whereDate('created_at', Carbon::now())->get();
        // $dataTask = RsoTask::whereDate('created_at', Carbon::now())->get();

        $tanggal = new Carbon($tanggalKunjungan);
        $pencarianTask = Carbon::parse($tanggal)->format('Y/m/d');

        $data = RsoAntrian::whereDate('tglPeriksa', $tanggal)
            ->get();

        $dataTask = RsoTask::where('kodeBooking', 'like', "%$pencarianTask%")->get();

        return view('antrian.client', compact('data', 'dataTask'));
    }

    public function getPoliBpj($id)
    {
        $cek = DB::connection('mysqlkhanza')->table('maping_poli_bpjs')
            ->select('kd_poli_rs', 'kd_poli_bpjs', 'nm_poli_bpjs')
            ->where('kd_poli_rs', '=', $id)
            ->first();

        if (empty($cek)) {
            $cek = DB::connection('mysqlkhanza')->table('poliklinik')
                ->select('kd_poli as kd_poli_bpjs', 'nm_poli as nm_poli_bpjs')
                ->where('kd_poli', '=', $id)
                ->first();

            return $cek;
        }

        return $cek;
    }

    public function getDokterBpj($id)
    {
        $cek = DB::connection('mysqlkhanza')->table('maping_dokter_dpjpvclaim')
            ->select('kd_dokter', 'kd_dokter_bpjs', 'nm_dokter_bpjs')
            ->where('kd_dokter', '=', $id)
            ->first();

        if (empty($cek)) {
            $cek = DB::connection('mysqlkhanza')->table('dokter')
                ->select('kd_dokter as kd_dokter_bpjs', 'nm_dokter as nm_dokter_bpjs')
                ->where('kd_dokter', '=', $id)
                ->first();
        }

        return $cek;
    }

    public function getJadwal($id, $tanggal)
    {
        $hari = new Carbon($tanggal);
        $namaHari = $hari->locale('id')->dayName;
        // dd($hari, $namaHari);
        $cek = DB::connection('mysqlkhanza')->table('jadwal')
            ->select('kd_dokter', 'hari_kerja', 'jam_mulai')
            ->where('kd_dokter', '=', $id)
            ->where('hari_kerja', 'like', $namaHari)
            ->first();

        // dd($cek);
        if (empty($cek)) {
            $cek = [
                'kd_dokter' => $id,
                'jam_mulai' => '08:00:00'
            ];
            $cek = json_decode(json_encode($cek));

            return $cek;
        }

        return $cek;
    }

    public function getKunjungan($id)
    {
        $cek = DB::connection('mysqlkhanza')->table('bridging_sep')
            ->select('no_rawat', 'no_rujukan', 'asal_rujukan')
            ->where('no_rawat', '=', $id)
            ->first();

        // dd($cek);
        if (empty($cek)) {
            $cek = [
                'no_rawat' => $id,
                'asal_rujukan' => 3,
                'no_rujukan' => ''
            ];
        } else {
            if ($cek->asal_rujukan == '1. Faskes 1') {
                $cek = [
                    'no_rawat' => $id,
                    'asal_rujukan' => 1,
                    'no_rujukan' => $cek->no_rujukan
                ];
            } else {
                $cek = [
                    'no_rawat' => $id,
                    'asal_rujukan' => 4,
                    'no_rujukan' => $cek->no_rujukan
                ];
            }
        }

        return json_decode(json_encode($cek));
    }

    public function pushTask3($id)
    {
        // Ambil dari Mutasi Berkas
        // $cek = DB::connection('mysqlkhanza')->table('mutasi_berkas')
        //     ->select('no_rawat', 'dikirim', 'diterima', 'kembali', 'tidakada')
        //     ->where('no_rawat', '=', $id)
        //     ->first();

        //Ambil dari jam regis
        $cek = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->select('no_rawat', 'tgl_registrasi', 'jam_reg')
            ->where('no_rawat', '=', $id)
            ->first();

        if (empty($cek)) {
            return null;
        } else {
            // if ($cek->dikirim != '0000-00-00 00:00:00') {

            //Kirim data
            $xtime = Carbon::now()->locale('UTC')->timestamp;
            $waktu = new Carbon($cek->tgl_registrasi . ' ' . $cek->jam_reg);
            $task3 = $waktu->timestamp;

            // dd($xtime, session('base_url_antrian'), session('akun_antrian'), session('pass_antrian'), $task3);

            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_antrian')]);
            $response = $client->request('POST', 'index.php/Antrian/task', [
                'headers' => [
                    'X-rs-id' => session('akun_antrian'),
                    'X-Timestamp' => "$xtime",
                    'X-pass' => session('pass_antrian')
                ],
                'json' => [
                    "kodebooking" => "$id",
                    "taskid" => 3,
                    "waktu" => "$task3"
                ]
            ]);

            $data = json_decode($response->getBody());

            // dd($data);
            if ((str_contains((string) $data->task[0]->message, (string) 'sudah ada.')) || (str_contains((string) $data->task[0]->message, (string) 'disimpan'))) {
                // dd('masuk');
                $update = RsoTask::where('kodeBooking', $id)
                    ->first();
                $update->taskid3 = $task3;
                $update->save();
            }
            // }
        }
    }

    public function pushTask4($id)
    {
        // dd($id);
        $cek = DB::connection('mysqlkhanza')->table('mutasi_berkas')
            ->select('no_rawat', 'dikirim', 'diterima', 'kembali', 'tidakada')
            ->where('no_rawat', '=', $id)
            ->first();

        if (empty($cek)) {
            return null;
        } else {
            if ($cek->diterima != '0000-00-00 00:00:00') {
                //Kirim data
                $xtime = Carbon::now()->locale('UTC')->timestamp;
                $waktu = new Carbon($cek->diterima);
                $task4 = $waktu->timestamp;

                // dd($xtime, session('base_url_antrian'), session('akun_antrian'), session('pass_antrian'), $task3);

                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_antrian')]);
                $response = $client->request('POST', 'index.php/Antrian/task', [
                    'headers' => [
                        'X-rs-id' => session('akun_antrian'),
                        'X-Timestamp' => "$xtime",
                        'X-pass' => session('pass_antrian')
                    ],
                    'json' => [
                        "kodebooking" => "$id",
                        "taskid" => 4,
                        "waktu" => "$task4"
                    ]
                ]);

                $data = json_decode($response->getBody());

                // dd($data);
                if ((str_contains((string) $data->task[0]->message, (string) 'sudah ada.')) || (str_contains((string) $data->task[0]->message, (string) 'disimpan'))) {
                    // dd('masuk');
                    $update = RsoTask::where('kodeBooking', $id)
                        ->first();
                    $update->taskid4 = $task4;
                    $update->save();
                }
            }
        }
    }

    public function pushTask99($id)
    {
        // dd($id);
        $cek = DB::connection('mysqlkhanza')->table('mutasi_berkas')
            ->select('no_rawat', 'dikirim', 'diterima', 'kembali', 'tidakada')
            ->where('no_rawat', '=', $id)
            ->first();

        if (empty($cek)) {
            return null;
        } else {
            if ($cek->tidakada != '0000-00-00 00:00:00') {
                //Kirim data
                $xtime = Carbon::now()->locale('UTC')->timestamp;
                $waktu = new Carbon($cek->tidakada);
                $task99 = $waktu->timestamp;

                // dd($xtime, session('base_url_antrian'), session('akun_antrian'), session('pass_antrian'), $task3);

                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_antrian')]);
                $response = $client->request('POST', 'index.php/Antrian/task', [
                    'headers' => [
                        'X-rs-id' => session('akun_antrian'),
                        'X-Timestamp' => "$xtime",
                        'X-pass' => session('pass_antrian')
                    ],
                    'json' => [
                        "kodebooking" => "$id",
                        "taskid" => 99,
                        "waktu" => "$task99"
                    ]
                ]);

                $data = json_decode($response->getBody());

                // dd($data);
                if ((str_contains((string) $data->task[0]->message, (string) 'sudah ada.')) || (str_contains((string) $data->task[0]->message, (string) 'disimpan'))) {
                    // dd('masuk');
                    $update = RsoTask::where('kodeBooking', $id)
                        ->first();
                    $update->taskid99 = $task99;
                    $update->statusKirim = true;
                    $update->save();
                }
            }
        }
    }

    public function pushTask5($id)
    {
        $cek = DB::connection('mysqlkhanza')->table('pemeriksaan_ralan')
            ->select('no_rawat', 'tgl_perawatan', 'jam_rawat')
            ->where('no_rawat', '=', $id)
            ->first();
        // dd($id, $cek->tgl_perawatan);


        if (empty($cek)) {
            return null;
        } else {
            if ($cek->tgl_perawatan != '0000-00-00') {
                //Kirim data
                $xtime = Carbon::now()->locale('UTC')->timestamp;
                $waktu = new Carbon($cek->tgl_perawatan . ' ' . $cek->jam_rawat);
                $task5 = $waktu->timestamp;

                // dd($xtime, session('base_url_antrian'), session('akun_antrian'), session('pass_antrian'), $task3);

                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_antrian')]);
                $response = $client->request('POST', 'index.php/Antrian/task', [
                    'headers' => [
                        'X-rs-id' => session('akun_antrian'),
                        'X-Timestamp' => "$xtime",
                        'X-pass' => session('pass_antrian')
                    ],
                    'json' => [
                        "kodebooking" => "$id",
                        "taskid" => 5,
                        "waktu" => "$task5"
                    ]
                ]);

                $data = json_decode($response->getBody());

                // dd($data);
                if ((str_contains((string) $data->task[0]->message, (string) 'sudah ada.')) || (str_contains((string) $data->task[0]->message, (string) 'disimpan'))) {
                    // dd('masuk');
                    $update = RsoTask::where('kodeBooking', $id)
                        ->first();
                    $update->taskid5 = $task5;
                    $update->save();
                }
            }
        }
    }

    public function pushTask6($id)
    {
        $cek = DB::connection('mysqlkhanza')->table('resep_obat')
            ->select('no_rawat', 'tgl_perawatan', 'jam', 'tgl_peresepan', 'jam_peresepan')
            ->where('no_rawat', '=', $id)
            ->first();
        // dd($id, $cek->tgl_perawatan);


        if (empty($cek)) {
            return null;
        } else {
            if ($cek->tgl_peresepan != '0000-00-00') {
                //Kirim data
                $xtime = Carbon::now()->locale('UTC')->timestamp;
                $waktu = new Carbon($cek->tgl_peresepan . ' ' . $cek->jam_peresepan);
                $task6 = $waktu->timestamp;

                // dd($xtime, session('base_url_antrian'), session('akun_antrian'), session('pass_antrian'), $task3);

                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_antrian')]);
                $response = $client->request('POST', 'index.php/Antrian/task', [
                    'headers' => [
                        'X-rs-id' => session('akun_antrian'),
                        'X-Timestamp' => "$xtime",
                        'X-pass' => session('pass_antrian')
                    ],
                    'json' => [
                        "kodebooking" => "$id",
                        "taskid" => 6,
                        "waktu" => "$task6"
                    ]
                ]);

                $data = json_decode($response->getBody());

                // dd($data);
                if ((str_contains((string) $data->task[0]->message, (string) 'sudah ada.')) || (str_contains((string) $data->task[0]->message, (string) 'disimpan'))) {
                    // dd('masuk');
                    $update = RsoTask::where('kodeBooking', $id)
                        ->first();
                    $update->taskid6 = $task6;
                    $update->save();
                }
            }
        }
    }

    public function pushTask7($id)
    {
        $cek = DB::connection('mysqlkhanza')->table('resep_obat')
            ->select('no_rawat', 'tgl_perawatan', 'jam', 'tgl_peresepan', 'jam_peresepan', 'tgl_penyerahan', 'jam_penyerahan')
            ->where('no_rawat', '=', $id)
            ->first();
        // dd($id, $cek->tgl_perawatan, $cek->jam);


        if (empty($cek)) {
            return null;
        } else {
            if ($cek->tgl_penyerahan != '0000-00-00') {

                //Kirim data
                $xtime = Carbon::now()->locale('UTC')->timestamp;
                $waktu = new Carbon($cek->tgl_penyerahan . ' ' . $cek->jam_penyerahan);
                $task7 = $waktu->timestamp;

                // dd($xtime, session('base_url_antrian'), session('akun_antrian'), session('pass_antrian'), $task3);

                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_antrian')]);
                $response = $client->request('POST', 'index.php/Antrian/task', [
                    'headers' => [
                        'X-rs-id' => session('akun_antrian'),
                        'X-Timestamp' => "$xtime",
                        'X-pass' => session('pass_antrian')
                    ],
                    'json' => [
                        "kodebooking" => "$id",
                        "taskid" => 7,
                        "waktu" => "$task7"
                    ]
                ]);

                $data = json_decode($response->getBody());

                // dd($data);
                if ((str_contains((string) $data->task[0]->message, (string) 'sudah ada.')) || (str_contains((string) $data->task[0]->message, (string) 'disimpan'))) {
                    // dd('masuk');
                    $update = RsoTask::where('kodeBooking', $id)
                        ->first();
                    $update->taskid7 = $task7;
                    $update->statusKirim = true;
                    $update->save();
                }
            }
        }
    }
}
