<?php

namespace App\Http\Controllers;

use App\Imports\PengeluaranImport;
use App\Pengeluaran;
use App\ScheduleUpdate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class PengeluaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'BIOS facelift');
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
            'tanggal_transaksi' => 'required',
        ]);

        $cek = Pengeluaran::where('kd_akun', $request->kd_akun)
            ->where('tgl_transaksi', $request->tanggal_transaksi)
            ->get();

        if ($cek->count() > 0) {
            Session::flash('error', 'Data pada tanggal tersebut sudah pernah dimasukkan!');
        } else {
            $data = new Pengeluaran();
            $data->kd_akun = $request->kd_akun;
            $data->jumlah = $request->jumlah;
            $data->tgl_transaksi = $request->tanggal_transaksi;
            $data->status = false;
            $data->save();

            Session::flash('sukses', 'Data Berhasil diperbaharui!');
        }

        return redirect('/pengeluaran');
    }

    public function edit($id)
    {
        session()->put('ibu', 'BIOS facelift');
        session()->put('anak', 'Layanan Keuangan');
        session()->put('cucu', 'Edit Pengeluaran');

        $id = Crypt::decrypt($id);

        // dd($id);

        $data = Pengeluaran::find($id);
        // if ($data->status == true) {
        //     Session::flash('error', 'Data Sudah terkirim!');

        //     return redirect()->back();
        // }
        $akun = PemasukanController::RefAkun();

        return view('keuangan.pengeluaran_edit', compact('data', 'akun'));
    }

    public function update($id, Request $request)
    {
        $data = Pengeluaran::find($id);
        // $data->kd_akun = $request->kd_akun;
        $data->jumlah = $request->jumlah;
        $data->status = false;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect('/pengeluaran');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = Pengeluaran::find($id);
        if ($delete->status == true) {
            Session::flash('error', 'Data Sudah terkirim, tidak bisa dihapus!');

            return redirect()->back();
        }
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

        return view('keuangan.pengeluaran', compact('data', 'akun'));
    }

    public function client()
    {
        session()->put('ibu', 'BIOS facelift');
        session()->put('anak', 'Layanan Keuangan');
        session()->put('cucu', 'Client Pengeluaran');

        ClientController::token();
        $now = Carbon::now();
        $jam = Carbon::now()->format('H:i:s');
        // dd($now);
        $data_pengeluaran = Pengeluaran::where('status', '=', 'false')
            ->whereDate('tgl_transaksi', '<', $now)
            ->get();

        //Jika data pemasukan pada hari kemarin tidak ada maka input otomatis
        $cekPemasukanKemarin = Pengeluaran::whereDate('tgl_transaksi', $now->yesterday())
            ->get();
        if ($cekPemasukanKemarin->count() == 0) {
            $simpan = new Pengeluaran();
            $simpan->tgl_transaksi = $now->yesterday();
            $simpan->kd_akun = 525111;
            $simpan->jumlah = 0;
            $simpan->status = false;
            $simpan->save();
        }

        // Kirim data yang bukan hari ini
        if ($data_pengeluaran->count() > 0) {
            // dd($data_pengeluaran);
            // $schedule = ScheduleUpdate::all();
            // foreach ($schedule as $jadwal) {
            //     if (($jam >= $jadwal->waktu_mulai) and ($jam <= $jadwal->waktu_selesai)) {
            foreach ($data_pengeluaran as $data) {

                // $tgl_transaksi = Carbon::parse($data->tgl_transaksi)->format('Y/m/d');
                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
                $response = $client->request('POST', 'ws/keuangan/akuntansi/pengeluaran', [
                    'headers' => [
                        'token' => session('tokenBios'),
                    ],
                    'form_params' => [
                        'kd_akun' => $data->kd_akun,
                        'jumlah' => $data->jumlah,
                        'tgl_transaksi' => $data->tgl_transaksi,
                    ]
                ]);

                if ($response->getStatusCode() == 200) {

                    $datajson = json_decode($response->getBody());

                    // dd($datajson, $data);

                    if ($data->status != 'MSG20003') {
                        Session::flash('error', $datajson->message);

                        return view('keuangan.client_pemasukan');
                    } else {
                        Session::flash('sukses', "$datajson->status, $datajson->message");

                        $update = Pengeluaran::find($data->id);
                        $update->status = 1;
                        $update->save();
                    }
                } else {
                    Session::flash('error', 'Pengiriman gagal status pengiriman ' . $response->getStatusCode());
                    goto Selesai;
                }
            }
            //     }
            // }
        }

        Selesai:
        $tgl_mulai = new Carbon('first day of this month');
        $tgl_selesai = new Carbon('last day of this month');

        $data = Pengeluaran::whereDate('tgl_transaksi', '>=', $tgl_mulai)
            ->whereDate('tgl_transaksi', '<=', $tgl_selesai)
            ->orderBy('tgl_transaksi', 'DESC')
            ->get();

        return view('keuangan.client_pengeluaran', compact('data'));
    }

    public function import(Request $request)
    {
        // validasi
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        // menangkap file excel
        $file = $request->file('file');

        // dd($file);

        // membuat nama file unik
        $nama_file = rand() . $file->getClientOriginalName();

        // upload ke folder file_siswa di dalam folder public
        $file->move('bios/pengeluaran', $nama_file);

        try {
            // import data
            Excel::import(new PengeluaranImport, public_path('/bios/pengeluaran/' . $nama_file));

            // notifikasi dengan session
            Session::flash('sukses', 'Data Berhasil Diimport!');
        } catch (\Throwable $th) {
            // notifikasi dengan session
            Session::flash('error', 'Cek kembali data file Anda!');
        }

        // alihkan halaman kembali
        return redirect()->back();
    }
}
