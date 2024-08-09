<?php

namespace App\Http\Controllers;

use App\Exports\FraudRajalExport;
use App\FraudRajal;
use App\FraudRanap;
use App\PeriodeKlaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Excel;

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
}
