<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SDMController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'Data Transaksi');
        session()->put('anak', 'Data SDM');

        //data Pegawai
        $spesialis = SDMController::spesialis();
        $drg = SDMController::drgigi();
        $umum = SDMController::umum();

        // dd($umum);

        return view('layanan_sdm', compact('spesialis', 'drg', 'umum'));
    }

    public function spesialis()
    {
        // $tanggal = Carbon::now()->format('Y-m-d');

        $data = DB::connection('mysqlsimadam')->table('riwayat_data_utama')
            ->join('tabel_status_pegawai', 'tabel_status_pegawai.kd_status', '=', 'riwayat_data_utama.kd_status_pegawai')
            // ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('riwayat_data_utama.nip', 'riwayat_data_utama.nama', 'riwayat_data_utama.gelar_belakang', 'tabel_status_pegawai.nama_status')
            ->where('riwayat_data_utama.gelar_belakang', 'like', 'Sp.%')
            ->orderBy('tabel_status_pegawai.nama_status', 'asc')
            ->get();

        // dd($data);

        $pns = $tetap = $kontrak = 0;

        foreach ($data as $pegawai) {
            if (($pegawai->nama_status == 'PNS') or ($pegawai->nama_status == 'CPNS')) {
                $pns++;
            } elseif ($pegawai->nama_status == 'BLU') {
                $tetap++;
            } elseif ($pegawai->nama_status == 'MITRA') {
                $kontrak++;
            }
        }

        $array = [
            'tgl_transaksi' => Carbon::now()->format('Y-m-d'),
            'pns' => $pns,
            'non_pns_tetap' => $tetap,
            'kontrak' => $kontrak
        ];


        $spesialis = json_decode(json_encode($array));

        // dd($spesialis);

        return $spesialis;
    }

    public function drgigi()
    {
        // $tanggal = Carbon::now()->format('Y-m-d');

        $data = DB::connection('mysqlsimadam')->table('riwayat_data_utama')
            ->join('tabel_status_pegawai', 'tabel_status_pegawai.kd_status', '=', 'riwayat_data_utama.kd_status_pegawai')
            // ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('riwayat_data_utama.nip', 'riwayat_data_utama.nama', 'riwayat_data_utama.gelar_depan', 'tabel_status_pegawai.nama_status')
            ->where('riwayat_data_utama.gelar_depan', 'like', 'drg%')
            ->orderBy('tabel_status_pegawai.nama_status', 'asc')
            ->get();

        // dd($data);

        $pns = $tetap = $kontrak = 0;

        foreach ($data as $pegawai) {
            if (($pegawai->nama_status == 'PNS') or ($pegawai->nama_status == 'CPNS')) {
                $pns++;
            } elseif ($pegawai->nama_status == 'BLU') {
                $tetap++;
            } elseif ($pegawai->nama_status == 'MITRA') {
                $kontrak++;
            }
        }

        $array = [
            'tgl_transaksi' => Carbon::now()->format('Y-m-d'),
            'pns' => $pns,
            'non_pns_tetap' => $tetap,
            'kontrak' => $kontrak
        ];


        $drg = json_decode(json_encode($array));

        // dd($spesialis);

        return $drg;
    }

    public function umum()
    {
        // $tanggal = Carbon::now()->format('Y-m-d');

        $data = DB::connection('mysqlsimadam')->table('riwayat_data_utama')
            ->join('tabel_status_pegawai', 'tabel_status_pegawai.kd_status', '=', 'riwayat_data_utama.kd_status_pegawai')
            // ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('riwayat_data_utama.nip', 'riwayat_data_utama.nama', 'riwayat_data_utama.gelar_depan', 'riwayat_data_utama.gelar_belakang', 'tabel_status_pegawai.nama_status')
            ->where('riwayat_data_utama.gelar_depan', '=', 'dr')
            ->where('riwayat_data_utama.gelar_belakang', 'not like', '%Sp.%')
            ->orderBy('tabel_status_pegawai.nama_status', 'asc')
            ->get();

        // dd($data);

        $pns = $tetap = $kontrak = 0;

        foreach ($data as $pegawai) {
            if (($pegawai->nama_status == 'PNS') or ($pegawai->nama_status == 'CPNS')) {
                $pns++;
            } elseif ($pegawai->nama_status == 'BLU') {
                $tetap++;
            } elseif ($pegawai->nama_status == 'MITRA') {
                $kontrak++;
            }
        }

        $array = [
            'tgl_transaksi' => Carbon::now()->format('Y-m-d'),
            'pns' => $pns,
            'non_pns_tetap' => $tetap,
            'kontrak' => $kontrak
        ];


        $umum = json_decode(json_encode($array));

        // dd($spesialis);

        return $umum;
    }
}
