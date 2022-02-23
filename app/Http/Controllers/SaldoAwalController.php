<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaldoAwalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'Saldo Awal');
        session()->forget('anak');

        $tgl_keluar = Carbon::now()->format('Y-m-d');

        $data = DB::connection('mysqlkhanza')->table('kamar_inap')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('kamar_inap.no_rawat', 'kamar_inap.lama', 'kamar_inap.tgl_keluar', 'kamar.kelas')
            ->where('kamar_inap.tgl_keluar', '<', $tgl_keluar)
            ->get();

        // dd($data);
        $lamakelas1 = $lamakelas2 = $lamakelas3 = $nonkelas = 0;
        $pasien1 = $pasien2 = $pasien3 = 0;

        foreach ($data as $data) {
            if ($data->kelas == 'Kelas 1') {
                $lamakelas1 = $lamakelas1 + $data->lama;
                $pasien1++;
            } elseif ($data->kelas == 'Kelas 2') {
                $lamakelas2 = $lamakelas2 + $data->lama;
                $pasien2++;
            } elseif ($data->kelas == 'Kelas 3') {
                $lamakelas3 = $lamakelas3 + $data->lama;
                $pasien3++;
            } else {
                $nonkelas = $nonkelas + $data->lama;
            }
        }

        // dd($lamakelas1, $pasien1, $lamakelas2, $pasien2, $lamakelas3, $pasien3);

        $datasaldo = [
            [
                'kd_kelas' => '02',
                'jml_hari' => $lamakelas1,
                'jml_pasien' => $pasien1,
                'tgl_transaksi' => $tgl_keluar
            ],
            [
                'kd_kelas' => '03',
                'jml_hari' => $lamakelas2,
                'jml_pasien' => $pasien2,
                'tgl_transaksi' => $tgl_keluar
            ],
            [
                'kd_kelas' => '04',
                'jml_hari' => $lamakelas3,
                'jml_pasien' => $pasien3,
                'tgl_transaksi' => $tgl_keluar
            ]
        ];
        $saldoawal = json_decode(json_encode($datasaldo));
        // dd($saldoawal);

        return view('saldo_awal', compact('saldoawal'));
    }

    public function set(Request $request)
    {
        session()->put('ibu', 'Saldo Awal');
        // session()->put('anak', 'Detail Indikator');

        $tgl_keluar = $request->get('tanggal');

        $data = DB::connection('mysqlkhanza')->table('kamar_inap')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('kamar_inap.no_rawat', 'kamar_inap.lama', 'kamar_inap.tgl_keluar', 'kamar.kelas')
            ->where('kamar_inap.tgl_keluar', '<', $tgl_keluar)
            ->get();

        // dd($data);
        $lamakelas1 = $lamakelas2 = $lamakelas3 = $nonkelas = 0;
        $pasien1 = $pasien2 = $pasien3 = 0;

        foreach ($data as $data) {
            if ($data->kelas == 'Kelas 1') {
                $lamakelas1 = $lamakelas1 + $data->lama;
                $pasien1++;
            } elseif ($data->kelas == 'Kelas 2') {
                $lamakelas2 = $lamakelas2 + $data->lama;
                $pasien2++;
            } elseif ($data->kelas == 'Kelas 3') {
                $lamakelas3 = $lamakelas3 + $data->lama;
                $pasien3++;
            } else {
                $nonkelas = $nonkelas + $data->lama;
            }
        }

        // dd($lamakelas1, $pasien1, $lamakelas2, $pasien2, $lamakelas3, $pasien3);

        $datasaldo = [
            [
                'kd_kelas' => '02',
                'jml_hari' => $lamakelas1,
                'jml_pasien' => $pasien1,
                'tgl_transaksi' => $tgl_keluar
            ],
            [
                'kd_kelas' => '03',
                'jml_hari' => $lamakelas2,
                'jml_pasien' => $pasien2,
                'tgl_transaksi' => $tgl_keluar
            ],
            [
                'kd_kelas' => '04',
                'jml_hari' => $lamakelas3,
                'jml_pasien' => $pasien3,
                'tgl_transaksi' => $tgl_keluar
            ]
        ];
        $saldoawal = json_decode(json_encode($datasaldo));
        // dd($saldoawal);

        return view('saldo_awal', compact('saldoawal'));
    }

    public static function saldo($tanggal)
    {
        // $tgl_keluar = $request->get('tanggal');

        $data = DB::connection('mysqlkhanza')->table('kamar_inap')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('kamar_inap.no_rawat', 'kamar_inap.lama', 'kamar_inap.tgl_keluar', 'kamar.kelas')
            ->where('kamar_inap.tgl_keluar', '<', $tanggal)
            ->get();

        // dd($data);
        $lamakelas1 = $lamakelas2 = $lamakelas3 = $nonkelas = 0;
        $pasien1 = $pasien2 = $pasien3 = 0;

        foreach ($data as $data) {
            if ($data->kelas == 'Kelas 1') {
                $lamakelas1 = $lamakelas1 + $data->lama;
                $pasien1++;
            } elseif ($data->kelas == 'Kelas 2') {
                $lamakelas2 = $lamakelas2 + $data->lama;
                $pasien2++;
            } elseif ($data->kelas == 'Kelas 3') {
                $lamakelas3 = $lamakelas3 + $data->lama;
                $pasien3++;
            } else {
                $nonkelas = $nonkelas + $data->lama;
            }
        }

        // dd($lamakelas1, $pasien1, $lamakelas2, $pasien2, $lamakelas3, $pasien3);

        $tanggal2 = Carbon::parse($tanggal)->format('Y/m/d');

        $datasaldo = [
            [
                'kelas' => '02',

                'jml_pasien' => $pasien1,
                'jml_hari' => $lamakelas1,
                'tgl_transaksi' => $tanggal2
            ],
            [
                'kelas' => '03',
                'jml_pasien' => $pasien2,
                'jml_hari' => $lamakelas2,
                'tgl_transaksi' => $tanggal2
            ],
            [
                'kelas' => '04',
                'jml_pasien' => $pasien3,
                'jml_hari' => $lamakelas3,
                'tgl_transaksi' => $tanggal2
            ]
        ];
        $saldoawal = json_decode(json_encode($datasaldo));
        // dd($saldoawal);

        return $saldoawal;
    }
}
