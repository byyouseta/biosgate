<?php

namespace App\Http\Controllers;

use App\AkunBios;
use App\Imports\PemasukanImport;
use App\Pemasukan;
use App\Pengeluaran;
use App\SaldoKeuangan;
use App\SaldoOperasional;
use App\SaldoPengelolaan;
use App\ScheduleUpdate;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class PemasukanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'BIOS facelift');
        session()->put('anak', 'Layanan Keuangan');
        session()->put('cucu', 'Data Penerimaan');

        $tanggal = Carbon::now()->format('Y-m-d');
        $tahun = Carbon::now()->format('Y');
        $bulan = Carbon::now()->format('m');

        $data = Pemasukan::whereYear('tgl_transaksi', $tahun)
            ->whereMonth('tgl_transaksi', $bulan)
            // ->orderBy('tgl_transaksi', 'DESC')
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
            'tanggal_transaksi' => 'required',
        ]);

        // $tanggal = Carbon::now()->format('Y-m-d');
        $cek = Pemasukan::where('kd_akun', $request->kd_akun)
            ->where('tgl_transaksi', $request->tanggal_transaksi)
            ->get();

        if ($cek->count() > 0) {
            Session::flash('error', 'Data pada tanggal tersebut sudah pernah dimasukkan!');
        } else {

            $data = new Pemasukan();
            $data->kd_akun = $request->kd_akun;
            $data->jumlah = $request->jumlah;
            $data->tgl_transaksi = $request->tanggal_transaksi;
            $data->status = false;
            // $user->akses = $request->akses;
            $data->save();

            Session::flash('sukses', 'Data Berhasil diperbaharui!');
        }

        return redirect('/penerimaan');
    }

    public function edit($id)
    {
        session()->put('ibu', 'BIOS facelift');
        session()->put('anak', 'Layanan Keuangan');
        session()->put('cucu', 'Edit Penerimaan');

        $id = Crypt::decrypt($id);

        $data = Pemasukan::find($id);
        // if ($data->status == true) {
        //     Session::flash('error', 'Data Sudah terkirim!');

        //     return redirect()->back();
        // }
        $akun = PemasukanController::RefAkun();

        return view('keuangan.pemasukan_edit', compact('data', 'akun'));
    }

    public function update($id, Request $request)
    {
        $data = Pemasukan::find($id);
        // $data->kd_akun = $request->kd_akun;
        $data->jumlah = $request->jumlah;
        $data->status = false;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect('/penerimaan');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = Pemasukan::find($id);
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
        // ClientController::token();

        // //Ambil Data Ref Akun
        // $client = new \GuzzleHttp\Client(['base_uri' => 'https://bios.kemenkeu.go.id/api/']);
        // $response = $client->request('GET', 'ws/ref/akun');
        // $akun = json_decode($response->getBody());
        // $akun = $akun->data;
        // foreach ($akun as $dataAkun) {

        //     $cek = AkunBios::where('kode', $dataAkun->kode)->get();

        //     if ($cek->count() > 0) {
        //         $update = AkunBios::where('kode', $dataAkun->kode)->first();
        //         $update->uraian = $dataAkun->uraian;
        //         $update->save();
        //     } else {
        //         $simpan = new AkunBios();
        //         $simpan->kode = $dataAkun->kode;
        //         $simpan->uraian = $dataAkun->uraian;
        //         $simpan->save();
        //     }
        // }
        $akun = AkunBios::all();

        return $akun;
    }

    public function client()
    {
        session()->put('ibu', 'BIOS Facelift');
        session()->put('anak', 'Layanan Keuangan');
        session()->put('cucu', 'Client Penerimaan');

        ClientController::token();
        $now = Carbon::now();
        // $now = new Carbon('2023-07-12');
        $jam = Carbon::now()->format('H:i:s');

        $data_pemasukan = Pemasukan::where('status', '=', 'false')
            ->whereDate('tgl_transaksi', '<', $now)
            ->get();


        //Jika data pemasukan pada hari kemarin tidak ada maka input otomatis
        $cekPemasukanKemarin = Pemasukan::whereDate('tgl_transaksi', $now->yesterday())
            ->get();
        if ($cekPemasukanKemarin->count() == 0) {
            $simpan = new Pemasukan();
            $simpan->tgl_transaksi = $now->yesterday();
            $simpan->kd_akun = 424111;
            $simpan->jumlah = 0;
            $simpan->status = false;
            $simpan->save();
        }

        // dd($data_pemasukan);
        if ($data_pemasukan->count() > 0) {
            // dd($data_pemasukan);
            // $schedule = ScheduleUpdate::all();
            // foreach ($schedule as $jadwal) {
            //     if (($jam >= $jadwal->waktu_mulai) and ($jam <= $jadwal->waktu_selesai)) {
            foreach ($data_pemasukan as $data) {

                // $tgl_transaksi = Carbon::parse($data->tgl_transaksi)->format('Y/m/d');
                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
                $response = $client->request('POST', 'ws/keuangan/akuntansi/penerimaan', [
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

                    // dd($datajson);

                    if ($datajson->status != 'MSG20003') {
                        Session::flash('error', $datajson->message);

                        return view('keuangan.client_pemasukan');
                    } else {
                        Session::flash('sukses', "$datajson->status, $datajson->message");

                        $update = Pemasukan::find($data->id);
                        $update->status = true;
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

        // dd($tgl_mulai, $tgl_selesai);
        // $tanggal = Carbon::now()->format('Y-m-d');

        $data = Pemasukan::whereDate('tgl_transaksi', '>=', $tgl_mulai)
            ->whereDate('tgl_transaksi', '<=', $tgl_selesai)
            ->orderBy('tgl_transaksi', 'DESC')
            ->get();


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

    public function chart(Request $request)
    {
        session()->put('ibu', 'BIOS facelift');
        session()->put('anak', 'Layanan Keuangan');
        session()->put('cucu', 'Laporan Saldo');


        $users = User::select(DB::raw("COUNT(*) as count"), DB::raw("MONTHNAME(created_at) as month_name"))
            ->whereYear('created_at', date('Y'))
            ->groupBy(DB::raw("month_name"))
            ->orderBy('id', 'ASC')
            ->pluck('count', 'month_name');

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
        $dataPemasukan = [];
        $dataPengeluaran = [];
        $labelsBulan = $pemasukanBulan = $pengeluaranBulan = $operasional = $pengelolaanDepo = $pengelolaanBunga = $danaKelola = [];

        for ($i = 1; $i <= $jmlHari; $i++) {
            array_push($labels, $i);
            $cek = Pemasukan::where('tgl_transaksi', "$bulanTahun-$i")
                ->sum('jumlah');
            array_push($dataPemasukan, $cek);

            $cek = Pengeluaran::where('tgl_transaksi', "$bulanTahun-$i")
                ->sum('jumlah');
            array_push($dataPengeluaran, $cek);

            $cekOperasional = SaldoOperasional::where('tgl_transaksi', "$bulanTahun-$i")
                ->sum('saldo_akhir');
            array_push($operasional, $cekOperasional);

            $cekDepo = SaldoPengelolaan::where('tgl_transaksi', "$bulanTahun-$i")
                ->sum('nilai_deposito');
            array_push($pengelolaanDepo, $cekDepo);

            $cekBunga = SaldoPengelolaan::where('tgl_transaksi', "$bulanTahun-$i")
                ->sum('nilai_bunga');
            array_push($pengelolaanBunga, $cekBunga);

            $cekDana = SaldoKeuangan::where('tgl_transaksi', "$bulanTahun-$i")
                ->sum('saldo');
            array_push($danaKelola, $cekDana);

            // if (!empty($cekOperasional)) {
            //     dd($pengelolaanBunga, $pengelolaanDepo, $operasional, $danaKelola);
            // }
        }

        for ($i = 1; $i <= 12; $i++) {
            $bulanBulan = new Carbon("$tahun-$i-1");
            $namaBulan = $bulanBulan->locale('id')->monthName;
            // dd($bulanBulan, $namaBulan);
            array_push($labelsBulan, $namaBulan);
            $cek = Pemasukan::whereMonth('tgl_transaksi', "$i")
                ->whereYear('tgl_transaksi', $tahun)
                ->sum('jumlah');
            array_push($pemasukanBulan, $cek);
            $cek = Pengeluaran::whereMonth('tgl_transaksi', "$i")
                ->whereYear('tgl_transaksi', $tahun)
                ->sum('jumlah');
            array_push($pengeluaranBulan, $cek);
        }

        // dd($labelsBulan);

        // $labels = $users->keys();
        // $data = $users->values();

        // dd($pengelolaanBunga, $pengelolaanDepo);

        return view('keuangan.chart', compact(
            'labels',
            'dataPemasukan',
            'dataPengeluaran',
            'labelsBulan',
            'pemasukanBulan',
            'pengeluaranBulan',
            'operasional',
            'pengelolaanDepo',
            'pengelolaanBunga',
            'danaKelola'
        ));
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
        $file->move('bios/pemasukan', $nama_file);

        try {
            // import data
            Excel::import(new PemasukanImport, public_path('/bios/pemasukan/' . $nama_file));

            // notifikasi dengan session
            Session::flash('sukses', 'Data Berhasil Diimport!');
        } catch (\Throwable $th) {

            // notifikasi dengan session
            Session::flash('error', 'Cek kembali data file Anda!');
        }

        // alihkan halaman kembali
        return redirect()->back();
    }

    public function template()
    {
        //PDF file is stored under project/public/download/info.pdf
        $file = public_path() . "/bios/template_keuangan.xlsx";

        $headers = [
            'Content-Type' => 'application/xlsx',
        ];

        return response()->download($file, 'template_keuangan.xlsx', $headers);
    }
}
