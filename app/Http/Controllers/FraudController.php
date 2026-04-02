<?php

namespace App\Http\Controllers;

use App\DataPengajuanKlaim;
use App\Exports\FraudRajalExport;
use App\FraudRajal;
use App\FraudRanap;
use App\Pemasukan;
use App\Pengeluaran;
use App\PeriodeKlaim;
use App\SaldoKeuangan;
use App\SaldoOperasional;
use App\SaldoPengelolaan;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Excel;
use Illuminate\Support\Facades\DB;

class FraudController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function rajal(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Fraud Rajal/IGD');
        session()->forget('cucu');

        if (empty($request->periode)) {
            $dataFraud = null;
        } else {
            $dataFraud = FraudRajal::where('periode_klaim_id', $request->periode)
                ->get();
        }

        // dd($dataFraud, $request);

        $dataPeriode = PeriodeKlaim::orderBy('periode', 'DESC')
            ->get();

        return view('vedika.fraud_rajal', compact('dataFraud', 'dataPeriode'));
    }

    public function ranap(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Fraud Ranap');
        session()->forget('cucu');

        if (empty($request->periode)) {
            $dataFraud = null;
        } else {
            $dataFraud = FraudRanap::where('periode_klaim_id', $request->periode)
                ->get();
        }

        // dd($dataFraud, $request);

        $dataPeriode = PeriodeKlaim::orderBy('periode', 'DESC')
            ->get();

        return view('vedika.fraud_ranap', compact('dataFraud', 'dataPeriode'));
    }

    public function temuan(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Temuan Fraud');
        session()->forget('cucu');

        if (empty($request->periode)) {
            $dataFraud = collect();
        } else {
            if ($request->jenis == 'rajal') {
                $dataFraud = FraudRajal::where('periode_klaim_id', $request->periode)
                    ->get();
            } else {
                $dataFraud = FraudRanap::where('periode_klaim_id', $request->periode)
                    ->get();
            }
        }

        $dataFraud = $dataFraud->filter(function ($item) {
            return $item->up_coding == 1
                || $item->phantom_billing == 1
                || $item->cloning == 1
                || $item->inflated_bills == 1
                || $item->pemecahan == 1
                || $item->rujukan_semu == 1
                || $item->repeat_billing == 1
                || $item->prolonged_los == 1
                || $item->manipulasi_kels == 1
                || $item->re_admisi == 1
                || $item->kesesuian_tindakan == 1
                || $item->tagihan_tindakan == 1
                || $item->klarifikasi != null;
        });

        // dd($dataFraud);

        $dataPeriode = PeriodeKlaim::orderBy('periode', 'DESC')
            ->get();

        return view('vedika.temuan_fraud', compact('dataFraud', 'dataPeriode'));
    }

    public function store($id, $idd)
    {
        $id = Crypt::decrypt($id);

        $tambah = new FraudRajal();
        $tambah->data_pengajuan_klaim_id = $id;
        $tambah->periode_klaim_id = $idd;
        $tambah->save();

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        return redirect()->back();
    }

    public function storeranap($id, $idd)
    {
        $id = Crypt::decrypt($id);

        $tambah = new FraudRanap();
        $tambah->data_pengajuan_klaim_id = $id;
        $tambah->periode_klaim_id = $idd;
        $tambah->save();

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        return redirect()->back();
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);

        FraudRajal::where('data_pengajuan_klaim_id', $id)
            ->delete();

        Session::flash('sukses', 'Data Berhasil didelete!');

        return redirect()->back();
    }

    public function deleteranap($id)
    {
        $id = Crypt::decrypt($id);

        FraudRanap::where('data_pengajuan_klaim_id', $id)
            ->delete();

        Session::flash('sukses', 'Data Berhasil didelete!');

        return redirect()->back();
    }

    public function detailRajal($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Fraud Rajal/IGD');
        session()->put('cucu', 'Checklist Fraud');

        $data = FraudRajal::find(Crypt::decrypt($id));
        //Ambil data untuk Bukti Pelayanan
        $buktiPelayanan = VedikaController::buktiPelayanan($data->dataPengajuan->no_rawat);
        $diagnosa = $buktiPelayanan[0];
        $prosedur = $buktiPelayanan[1];
        $norm_pasien = $buktiPelayanan[2]->no_rkm_medis;

        // dd($data);

        return view('vedika.check_fraud', compact('data', 'diagnosa', 'prosedur', 'norm_pasien'));
    }

    public function detailRanap($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Fraud Ranap');
        session()->put('cucu', 'Checklist Fraud');

        $data = FraudRanap::find(Crypt::decrypt($id));
        //Ambil data untuk Bukti Pelayanan
        $buktiPelayanan = VedikaController::buktiPelayanan($data->dataPengajuan->no_rawat);
        $diagnosa = $buktiPelayanan[0];
        $prosedur = $buktiPelayanan[1];
        $norm_pasien = $buktiPelayanan[2]->no_rkm_medis;

        // dd($data);

        return view('vedika.check_fraudRanap', compact('data', 'diagnosa', 'prosedur', 'norm_pasien'));
    }

    public function storeRajal(Request $request, $id)
    {
        // dd($request);
        $id = Crypt::decrypt($id);
        $update = FraudRajal::find($id);
        $update->up_coding = $request->up_coding;
        $update->phantom_billing = $request->phantom_billing;
        $update->cloning = $request->cloning;
        $update->inflated_bills = $request->inflated_bills;
        $update->pemecahan = $request->pemecahan;
        $update->rujukan_semu = $request->rujukan_semu;
        $update->repeat_billing = $request->repeat_billing;
        $update->prolonged_los = $request->prolonged_los;
        $update->manipulasi_kels = $request->manipulasi_kels;
        $update->re_admisi = $request->re_admisi;
        $update->kesesuaian_tindakan = $request->kesesuaian_tindakan;
        $update->tagihan_tindakan = $request->tagihan_tindakan;
        $update->klarifikasi = $request->klarifikasi;
        $update->keterangan = $request->keterangan;
        $update->selesai = $request->selesai ? true : false;
        $update->save();

        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect()->back();
    }

    public function storeDetailRanap(Request $request, $id)
    {
        // dd($request);
        $id = Crypt::decrypt($id);
        $update = FraudRanap::find($id);
        $update->up_coding = $request->up_coding;
        $update->phantom_billing = $request->phantom_billing;
        $update->cloning = $request->cloning;
        $update->inflated_bills = $request->inflated_bills;
        $update->pemecahan = $request->pemecahan;
        $update->rujukan_semu = $request->rujukan_semu;
        $update->repeat_billing = $request->repeat_billing;
        $update->prolonged_los = $request->prolonged_los;
        $update->manipulasi_kels = $request->manipulasi_kels;
        $update->re_admisi = $request->re_admisi;
        $update->kesesuaian_tindakan = $request->kesesuaian_tindakan;
        $update->tagihan_tindakan = $request->tagihan_tindakan;
        $update->klarifikasi = $request->klarifikasi;
        $update->keterangan = $request->keterangan;
        $update->selesai = $request->selesai ? true : false;
        $update->save();

        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect()->back();
    }

    public function exportRajal($id)
    {
        // Excel::create('New File', function ($excel) {
        //     $excel->sheet('First sheet', function ($sheet) {
        //         $sheet->loadView('excel.exp1');
        //     });
        // })->export('xls');;
        $data = FraudRajal::where('periode_klaim_id', Crypt::decrypt($id))
            ->get();

        // dd($data);

        return Excel::download(new FraudRajalExport($data), 'DataFraudExcel.xlsx');

        // return Excel::download(new ExportReport("loan_report.repayments_report_pdf", $data));
    }

    public function exportRanap($id)
    {
        // Excel::create('New File', function ($excel) {
        //     $excel->sheet('First sheet', function ($sheet) {
        //         $sheet->loadView('excel.exp1');
        //     });
        // })->export('xls');;
        $data = FraudRanap::where('periode_klaim_id', Crypt::decrypt($id))
            ->get();

        // dd($data);

        return Excel::download(new FraudRajalExport($data), 'DataFraudExcel.xlsx');

        // return Excel::download(new ExportReport("loan_report.repayments_report_pdf", $data));
    }

    public function chart(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Dashboard Fraud');
        session()->forget('cucu');

        // $users = User::select(DB::raw("COUNT(*) as count"), DB::raw("MONTHNAME(created_at) as month_name"))
        //     ->whereYear('created_at', date('Y'))
        //     ->groupBy(DB::raw("month_name"))
        //     ->orderBy('id', 'ASC')
        //     ->pluck('count', 'month_name');

        if (empty($request->get('tanggal'))) {
            $tanggal = Carbon::now();
            $namaBulan = $tanggal->monthName;
            $jmlHari = $tanggal->daysInMonth;
            $bulanTahun = $tanggal->format('Y-m');
            $tahun = $tanggal->format('Y');
        } else {
            $tanggal = $request->get('tanggal');
            $namaBulan = Carbon::parse($tanggal)->monthName;
            $jmlHari = Carbon::parse($tanggal)->daysInMonth;
            $bulanTahun = Carbon::parse($tanggal)->format('Y-m');
            $tahun = Carbon::parse($tanggal)->format('Y');
        }

        $labels = [];
        $dataBulanRajal = [];
        $dataBulanRanap = [];
        $labelsBulan = [];

        // dd($users);
        //sampel vs temuan fraud
        $dataPengajuanRajal = DataPengajuanKlaim::whereHas('periodeKlaim', function ($q) use ($bulanTahun) {
            $q->where('periode', 'like', "%$bulanTahun%");
        })
            ->where('jenis_rawat', 'Rawat Jalan')
            ->get();

        $dataFraud = FraudRajal::whereHas('periodeKlaim', function ($q) use ($bulanTahun) {
            $q->where('periode', 'like', "%$bulanTahun%");
        })
            ->get();

        $potensiFraudRajal = FraudRajal::whereHas('periodeKlaim', function ($q) use ($bulanTahun) {
            $q->where('periode', 'like', "%$bulanTahun%");
        })
            ->where(function ($q) {
                $q->where('up_coding', 1)
                    ->orWhere('phantom_billing', 1)
                    ->orWhere('cloning', 1)
                    ->orWhere('inflated_bills', 1)
                    ->orWhere('pemecahan', 1)
                    ->orWhere('rujukan_semu', 1)
                    ->orWhere('repeat_billing', 1)
                    ->orWhere('prolonged_los', 1)
                    ->orWhere('manipulasi_kels', 1)
                    ->orWhere('re_admisi', 1)
                    ->orWhere('kesesuaian_tindakan', 1)
                    ->orWhere('tagihan_tindakan', 1)
                    ->orWhereNotNull('klarifikasi');
            })
            ->get();

        $selesaiFraudRajal = FraudRajal::whereHas('periodeKlaim', function ($q) use ($bulanTahun) {
            $q->where('periode', 'like', "%$bulanTahun%");
        })
            ->where('selesai', true)
            ->get();

        $dataTable1 = [
            'Total Pengajuan Klaim Rajal' => $dataPengajuanRajal->count(),
            'Total Sampel' => $dataFraud->count(),
            'Total Potensi Fraud' => $potensiFraudRajal->count(),
            'Total Selesai Cek' => $selesaiFraudRajal->count(),
        ];

        $dataPengajuanRanap = DataPengajuanKlaim::whereHas('periodeKlaim', function ($q) use ($bulanTahun) {
            $q->where('periode', 'like', "%$bulanTahun%");
        })
            ->where('jenis_rawat', 'Rawat Inap')
            ->get();

        $dataFraudRanap = FraudRanap::whereHas('periodeKlaim', function ($q) use ($bulanTahun) {
            $q->where('periode', 'like', "%$bulanTahun%");
        })
            ->get();

        $potensiFraudRanap = FraudRanap::whereHas('periodeKlaim', function ($q) use ($bulanTahun) {
            $q->where('periode', 'like', "%$bulanTahun%");
        })
            ->where(function ($q) {
                $q->where('up_coding', 1)
                    ->orWhere('phantom_billing', 1)
                    ->orWhere('cloning', 1)
                    ->orWhere('inflated_bills', 1)
                    ->orWhere('pemecahan', 1)
                    ->orWhere('rujukan_semu', 1)
                    ->orWhere('repeat_billing', 1)
                    ->orWhere('prolonged_los', 1)
                    ->orWhere('manipulasi_kels', 1)
                    ->orWhere('re_admisi', 1)
                    ->orWhere('kesesuaian_tindakan', 1)
                    ->orWhere('tagihan_tindakan', 1)
                    ->orWhereNotNull('klarifikasi');
            })
            ->get();

        $selesaiFraudRanap = FraudRanap::whereHas('periodeKlaim', function ($q) use ($bulanTahun) {
            $q->where('periode', 'like', "%$bulanTahun%");
        })
            ->where('selesai', true)
            ->get();

        $dataTable2 = [
            'Total Pengajuan Klaim Ranap' => $dataPengajuanRanap->count(),
            'Total Sampel' => $dataFraudRanap->count(),
            'Total Potensi Fraud' => $potensiFraudRanap->count(),
            'Total Selesai Cek' => $selesaiFraudRanap->count(),
        ];

        $labelsFraudKriteria = [
            'Up Coding',
            'Phantom Billing',
            'Cloning',
            'Inflated Bills',
            'Pemecahan',
            'Rujukan Semu',
            'Repeat Billing',
            'Prolonged LOS',
            'Manipulasi Kelas',
            'Re-Admisi',
            'Kesesuaian Tindakan',
            'Tagihan Tindakan',
            'Klarifikasi'
        ];

        $dataFraudKriteriaRajal = [];
        $dataFraudKriteriaRanap = [];

        foreach ($labelsFraudKriteria as $kriteria) {
            switch ($kriteria) {
                case 'Up Coding':
                    $jumlah = $potensiFraudRajal->where('up_coding', 1)->count();
                    $jumlahRanap = $potensiFraudRanap->where('up_coding', 1)->count();
                    break;
                case 'Phantom Billing':
                    $jumlah = $potensiFraudRajal->where('phantom_billing', 1)->count();
                    $jumlahRanap = $potensiFraudRanap->where('phantom_billing', 1)->count();
                    break;
                case 'Cloning':
                    $jumlah = $potensiFraudRajal->where('cloning', 1)->count();
                    $jumlahRanap = $potensiFraudRanap->where('cloning', 1)->count();
                    break;
                case 'Inflated Bills':
                    $jumlah = $potensiFraudRajal->where('inflated_bills', 1)->count();
                    $jumlahRanap = $potensiFraudRanap->where('inflated_bills', 1)->count();
                    break;
                case 'Pemecahan':
                    $jumlah = $potensiFraudRajal->where('pemecahan', 1)->count();
                    $jumlahRanap = $potensiFraudRanap->where('pemecahan', 1)->count();
                    break;
                case 'Rujukan Semu':
                    $jumlah = $potensiFraudRajal->where('rujukan_semu', 1)->count();
                    $jumlahRanap = $potensiFraudRanap->where('rujukan_semu', 1)->count();
                    break;
                case 'Repeat Billing':
                    $jumlah = $potensiFraudRajal->where('repeat_billing', 1)->count();
                    $jumlahRanap = $potensiFraudRanap->where('repeat_billing', 1)->count();
                    break;
                case 'Prolonged LOS':
                    $jumlah = $potensiFraudRajal->where('prolonged_los', 1)->count();
                    $jumlahRanap = $potensiFraudRanap->where('prolonged_los', 1)->count();
                    break;
                case 'Manipulasi Kelas':
                    $jumlah = $potensiFraudRajal->where('manipulasi_kels', 1)->count();
                    $jumlahRanap = $potensiFraudRanap->where('manipulasi_kels', 1)->count();
                    break;
                case 'Re-Admisi':
                    $jumlah = $potensiFraudRajal->where('re_admisi', 1)->count();
                    $jumlahRanap = $potensiFraudRanap->where('re_admisi', 1)->count();
                    break;
                case 'Kesesuaian Tindakan':
                    $jumlah = $potensiFraudRajal->where('kesesuaian_tindakan', 1)->count();
                    $jumlahRanap = $potensiFraudRanap->where('kesesuaian_tindakan', 1)->count();
                    break;
                case 'Tagihan Tindakan':
                    $jumlah = $potensiFraudRajal->where('tagihan_tindakan', 1)->count();
                    $jumlahRanap = $potensiFraudRanap->where('tagihan_tindakan', 1)->count();
                    break;
                case 'Klarifikasi':
                    $jumlah = $potensiFraudRajal->whereNotNull('klarifikasi')->count();
                    $jumlahRanap = $potensiFraudRanap->whereNotNull('klarifikasi')->count();
                    break;
                default:
                    $jumlah = $jumlahRanap = 0;
                    break;
            }
            array_push($dataFraudKriteriaRajal, $jumlah);
            array_push($dataFraudKriteriaRanap, $jumlahRanap);
        }

        for ($i = 1; $i <= 12; $i++) {
            $bulanBulan = new Carbon("$tahun-$i-1");
            $formatBulan = $bulanBulan->format('Y-m');
            $namaBulan = $bulanBulan->locale('id')->monthName;
            array_push($labelsBulan, $namaBulan);

            $cekRanap = FraudRanap::whereHas('periodeKlaim', function ($q) use ($formatBulan) {
                $q->where('periode', 'like', "%$formatBulan%");
            })
                ->where(function ($q) {
                    $q->where('up_coding', 1)
                        ->orWhere('phantom_billing', 1)
                        ->orWhere('cloning', 1)
                        ->orWhere('inflated_bills', 1)
                        ->orWhere('pemecahan', 1)
                        ->orWhere('rujukan_semu', 1)
                        ->orWhere('repeat_billing', 1)
                        ->orWhere('prolonged_los', 1)
                        ->orWhere('manipulasi_kels', 1)
                        ->orWhere('re_admisi', 1)
                        ->orWhere('kesesuaian_tindakan', 1)
                        ->orWhere('tagihan_tindakan', 1)
                        ->orWhereNotNull('klarifikasi');
                })
                ->count();

            array_push($dataBulanRanap, $cekRanap);
            $cekRajal = FraudRajal::whereHas('periodeKlaim', function ($q) use ($formatBulan) {
                $q->where('periode', 'like', "%$formatBulan%");
            })
                ->where(function ($q) {
                    $q->where('up_coding', 1)
                        ->orWhere('phantom_billing', 1)
                        ->orWhere('cloning', 1)
                        ->orWhere('inflated_bills', 1)
                        ->orWhere('pemecahan', 1)
                        ->orWhere('rujukan_semu', 1)
                        ->orWhere('repeat_billing', 1)
                        ->orWhere('prolonged_los', 1)
                        ->orWhere('manipulasi_kels', 1)
                        ->orWhere('re_admisi', 1)
                        ->orWhere('kesesuaian_tindakan', 1)
                        ->orWhere('tagihan_tindakan', 1)
                        ->orWhereNotNull('klarifikasi');
                })
                ->count();
            array_push($dataBulanRajal, $cekRajal);
        }

        return view('vedika.chart_fraud', compact(
            'labelsFraudKriteria',
            'dataFraudKriteriaRajal',
            'dataFraudKriteriaRanap',
            'dataTable1',
            'dataTable2',
            'labelsBulan',
            'dataBulanRajal',
            'dataBulanRanap'
        ));
    }
}
