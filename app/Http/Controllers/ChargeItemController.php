<?php

namespace App\Http\Controllers;

use App\ChargeItemSatuSehat;
use App\Jobs\SendChargeItemJob;
use App\Jobs\SyncChargeItemJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChargeItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Charge Item Satu Sehat');
        session()->forget('cucu');

        if (empty($request->get('awal'))) {
            $awal = Carbon::now();
            $akhir = Carbon::now();
        } else {
            $awal = new Carbon($request->get('awal'));
            $akhir = new Carbon($request->get('akhir'));
        }

        $pasien = DB::connection('mysqlkhanza')
            ->table('reg_periksa as rp')
            ->join('pasien as p', 'p.no_rkm_medis', '=', 'rp.no_rkm_medis')
            ->leftJoin('poliklinik as po', 'po.kd_poli', '=', 'rp.kd_poli')
            ->whereBetween('rp.tgl_registrasi', [$awal, $akhir])
            ->whereNotIn('rp.stts', ['Batal', 'Belum'])
            ->select(
                'rp.no_rawat',
                'rp.no_rkm_medis',
                'p.nm_pasien',
                'rp.tgl_registrasi',
                'rp.jam_reg',
                'rp.stts',
                'rp.status_lanjut',
                'po.nm_poli'
            )
            ->orderBy('rp.tgl_registrasi')
            ->orderBy('rp.jam_reg')
            ->get();

        /*
         * Ambil status Charge Item berdasarkan no_rawat
         */
        $chargeItems = ChargeItemSatuSehat::whereIn(
            'no_rawat',
            $pasien->pluck('no_rawat')
        )
            ->select(
                'no_rawat',
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('no_rawat', 'status')
            ->get()
            ->groupBy('no_rawat');

        /*
         * Tempelkan informasi Charge Item ke data pasien
         */
        foreach ($pasien as $row) {

            $items = $chargeItems->get($row->no_rawat, collect());

            $row->charge_total = $items->sum('total');

            $row->charge_pending = $items
                ->where('status', 'pending')
                ->sum('total');

            $row->charge_sent = $items
                ->where('status', 'sent')
                ->sum('total');

            $row->charge_failed = $items
                ->where('status', 'failed')
                ->sum('total');
        }

        return view('satu_sehat.summaryCharge', compact('pasien'));
    }

    public function sync($no_rawat)
    {
        $no_rawat = decrypt($no_rawat);

        SyncChargeItemJob::dispatch($no_rawat)
            ->onQueue('satusehat');

        return back()->with(
            'success',
            'Proses sinkronisasi untuk ' . $no_rawat . ' telah dimasukkan ke antrean.'
        );
    }

    /**
     * Sync semua kunjungan pada tanggal tertentu
     */
    public function syncAll(Request $request)
    {
        $request->validate([
            'awal' => 'required|date',
            'akhir' => 'required|date|after_or_equal:awal',
        ]);

        $awal = $request->awal;
        $akhir = $request->akhir;

        $pasien = DB::connection('mysqlkhanza')
            ->table('reg_periksa')
            ->whereBetween('tgl_registrasi', [
                $awal,
                $akhir
            ])
            ->select('no_rawat')
            ->orderBy('no_rawat')
            ->get();

        $total = 0;

        foreach ($pasien as $row) {

            SyncChargeItemJob::dispatch($row->no_rawat)
                ->onQueue('satusehat');

            $total++;
        }

        return back()->with(
            'success',
            $total . ' kunjungan dari tanggal ' .
                $awal . ' sampai ' . $akhir .
                ' telah dimasukkan ke antrean SATUSEHAT.'
        );
    }

    public function send($data)
    {
        $data = decrypt($data);
        $pecah = explode('_', $data);
        $no_rawat = $pecah[0];
        $jenis_rawat = $pecah[1];

        if ($jenis_rawat == 'Ralan') {
            $jenis_rawat = 'ralan';
        } else {
            $jenis_rawat = 'ranap';
        }

        SendChargeItemJob::dispatch($no_rawat, $jenis_rawat)
            ->onQueue('satusehat');

        return back()->with(
            'success',
            'Proses Kirim charge Item untuk ' . $no_rawat . ' telah dimasukkan ke antrean.'
        );
    }
}
