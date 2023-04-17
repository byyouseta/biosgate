<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'BIOS facelift');
        session()->put('anak', 'Data Statistik');
        session()->forget('cucu');

        $tanggal = Carbon::now()->format('Y-m-05');

        //data Inap Tanggal sesuai tanggal
        $bor = BorController::bor($tanggal);
        $alos = BorController::alos($tanggal);
        $toi = BorController::toi($tanggal);
        $bto = BorController::bto($tanggal);

        // $awalBulanLalu = Carbon::parse($tanggal)->subMonth()->startOfMonth();
        // $akhirBulanLalu = Carbon::parse($tanggal)->subMonth()->endOfMonth();

        // $data = DB::connection('mysqlkhanza')->table('kamar_inap')
        //     // ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
        //     ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
        //     ->select('kamar_inap.no_rawat', 'kamar_inap.tgl_masuk', 'kamar_inap.tgl_keluar', 'kamar.kelas', 'kamar_inap.lama')
        //     ->whereDate('kamar_inap.tgl_masuk', '<=', $akhirBulanLalu)
        //     ->whereDate('kamar_inap.tgl_masuk', '>=', $awalBulanLalu)
        //     ->orWhere(function ($query) use ($awalBulanLalu) {
        //         $query->where('kamar_inap.tgl_masuk', '<', $awalBulanLalu)
        //             ->where('kamar_inap.tgl_keluar', '>', $awalBulanLalu);
        //     })
        //     ->orderBy('kamar_inap.no_rawat', 'ASC')
        //     ->groupBy('kamar_inap.no_rawat')
        //     ->get();

        // foreach ($data as $dataPasien) {
        //     $awal = DB::connection('mysqlkhanza')->table('kamar_inap')
        //         ->select('kamar_inap.no_rawat', 'kamar_inap.tgl_masuk')
        //         ->where('kamar_inap.no_rawat', $dataPasien->no_rawat)
        //         ->orderBy('kamar_inap.tgl_masuk', 'ASC')
        //         ->first();
        //     $akhir = DB::connection('mysqlkhanza')->table('kamar_inap')
        //         ->select('kamar_inap.no_rawat', 'kamar_inap.tgl_keluar')
        //         ->where('kamar_inap.no_rawat', $dataPasien->no_rawat)
        //         ->orderBy('kamar_inap.tgl_keluar', 'DESC')
        //         ->first();
        //     if ($dataPasien->no_rawat == '2022/12/29/000244') {
        //         dd($awal, $akhir);
        //     }
        // }

        return view('bios.layanan_bor', compact('bor', 'alos', 'toi', 'bto'));
    }

    public function cari(Request $request)
    {
        session()->put('ibu', 'BIOS facelift');
        session()->put('anak', 'Data Statistik');
        session()->forget('cucu');

        $now = Carbon::now();
        $tanggal = $request->get('tanggal');
        if (new Carbon($tanggal) > $now) {
            Session::flash('error', 'Tanggal yang diminta melebihi hari ini!');
            return redirect()->back()->withInput();
        }
        $tanggal = Carbon::parse($tanggal)->subMonth()->format('Y-m-05');
        // dd($tanggal);


        //data Inap Tanggal sesuai tanggal
        $bor = BorController::bor($tanggal);
        $alos = BorController::alos($tanggal);
        $toi = BorController::toi($tanggal);
        $bto = BorController::bto($tanggal);

        // dd($alos);

        return view('bios.layanan_bor', compact('bor', 'alos', 'toi', 'bto'));
    }

    public static function bor($tanggal)
    {
        $jmlTT = BorController::jmlTT();

        $HP = BorController::HP($tanggal);

        $jmlHari = Carbon::parse($tanggal)->subMonth()->daysInMonth;

        $nilaiBor = number_format($HP * 100 / ($jmlTT * $jmlHari), 2);

        // dd($HP, $jmlTT, $jmlHari);

        $arrayBor = [
            'bor' => $nilaiBor,
            'tgl_transaksi' => $tanggal
        ];

        $bor = json_decode(json_encode($arrayBor));

        // dd($bor);

        return $bor;
    }

    public static function alos($tanggal)
    {
        $pasien_keluar = BorController::PasienKeluar($tanggal);

        $lamaDirawat = 0;
        $jmlPasienKeluar = count($pasien_keluar);

        foreach ($pasien_keluar as $data) {
            // dd($data);
            $lamaDirawat = $lamaDirawat + intval($data['lama']);
        }

        // dd($jmlPasienKeluar, $lamaDirawat);

        if (($lamaDirawat > 0) and ($jmlPasienKeluar > 0)) {
            $nilaiAlos = number_format($lamaDirawat / $jmlPasienKeluar, 2);
        } else {
            $nilaiAlos = number_format(0, 2);
        }

        $arrayAlos = [
            'alos' => $nilaiAlos,
            'tgl_transaksi' => $tanggal
        ];

        $alos = json_decode(json_encode($arrayAlos));

        // dd($lamaDirawat, $jmlPasienKeluar);

        return $alos;
    }

    public static function toi($tanggal)
    {
        $jmlTT = BorController::jmlTT();
        $HP = BorController::HP($tanggal);

        $pasien_keluar = count(BorController::PasienKeluar($tanggal));
        $now = new Carbon($tanggal);
        $jml_hari = $now->subMonth()->daysInMonth;

        // dd($jmlTT, $HP, $pasien_keluar, $jml_hari);

        // if ($pasien_keluar > 0) {
        //     $nilaiToi = number_format(($jmlTT - $HP) / $pasien_keluar, 2);
        // } else {
        //     $nilaiToi = number_format(0, 2);
        // }
        $nilaiToi = number_format((($jmlTT * $jml_hari) - $HP) / $pasien_keluar, 2);

        $arrayToi = [
            'toi' => $nilaiToi,
            'tgl_transaksi' => $tanggal
        ];

        $toi = json_decode(json_encode($arrayToi));

        // dd($alos);

        return $toi;
    }

    public static function bto($tanggal)
    {
        $pasien_keluar = count(BorController::PasienKeluar($tanggal));
        $jmlTT = BorController::jmlTT();

        $nilaiBto = number_format($pasien_keluar / $jmlTT, 2);

        $arrayBto = [
            'bto' => $nilaiBto,
            'tgl_transaksi' => $tanggal
        ];

        $bto = json_decode(json_encode($arrayBto));

        // dd($alos);

        return $bto;
    }

    public static function jmlTT()
    {
        // $data = DB::connection('mysqlkhanza')->table('kamar')
        //     ->select('kamar.kd_kamar', 'kamar.statusdata')
        //     ->where('kamar.statusdata', '1')
        //     ->count();

        $data = 104;

        return $data;
    }

    public static function HP($tanggal)
    {
        // dd($tanggal);
        $awalBulanLalu = Carbon::parse($tanggal)->subMonth()->startOfMonth();
        $akhirBulanLalu = Carbon::parse($tanggal)->subMonth()->endOfMonth();
        // dd($awalBulanLalu, $akhirBulanLalu, $tanggal);
        $HP = DB::connection('mysqlkhanza')->table('kamar_inap')
            // ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('kamar_inap.no_rawat', 'kamar_inap.tgl_masuk', 'kamar_inap.tgl_keluar', 'kamar.kelas', 'kamar_inap.lama')
            ->whereDate('kamar_inap.tgl_masuk', '<=', $akhirBulanLalu)
            ->whereDate('kamar_inap.tgl_masuk', '>=', $awalBulanLalu)
            ->orWhere(function ($query) use ($awalBulanLalu) {
                $query->where('kamar_inap.tgl_masuk', '<', $awalBulanLalu)
                    ->where('kamar_inap.tgl_keluar', '>', $awalBulanLalu);
            })
            //pengaturan dimana no_rawat terhubung dikalkulasi ulang
            ->groupBy('kamar_inap.no_rawat')
            ->orderBy('kamar_inap.no_rawat', 'ASC')
            ->get();
        $hari = 0;
        $data_pasien = [];
        foreach ($HP as $index => $data) {
            //pengaturan dimana no_rawat terhubung dikalkulasi ulang
            $cek_tglMasuk = DB::connection('mysqlkhanza')->table('kamar_inap')
                ->select('kamar_inap.no_rawat', 'kamar_inap.tgl_masuk', 'kamar_inap.tgl_keluar')
                ->where('kamar_inap.no_rawat', $data->no_rawat)
                ->orderBy('kamar_inap.tgl_masuk', 'ASC')
                ->first();
            $cek_tglKeluar = DB::connection('mysqlkhanza')->table('kamar_inap')
                ->select('kamar_inap.no_rawat', 'kamar_inap.tgl_masuk', 'kamar_inap.tgl_keluar', 'kamar_inap.jam_masuk')
                ->where('kamar_inap.no_rawat', $data->no_rawat)
                ->orderBy('kamar_inap.tgl_masuk', 'DESC')
                ->orderBy('kamar_inap.jam_masuk', 'DESC')
                ->first();

            // dd($HP, $cek_tglMasuk, $cek_tglKeluar);

            // $no = $index + 1;

            $tgl_masuk = $tgl_keluar = null;
            $masuk = new Carbon($cek_tglMasuk->tgl_masuk);
            if ($masuk <  $awalBulanLalu) {
                $tgl_masuk = Carbon::parse($awalBulanLalu)->format('Y-m-d');
                $masuk = new Carbon($tgl_masuk);
                // dd('kecil', $masuk, $awalBulanLalu, $tgl_masuk);
            } else {
                $tgl_masuk = Carbon::parse($masuk)->format('Y-m-d');
                $masuk = new Carbon($tgl_masuk);
                // dd('besar', $masuk, $awalBulanLalu, $tgl_masuk);
            }

            $keluar = new Carbon($cek_tglKeluar->tgl_keluar);
            if ($keluar > $akhirBulanLalu) {
                $tgl_keluar = Carbon::parse($keluar)->format('Y-m-d');

                $keluar1 = new Carbon($akhirBulanLalu);
                $keluar = $keluar1->addDay();
                $tgl_keluar = Carbon::parse($keluar)->format('Y-m-d');

                // dd($keluar, $tgl_keluar);
            } else {
                $tgl_keluar = Carbon::parse($keluar)->format('Y-m-d');

                $keluar = new Carbon($tgl_keluar);
            }


            $selisih = $keluar->diff($masuk);
            $days = $selisih->format('%a');
            $data_pasien[$index]['no_rawat'] = $data->no_rawat;
            $data_pasien[$index]['tgl_masuk_real'] = $cek_tglMasuk->tgl_masuk;
            $data_pasien[$index]['tgl_masuk'] = $tgl_masuk;
            $data_pasien[$index]['tgl_keluar_real'] = $cek_tglKeluar->tgl_keluar;
            $data_pasien[$index]['tgl_keluar'] = $tgl_keluar;

            // dd($masuk, $keluar, $selisih, $days);
            if ($days < 1) {
                $hari = $hari + 1;
                $data_pasien[$index]['lama'] = 1;
                // $data->tempHari = 1;
                // } elseif ($days > 350) {
                // $data->tempHari = 1;
                // $hari = $hari + 1;
            } elseif ($days >= 1) {
                // dd($days);
                // $data->tempHari = $days;
                $hari = $hari + intval($days);
                $data_pasien[$index]['lama'] = $days;
            }
        }
        // dd($HP, $hari, $data_pasien);

        return $hari;
    }

    public static function PasienKeluar($tanggal)
    {
        $awalBulanLalu = Carbon::parse($tanggal)->subMonth()->startOfMonth();
        $akhirBulanLalu = Carbon::parse($tanggal)->subMonth()->endOfMonth();

        $pasien_keluar = DB::connection('mysqlkhanza')->table('kamar_inap')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('kamar_inap.no_rawat', 'kamar_inap.lama', 'kamar_inap.tgl_masuk', 'kamar_inap.tgl_keluar', 'kamar.kelas')
            ->whereDate('kamar_inap.tgl_keluar', '>=', $awalBulanLalu)
            ->whereDate('kamar_inap.tgl_keluar', '<=', $akhirBulanLalu)
            ->groupBy('kamar_inap.no_rawat')
            ->orderBy('kamar_inap.no_rawat', 'ASC')
            ->get();
        $fix_data = [];
        $no = $hari = 0;

        foreach ($pasien_keluar as $data_keluar) {
            $cek = DB::connection('mysqlkhanza')->table('kamar_inap')
                ->select('kamar_inap.no_rawat', 'kamar_inap.tgl_masuk', 'kamar_inap.tgl_keluar', 'kamar_inap.jam_masuk')
                ->where('kamar_inap.no_rawat', $data_keluar->no_rawat)
                ->orderBy('kamar_inap.tgl_masuk', 'DESC')
                ->orderBy('kamar_inap.jam_masuk', 'DESC')
                ->first();
            if ($cek->tgl_keluar <= $akhirBulanLalu) {
                $cek_masuk = DB::connection('mysqlkhanza')->table('kamar_inap')
                    ->select('kamar_inap.no_rawat', 'kamar_inap.tgl_masuk', 'kamar_inap.tgl_keluar')
                    ->where('kamar_inap.no_rawat', $data_keluar->no_rawat)
                    ->orderBy('kamar_inap.tgl_masuk', 'ASC')
                    ->first();
                $fix_data[$no]['no_rawat'] = $cek->no_rawat;
                $fix_data[$no]['tgl_masuk'] = $cek_masuk->tgl_masuk;
                $fix_data[$no]['tgl_keluar'] = $cek->tgl_keluar;
                $masuk = new Carbon($cek_masuk->tgl_masuk);
                $keluar = new Carbon($cek->tgl_keluar);
                $selisih = $keluar->diff($masuk);
                $days = $selisih->format('%a');
                if ($days < 1) {
                    $hari = $hari + 1;
                    $fix_data[$no]['lama'] = 1;
                    // $data->tempHari = 1;
                    // } elseif ($days > 350) {
                    // $data->tempHari = 1;
                    // $hari = $hari + 1;
                } elseif ($days >= 1) {
                    // dd($days);
                    // $data->tempHari = $days;
                    $hari = $hari + intval($days);
                    $fix_data[$no]['lama'] = $days;
                }
                $no++;
            }
        }


        // dd(json_decode(json_encode($fix_data)), $fix_data, $pasien_keluar, $days);
        $pasien_keluar = $fix_data;

        // dd($pasien_keluar, $hari);

        return $pasien_keluar;
    }
}
