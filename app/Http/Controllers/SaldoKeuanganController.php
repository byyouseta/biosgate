<?php

namespace App\Http\Controllers;

use App\Bank;
use App\SaldoKeuangan;
use App\ScheduleUpdate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class SaldoKeuanganController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'BIOS G2');
        session()->put('anak', 'Layanan Keuangan');
        session()->put('cucu', 'Data Saldo');

        $tanggal = Carbon::now()->format('Y-m-d');
        $tahun = Carbon::now()->format('Y');
        $bulan = Carbon::now()->format('m');

        $data = SaldoKeuangan::whereYear('tgl_transaksi', $tahun)
            ->whereMonth('tgl_transaksi', $bulan)
            ->get();
        $bank = Bank::all();
        $rekening = SaldoKeuanganController::KdRek();

        // dd($rekening);
        return view('keuangan.saldo', compact('data', 'bank', 'rekening'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'bank' => 'required',
            'saldo' => 'required',
            'kd_rek' => 'required',
        ]);

        $tanggal = Carbon::now()->format('Y-m-d');

        $data = new SaldoKeuangan();
        $data->bank_id = $request->bank;
        $data->saldo = $request->saldo;
        $data->kd_rek = $request->kd_rek;
        $data->tgl_transaksi = $tanggal;
        $data->status = false;
        // $user->akses = $request->akses;
        $data->save();

        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect('/saldokeuangan');
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);

        $data = SaldoKeuangan::find($id);
        $bank = Bank::all();
        $rekening = SaldoKeuanganController::KdRek();

        return view('keuangan.saldo_edit', compact('data', 'bank', 'rekening'));
    }

    public function update($id, Request $request)
    {
        $data = SaldoKeuangan::find($id);
        $data->bank_id = $request->bank;
        $data->saldo = $request->saldo;
        $data->kd_rek = $request->kd_rek;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect('/saldokeuangan');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = SaldoKeuangan::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }

    public function cari(Request $request)
    {
        $tanggal = $request->get('tanggal');
        $split = explode("-", $tanggal);

        $data = SaldoKeuangan::whereYear('tgl_transaksi', $split[0])
            ->whereMonth('tgl_transaksi', $split[1])
            ->get();

        $bank = Bank::all();
        $rekening = SaldoKeuanganController::KdRek();

        return view('keuangan.saldo', compact('data', 'bank', 'rekening'));
    }

    public function client()
    {
        session()->put('ibu', 'BIOS G2');
        session()->put('anak', 'Layanan Keuangan');
        session()->put('cucu', 'Client Saldo');

        ClientController::token();
        $now = Carbon::now();
        $jam = Carbon::now()->format('H:i:s');
        // dd($now);
        $data_saldo = SaldoKeuangan::where('status', '=', 'false')
            ->whereDate('tgl_transaksi', '<', $now)
            ->get();

        // dd($data_saldo);

        // Kirim data yang bukan hari ini
        if ($data_saldo->count() > 0) {
            // dd($data);
            $schedule = ScheduleUpdate::all();
            foreach ($schedule as $jadwal) {
                if (($jam >= $jadwal->waktu_mulai) and ($jam <= $jadwal->waktu_selesai)) {
                    foreach ($data_saldo as $data) {

                        $tgl_transaksi = Carbon::parse($data->tgl_transaksi)->format('Y/m/d');
                        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
                        $response = $client->request('POST', 'ws/saldo/prod', [
                            'headers' => [
                                'token' => session('token'),
                            ],
                            'form_params' => [
                                'kd_bank' => $data->bank->kd_bank,
                                'norek' => $data->bank->norek,
                                'saldo' => $data->saldo,
                                'kd_rek' => $data->kd_rek,
                                'tgl_transaksi' => $tgl_transaksi,
                            ]
                        ]);

                        $datajson = json_decode($response->getBody());

                        // dd($data);

                        if ($data->status != 'MSG20003') {
                            Session::flash('error', $datajson->message);

                            return view('keuangan.client_saldo');
                        } else {
                            Session::flash('sukses', "$datajson->status, $datajson->message");

                            $update = SaldoKeuangan::find($data->id);
                            $update->status = true;
                            $update->save();
                        }
                    }
                }
            }
        }
        $tgl_mulai = new Carbon('first day of this month');
        $tgl_selesai = new Carbon('last day of this month');

        // dd($tgl_mulai, $tgl_selesai);
        // $tanggal = Carbon::now()->format('Y-m-d');

        $data = SaldoKeuangan::whereDate('tgl_transaksi', '>=', $tgl_mulai)
            ->whereDate('tgl_transaksi', '<=', $tgl_selesai)
            ->get();

        $kemarin = Carbon::now()->yesterday()->format('Y/m/d');
        // $kemarin = "2022/03/29";
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('POST', 'get/data/saldo', [
            'headers' => [
                'token' => session('token'),
            ],
            'form_params' => [
                'tgl_transaksi' => $kemarin,
            ]
        ]);

        $datajson = json_decode($response->getBody());
        $cekdata = $datajson->data;
        // dd($cekdata);

        return view('keuangan.client_saldo', compact('data', 'cekdata'));
    }

    public static function KdRek()
    {
        ClientController::token();

        //Ambil Data Ref Akun
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
        $response = $client->request('GET', 'ws/ref/rekening');
        $rekening = json_decode($response->getBody());
        $rekening = $rekening->data;

        // dd($rekening);
        return $rekening;
    }
}
