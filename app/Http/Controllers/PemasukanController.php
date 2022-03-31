<?php

namespace App\Http\Controllers;

use App\Pemasukan;
use App\ScheduleUpdate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class PemasukanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'BIOS G2');
        session()->put('anak', 'Layanan Keuangan');
        session()->put('cucu', 'Data Penerimaan');

        $tanggal = Carbon::now()->format('Y-m-d');
        $tahun = Carbon::now()->format('Y');
        $bulan = Carbon::now()->format('m');

        $data = Pemasukan::whereYear('tgl_transaksi', $tahun)
            ->whereMonth('tgl_transaksi', $bulan)
            ->get();

        // ClientController::token();

        $akun = PemasukanController::RefAkun();
        // dd($akun->data);
        return view('keuangan.pemasukan', compact('data', 'akun'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'kd_akun' => 'required',
            'jumlah' => 'required',
        ]);

        $tanggal = Carbon::now()->format('Y-m-d');

        $data = new Pemasukan();
        $data->kd_akun = $request->kd_akun;
        $data->jumlah = $request->jumlah;
        $data->tgl_transaksi = $tanggal;
        $data->status = false;
        // $user->akses = $request->akses;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect('/penerimaan');
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);

        $data = Pemasukan::find($id);
        $akun = PemasukanController::RefAkun();

        return view('keuangan.pemasukan_edit', compact('data', 'akun'));
    }

    public function update($id, Request $request)
    {
        $data = Pemasukan::find($id);
        $data->kd_akun = $request->kd_akun;
        $data->jumlah = $request->jumlah;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect('/penerimaan');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = Pemasukan::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }

    public function cari(Request $request)
    {
        $tanggal = $request->get('tanggal');

        // dd($tanggal);

        // $str = $request->nama;
        $split = explode("-", $tanggal);

        $data = Pemasukan::whereYear('tgl_transaksi', $split[0])
            ->whereMonth('tgl_transaksi', $split[1])
            ->get();

        // dd($data);
        // ClientController::token();

        $akun = PemasukanController::RefAkun();

        return view('keuangan.pemasukan', compact('data', 'akun'));
    }

    public static function RefAkun()
    {
        ClientController::token();

        //Ambil Data Ref Akun
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'ws/ref/akun');
        $akun = json_decode($response->getBody());
        $akun = $akun->data;

        return $akun;
    }

    public function client()
    {
        session()->put('ibu', 'BIOS G2');
        session()->put('anak', 'Layanan Keuangan');
        session()->put('cucu', 'Client Penerimaan');

        ClientController::token();
        $now = Carbon::now();
        $jam = Carbon::now()->format('H:i:s');
        // dd($now);
        $data_pemasukan = Pemasukan::where('status', '=', 'false')
            ->whereDate('tgl_transaksi', '<', $now)
            ->get();

        // dd($data_pemasukan);
        if ($data_pemasukan->count() > 0) {
            // dd($data);
            $schedule = ScheduleUpdate::all();
            foreach ($schedule as $jadwal) {
                if (($jam >= $jadwal->waktu_mulai) and ($jam <= $jadwal->waktu_selesai)) {
                    foreach ($data_pemasukan as $data) {

                        $tgl_transaksi = Carbon::parse($data->tgl_transaksi)->format('Y/m/d');
                        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
                        $response = $client->request('POST', 'ws/penerimaan/prod', [
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

                        if ($datajson->status != 'MSG20003') {
                            Session::flash('error', $datajson->message);

                            return view('keuangan.client_pemasukan');
                        } else {
                            Session::flash('sukses', "$datajson->status, $datajson->message");

                            $update = Pemasukan::find($data->id);
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

        $data = Pemasukan::whereDate('tgl_transaksi', '>=', $tgl_mulai)
            ->whereDate('tgl_transaksi', '<=', $tgl_selesai)
            ->get();

        // dd($data);

        return view('keuangan.client_pemasukan', compact('data'));
    }

    //Ambil Rekap
    public static function RekapPemasukan($tanggal)
    {
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('POST', 'get/data/penerimaan', [
            'headers' => [
                'token' => session('token'),
            ],
            'form_params' => [
                'tgl_transaksi' => Carbon::parse($tanggal)->format('Y/m/d'),
            ]
        ]);

        $dataterkirim = json_decode($response->getBody());

        return $dataterkirim;
    }
}
