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
        // $spesialis = SDMController::spesialis();
        // $drg = SDMController::drgigi();
        // $umum = SDMController::umum();
        $spesialis = SDMController::profesi('DOKTER-SPESIALIS');
        $drg = SDMController::profesi('DOKTER-GIGI');
        $umum = SDMController::profesi('DOKTER-UMUM');
        $perawat = SDMController::profesi('PERAWAT');
        $bidan = SDMController::profesi('BIDAN');
        $laborat = SDMController::profesi('PRANATA-LABORATORIUM');
        $radio = SDMController::profesi('RADIOGRAFER');
        $nutrision = SDMController::profesi('NUTRITIONIST');
        $fisio = SDMController::profesi('FISIOTERAPIS');
        $farmasi = SDMController::profesi('PHARMACIST');
        $profesionallain = SDMController::profesi('PROFESIONAL-LAIN');
        $nonmedis = SDMController::profesi('NON-MEDIS');

        // dd($perawat);

        return view('layanan_sdm', compact('spesialis', 'drg', 'umum', 'perawat', 'bidan', 'laborat', 'radio', 'nutrision', 'fisio', 'farmasi', 'profesionallain', 'nonmedis'));
    }

    public function profesi($profesi)
    {
        // $tanggal = Carbon::now()->format('Y-m-d');

        $data = DB::connection('mysqlsimadam')->table('riwayat_data_utama')
            ->join('tabel_status_pegawai', 'tabel_status_pegawai.kd_status', '=', 'riwayat_data_utama.kd_status_pegawai')
            // ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('riwayat_data_utama.nip', 'riwayat_data_utama.nama', 'riwayat_data_utama.nama_profesi', 'tabel_status_pegawai.nama_status')
            ->where('riwayat_data_utama.nama_profesi', '=', $profesi)
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


        $data = json_decode(json_encode($array));

        // dd($spesialis);

        return $data;
    }
}
