<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'Data Transaksi');
        session()->put('anak', 'Data IKT');

        $tanggal = Carbon::now()->format('Y-m-d');

        //data Inap Tanggal sesuai tanggal
        $visitpagi = VisitController::visit($tanggal, '00:00:00', '10:00:00');
        $visitsiang1 = VisitController::visit($tanggal, '10:00:01', '12:00:00');
        $visitsiang2 = VisitController::visit($tanggal, '12:00:01', '16:00:00');
        $tidakvisit = VisitController::tidakvisit($tanggal);
        $pertama = VisitController::pertama($tanggal);

        // dd($visitpagi, $visitsiang1, $visitsiang2);

        return view('layanan_ikt', compact('visitpagi', 'visitsiang1', 'visitsiang2', 'tidakvisit', 'pertama'));
    }

    public function cari(Request $request)
    {
        session()->put('ibu', 'Data Transaksi');
        session()->put('anak', 'Data IKT');

        $tanggal = $request->get('tanggal');

        //data Inap Tanggal sesuai tanggal
        $visitpagi = VisitController::visit($tanggal, '00:00:00', '10:00:00');
        $visitsiang1 = VisitController::visit($tanggal, '10:00:01', '12:00:00');
        $visitsiang2 = VisitController::visit($tanggal, '12:00:01', '16:00:00');
        $tidakvisit = VisitController::tidakvisit($tanggal);
        $pertama = VisitController::pertama($tanggal);

        // dd($visitpagi, $visitsiang1, $visitsiang2);

        return view('layanan_ikt', compact('visitpagi', 'visitsiang1', 'visitsiang2', 'tidakvisit', 'pertama'));
    }

    public function visit($tanggal, $mulai, $selesai)
    {
        // $tanggal = Carbon::now()->format('Y-m-d');

        $data = DB::connection('mysqlkhanza')->table('rawat_inap_dr')
            ->join('jns_perawatan_inap', 'jns_perawatan_inap.kd_jenis_prw', '=', 'rawat_inap_dr.kd_jenis_prw')
            // ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('rawat_inap_dr.tgl_perawatan', 'rawat_inap_dr.jam_rawat', 'jns_perawatan_inap.nm_perawatan')
            ->where('jns_perawatan_inap.nm_perawatan', 'like', '%Visite%')
            ->whereDate('rawat_inap_dr.tgl_perawatan', '=', $tanggal)
            ->whereTime('rawat_inap_dr.jam_rawat', '>=', $mulai)
            ->whereTime('rawat_inap_dr.jam_rawat', '<=', $selesai)
            ->get();

        // dd($data);

        $array = [
            'jumlah' => $data->count(),
            'tgl_transaksi' => $tanggal
        ];
        $data = json_decode(json_encode($array));

        return $data;
    }

    public function tidakvisit($tanggal)
    {
        $data = DB::connection('mysqlkhanza')->table('kamar_inap')
            // ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->join('dpjp_ranap', 'dpjp_ranap.no_rawat', '=', 'kamar_inap.no_rawat')
            ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('kamar_inap.no_rawat', 'kamar_inap.tgl_masuk', 'kamar_inap.tgl_keluar', 'kamar.kelas', 'dpjp_ranap.kd_dokter')
            ->whereDate('kamar_inap.tgl_masuk', '<=', $tanggal)
            ->whereDate('kamar_inap.tgl_keluar', '>=', $tanggal)
            ->orWhereDate('kamar_inap.tgl_keluar', '=', '0000-00-00')
            ->get();

        // dd($data);
        $tidak_visit = 0;

        foreach ($data as $data2) {
            $cek = DB::connection('mysqlkhanza')->table('rawat_inap_dr')
                ->join('jns_perawatan_inap', 'jns_perawatan_inap.kd_jenis_prw', '=', 'rawat_inap_dr.kd_jenis_prw')
                // ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
                ->select('rawat_inap_dr.tgl_perawatan', 'rawat_inap_dr.no_rawat', 'rawat_inap_dr.kd_dokter', 'rawat_inap_dr.jam_rawat', 'jns_perawatan_inap.nm_perawatan')
                ->where('jns_perawatan_inap.nm_perawatan', 'like', '%Visite%')
                ->where('rawat_inap_dr.no_rawat', '=', $data2->no_rawat)
                ->where('rawat_inap_dr.kd_dokter', '=', $data2->kd_dokter)
                ->whereDate('rawat_inap_dr.tgl_perawatan', '=', $tanggal)
                ->count();

            // dd($cek);
            if ($cek == 0) {
                $tidak_visit++;
            }
        }

        // dd($tidak_visit, $data->count());

        $array = [
            'jumlah' => $tidak_visit,
            'tgl_transaksi' => $tanggal
        ];
        $tidakvisit = json_decode(json_encode($array));

        return $tidakvisit;
    }

    public function pertama($tanggal)
    {
        // $tanggal = Carbon::now()->format('Y-m-d');

        $data = DB::connection('mysqlkhanza')->table('rawat_inap_dr')
            ->join('jns_perawatan_inap', 'jns_perawatan_inap.kd_jenis_prw', '=', 'rawat_inap_dr.kd_jenis_prw')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'rawat_inap_dr.no_rawat')
            ->select('rawat_inap_dr.tgl_perawatan', 'rawat_inap_dr.jam_rawat', 'jns_perawatan_inap.nm_perawatan', 'kamar_inap.tgl_masuk')
            ->where('jns_perawatan_inap.nm_perawatan', 'like', '%Visite%')
            ->whereDate('rawat_inap_dr.tgl_perawatan', '=', $tanggal)
            ->whereDate('kamar_inap.tgl_masuk', '=', $tanggal)
            ->get();

        // dd($data);

        $array = [
            'jumlah' => $data->count(),
            'tgl_transaksi' => $tanggal
        ];
        $pertama = json_decode(json_encode($array));

        // dd($pertama);

        return $pertama;
    }
}
