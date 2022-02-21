<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'Data Transaksi');
        session()->put('anak', 'Data Statistik');

        //data Inap Tanggal sesuai tanggal
        $bor = BorController::bor('2022-01-03');
        $alos = BorController::alos('2022-01-03');
        $toi = BorController::toi('2022-01-03');
        $bto = BorController::bto('2022-01-03');

        // dd($alos);

        return view('layanan_bor', compact('bor', 'alos', 'toi', 'bto'));
    }

    public function cari(Request $request)
    {
        $tanggal = $request->get('tanggal');

        //data Inap Tanggal sesuai tanggal
        $bor = BorController::bor($tanggal);
        $alos = BorController::alos($tanggal);
        $toi = BorController::toi($tanggal);
        $bto = BorController::bto($tanggal);

        // dd($alos);

        return view('layanan_bor', compact('bor', 'alos', 'toi', 'bto'));
    }

    public function bor($tanggal)
    {
        $jmlTT = BorController::jmlTT();

        //Hari Perawatan adalah Jumlah Pasien by Mas Guruh
        // $HP = DB::connection('mysqlkhanza')->table('kamar_inap')
        //     ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
        //     ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
        //     ->select('kamar_inap.no_rawat', 'kamar_inap.tgl_masuk', 'kamar_inap.tgl_keluar', 'kamar.kelas')
        //     ->whereDate('kamar_inap.tgl_keluar', '>=', $tanggal)
        //     ->whereDate('kamar_inap.tgl_masuk', '<=', $tanggal)
        //     ->count();

        $HP = BorController::HP($tanggal);

        $nilaiBor = number_format($HP * 100 / $jmlTT, 2);

        $arrayBor = [
            'bor' => $nilaiBor,
            'tgl_transaksi' => $tanggal
        ];

        $bor = json_decode(json_encode($arrayBor));

        // dd($bor);

        return $bor;
    }

    public function alos($tanggal)
    {
        $pasien_keluar = BorController::PasienKeluar($tanggal);

        $lamaDirawat = 0;
        $jmlPasienKeluar = $pasien_keluar->count();

        foreach ($pasien_keluar as $data) {
            $lamaDirawat = $lamaDirawat + $data->lama;
        }

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

        // dd($alos);

        return $alos;
    }

    public function toi($tanggal)
    {
        $jmlTT = BorController::jmlTT();
        $HP = BorController::HP($tanggal);

        $pasien_keluar = BorController::PasienKeluar($tanggal)->count();

        // dd($jmlTT, $HP, $pasien_keluar);

        if ($pasien_keluar > 0) {
            $nilaiToi = number_format(($jmlTT - $HP) / $pasien_keluar, 2);
        } else {
            $nilaiToi = number_format(0, 2);
        }

        $arrayToi = [
            'toi' => $nilaiToi,
            'tgl_transaksi' => $tanggal
        ];

        $toi = json_decode(json_encode($arrayToi));

        // dd($alos);

        return $toi;
    }

    public function bto($tanggal)
    {
        $pasien_keluar = BorController::PasienKeluar($tanggal)->count();
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

    public function jmlTT()
    {
        $data = DB::connection('mysqlkhanza')->table('kamar')
            ->select('kamar.kd_kamar')
            ->count();

        return $data;
    }

    public function HP($tanggal)
    {
        $HP = DB::connection('mysqlkhanza')->table('kamar_inap')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('kamar_inap.no_rawat', 'kamar_inap.tgl_masuk', 'kamar_inap.tgl_keluar', 'kamar.kelas')
            ->whereDate('kamar_inap.tgl_keluar', '>=', $tanggal)
            ->whereDate('kamar_inap.tgl_masuk', '<=', $tanggal)
            ->count();

        return $HP;
    }

    public function PasienKeluar($tanggal)
    {
        $pasien_keluar = DB::connection('mysqlkhanza')->table('kamar_inap')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('kamar_inap.no_rawat', 'kamar_inap.lama', 'kamar_inap.tgl_keluar', 'kamar.kelas')
            ->whereDate('kamar_inap.tgl_keluar', '=', $tanggal)
            ->get();

        return $pasien_keluar;
    }
}
