<?php

namespace App\Http\Controllers;

use App\Pengeluaran;
use App\ScheduleUpdate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class PengeluaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'BIOS G2');
        session()->put('anak', 'Layanan Keuangan');
        session()->put('cucu', 'Data Pengeluaran');

        $tanggal = Carbon::now()->format('Y-m-d');
        $tahun = Carbon::now()->format('Y');
        $bulan = Carbon::now()->format('m');

        $data = Pengeluaran::whereYear('tgl_transaksi', $tahun)
            ->whereMonth('tgl_transaksi', $bulan)
            ->get();

        // ClientController::token();

        $akun = PemasukanController::RefAkun();
        // dd($akun->data);
        return view('keuangan.pengeluaran', compact('data', 'akun'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'kd_akun' => 'required',
            'jumlah' => 'required',
        ]);

        $tanggal = Carbon::now()->format('Y-m-d');

        $data = new Pengeluaran();
        $data->kd_akun = $request->kd_akun;
        $data->jumlah = $request->jumlah;
        $data->tgl_transaksi = $tanggal;
        $data->status = false;
        // $user->akses = $request->akses;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect('/pengeluaran');
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);

        $data = Pengeluaran::find($id);
        $akun = PemasukanController::RefAkun();

        return view('keuangan.pengeluaran_edit', compact('data', 'akun'));
    }

    public function update($id, Request $request)
    {
        $data = Pengeluaran::find($id);
        $data->kd_akun = $request->kd_akun;
        $data->jumlah = $request->jumlah;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect('/pengeluaran');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = Pengeluaran::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }

    public function cari(Request $request)
    {
        $tanggal = $request->get('tanggal');
        $split = explode("-", $tanggal);

        $data = Pengeluaran::whereYear('tgl_transaksi', $split[0])
            ->whereMonth('tgl_transaksi', $split[1])
            ->get();

        // ClientController::token();

        $akun = PemasukanController::RefAkun();

        return view('keuangan.pemasukan', compact('data', 'akun'));
    }

    public function client()
    {
        session()->put('ibu', 'BIOS G2');
        session()->put('anak', 'Layanan Keuangan');
        session()->put('cucu', 'Client Pengeluaran');

        ClientController::token();
        $now = Carbon::now();
        $jam = Carbon::now()->format('H:i:s');
        // dd($now);
        $data_pengeluaran = Pengeluaran::where('status', '=', 'false')
            ->whereDate('tgl_transaksi', '<', $now)
            ->get();

        // Kirim data yang bukan hari ini
        if ($data_pengeluaran->count() > 0) {
            // dd($data);
            $schedule = ScheduleUpdate::all();
            foreach ($schedule as $jadwal) {
                if (($jam >= $jadwal->waktu_mulai) and ($jam <= $jadwal->waktu_selesai)) {
                    foreach ($data_pengeluaran as $data) {

                        $tgl_transaksi = Carbon::parse($data->tgl_transaksi)->format('Y/m/d');
                        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                        $response = $client->request('POST', 'ws/pengeluaran/prod', [
                            'headers' => [
                                'token' => session('token'),
                            ],
                            'form_params' => [
                                'kd_akun' => $data->kd_akun,
                                'jumlah' => $data->jumlah,
                                'tgl_transaksi' => $tgl_transaksi,
                            ]
                        ]);

                        $datajson = json_decode($response->getBody());

                        // dd($data);

                        if ($data->status != 'MSG20003') {
                            Session::flash('error', $datajson->message);

                            return view('keuangan.client_pemasukan');
                        } else {
                            Session::flash('sukses', "$datajson->status, $datajson->message");

                            $update = Pengeluaran::find($data->id);
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

        $data = Pengeluaran::whereDate('tgl_transaksi', '>=', $tgl_mulai)
            ->whereDate('tgl_transaksi', '<=', $tgl_selesai)
            ->get();

        // dd($data);

        return view('keuangan.client_pengeluaran', compact('data'));
    }
}
