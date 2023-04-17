<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Rekening;
use App\SaldoKeuangan;
use App\SaldoOperasional;
use App\SaldoPengelolaan;
use App\ScheduleUpdate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class SaldoKeuanganController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'BIOS facelift');
        session()->put('anak', 'Layanan Keuangan');
        session()->put('cucu', 'Saldo Operasional');

        $tanggal = Carbon::now()->format('Y-m-d');
        $tahun = Carbon::now()->format('Y');
        $bulan = Carbon::now()->format('m');

        $data = SaldoOperasional::whereYear('tgl_transaksi', $tahun)
            ->whereMonth('tgl_transaksi', $bulan)
            ->orderBy('tgl_transaksi', 'DESC')
            ->get();
        $bank = Bank::where('rekening_id', '2')->get();
        $rekening = Rekening::all();

        // dd($rekening);
        return view('keuangan.saldo', compact('data', 'bank', 'rekening'));
    }

    public function indexPengelolaan()
    {
        session()->put('ibu', 'BIOS facelift');
        session()->put('anak', 'Layanan Keuangan');
        session()->put('cucu', 'Saldo Pengelolaan Kas');

        $tanggal = Carbon::now()->format('Y-m-d');
        $tahun = Carbon::now()->format('Y');
        $bulan = Carbon::now()->format('m');

        $data = SaldoPengelolaan::whereYear('tgl_transaksi', $tahun)
            ->whereMonth('tgl_transaksi', $bulan)
            ->orderBy('tgl_transaksi', 'DESC')
            ->get();
        $bank = Bank::where('rekening_id', '1')->get();
        $rekening = Rekening::all();

        // dd($rekening);
        return view('keuangan.saldo_pengelolaan', compact('data', 'bank', 'rekening'));
    }

    public function indexKelolaan()
    {
        session()->put('ibu', 'BIOS facelift');
        session()->put('anak', 'Layanan Keuangan');
        session()->put('cucu', 'Saldo Dana Kelolaan');

        $tanggal = Carbon::now()->format('Y-m-d');
        $tahun = Carbon::now()->format('Y');
        $bulan = Carbon::now()->format('m');

        $data = SaldoKeuangan::whereYear('tgl_transaksi', $tahun)
            ->whereMonth('tgl_transaksi', $bulan)
            ->orderBy('tgl_transaksi', 'DESC')
            ->get();
        $bank = Bank::where('rekening_id', '3')->get();
        $rekening = Rekening::all();

        // dd($rekening);
        return view('keuangan.saldo_kelolaan', compact('data', 'bank', 'rekening'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'bank' => 'required',
            'saldo' => 'required',
            'tanggal_transaksi' => 'required',
        ]);

        $cekUnik = SaldoOperasional::where('bank_id', $request->bank)
            ->where('tgl_transaksi', $request->tanggal_transaksi)
            ->count();

        if (!empty($cekUnik)) {
            Session::flash('error', 'Data transaksi pada tanggal dan Bank tersebut sudah ada!');

            return redirect()->back();
        }

        // $tanggal = Carbon::now()->format('Y-m-d');

        $data = new SaldoOperasional();
        $data->bank_id = $request->bank;
        $data->saldo_akhir = $request->saldo;
        $data->tgl_transaksi = $request->tanggal_transaksi;
        $data->status = false;
        // $user->akses = $request->akses;
        $data->save();

        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect('/saldo/operasional');
    }

    public function storeKelolaan(Request $request)
    {
        $this->validate($request, [
            'bank' => 'required',
            'saldo' => 'required',
            'tanggal_transaksi' => 'required',
        ]);

        $cekUnik = SaldoKeuangan::where('bank_id', $request->bank)
            ->where('tgl_transaksi', $request->tanggal_transaksi)
            ->count();

        if (!empty($cekUnik)) {
            Session::flash('error', 'Data transaksi pada tanggal dan Bank tersebut sudah ada!');

            return redirect()->back();
        }

        // $tanggal = Carbon::now()->format('Y-m-d');

        $data = new SaldoKeuangan();
        $data->bank_id = $request->bank;
        $data->saldo = $request->saldo;
        $data->tgl_transaksi = $request->tanggal_transaksi;
        $data->status = false;
        // $user->akses = $request->akses;
        $data->save();

        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect('/saldo/kelolaan');
    }

    public function storePengelolaan(Request $request)
    {
        $this->validate($request, [
            'bank' => 'required',
            'nilai_deposito' => 'required',
            'nilai_bunga' => 'required',
            'tanggal_transaksi' => 'required',
        ]);

        $cekUnik = SaldoPengelolaan::where('bank_id', $request->bank)
            ->where('tgl_transaksi', $request->tanggal_transaksi)
            ->count();

        if (!empty($cekUnik)) {
            Session::flash('error', 'Data transaksi pada tanggal dan Bank tersebut sudah ada!');

            return redirect()->back();
        }

        // $tanggal = Carbon::now()->format('Y-m-d');

        $data = new SaldoPengelolaan();
        $data->bank_id = $request->bank;
        $data->nilai_deposito = $request->nilai_deposito;
        $data->nilai_bunga = $request->nilai_bunga;
        $data->tgl_transaksi = $request->tanggal_transaksi;
        $data->status = false;
        // $user->akses = $request->akses;
        $data->save();

        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect('/saldo/pengelolaankas');
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);

        $data = SaldoOperasional::find($id);
        $bank = Bank::where('rekening_id', '2')->get();

        return view('keuangan.saldo_edit', compact('data', 'bank'));
    }

    public function editKelolaan($id)
    {
        $id = Crypt::decrypt($id);

        $data = SaldoKeuangan::find($id);
        $bank = Bank::where('rekening_id', '3')->get();

        return view('keuangan.saldo_kelolaan_edit', compact('data', 'bank'));
    }

    public function editPengelolaan($id)
    {
        $id = Crypt::decrypt($id);

        $data = SaldoPengelolaan::find($id);
        $bank = Bank::where('rekening_id', '1')->get();

        return view('keuangan.saldo_pengelolaan_edit', compact('data', 'bank'));
    }

    public function update($id, Request $request)
    {
        $data = SaldoOperasional::find($id);
        $data->bank_id = $request->bank;
        $data->saldo_akhir = $request->saldo;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect('/saldo/operasional');
    }

    public function updateKelolaan($id, Request $request)
    {
        $data = SaldoKeuangan::find($id);
        $data->bank_id = $request->bank;
        $data->saldo = $request->saldo;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect('/saldo/kelolaan');
    }

    public function updatePengelolaan($id, Request $request)
    {
        $data = SaldoPengelolaan::find($id);
        // $data->bank_id = $request->bank;
        $data->nilai_deposito = $request->nilai_deposito;
        $data->nilai_bunga = $request->nilai_bunga;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect('/saldo/pengelolaankas');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = SaldoOperasional::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }

    public function deleteKelolaan($id)
    {
        $id = Crypt::decrypt($id);
        $delete = SaldoKeuangan::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }

    public function deletePengelolaan($id)
    {
        $id = Crypt::decrypt($id);
        $delete = SaldoPengelolaan::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }

    public function cari(Request $request)
    {
        $tanggal = $request->get('tanggal');
        $split = explode("-", $tanggal);

        $data = SaldoOperasional::whereYear('tgl_transaksi', $split[0])
            ->whereMonth('tgl_transaksi', $split[1])
            ->get();

        $bank = Bank::where('rekening_id', '2')->get();

        return view('keuangan.saldo', compact('data', 'bank'));
    }

    public function cariPengelolaan(Request $request)
    {
        $tanggal = $request->get('tanggal');
        $split = explode("-", $tanggal);

        $data = SaldoPengelolaan::whereYear('tgl_transaksi', $split[0])
            ->whereMonth('tgl_transaksi', $split[1])
            ->orderBy('tgl_transaksi', 'DESC')
            ->get();

        $bank = Bank::where('rekening_id', '1')->get();

        return view('keuangan.saldo_pengelolaan', compact('data', 'bank'));
    }

    public function client()
    {
        session()->put('ibu', 'BIOS facelift');
        session()->put('anak', 'Layanan Keuangan');
        session()->put('cucu', 'Client Saldo');

        ClientController::token();

        SaldoKeuanganController::KirimOperasional();
        SaldoKeuanganController::KirimPengelolaanKas();
        SaldoKeuanganController::KirimDanaKelolaan();
        // dd('done');

        // $tgl_mulai = new Carbon('first day of this month');
        // $tgl_selesai = new Carbon('last day of this month');

        // dd($tgl_mulai, $tgl_selesai);
        $kemarin = Carbon::now()->yesterday();

        $operasional = SaldoOperasional::whereDate('tgl_transaksi',  $kemarin)
            ->orderBy('tgl_transaksi', 'DESC')
            ->get();
        $pengelolaan = SaldoPengelolaan::whereDate('tgl_transaksi',  $kemarin)
            ->orderBy('tgl_transaksi', 'DESC')
            ->get();
        $kelolaan = SaldoKeuangan::whereDate('tgl_transaksi',  $kemarin)
            ->orderBy('tgl_transaksi', 'DESC')
            ->get();

        return view('keuangan.client_saldo', compact('operasional', 'pengelolaan', 'kelolaan'));
    }

    public function KirimOperasional()
    {
        $now = Carbon::now();

        //Jika data Operasional pada hari kemarin tidak ada maka input otomatis
        $cekKemarin = SaldoOperasional::whereDate('tgl_transaksi', $now->yesterday())
            ->get();

        // dd($cekKemarin);
        if ($cekKemarin->count() == 0) {
            $cekBank = Bank::where('rekening_id', 2)
                ->where('default', true)
                ->orderBy('updated_at', 'desc')
                ->first();

            if (!empty($cekBank)) {
                // dd($cekBank);

                $simpan = new SaldoOperasional();
                $simpan->tgl_transaksi = $now->yesterday();
                $simpan->bank_id = $cekBank->id;
                $simpan->saldo_akhir = 0;
                $simpan->status = false;
                $simpan->save();
            }
        }

        $dataOperasional = SaldoOperasional::whereDate('tgl_transaksi', '<', $now)
            ->where('status', 0)
            ->get();

        // dd($dataOperasional);
        if ($dataOperasional->count() > 0) {
            foreach ($dataOperasional as $listData) {

                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
                $response = $client->request('POST', 'ws/keuangan/saldo/saldo_operasional', [
                    'headers' => [
                        'token' => session('tokenBios'),
                    ],
                    'form_params' => [
                        'kdbank' => $listData->bank->rekening->kode,
                        'no_rekening' => $listData->bank->norek,
                        'saldo_akhir' => $listData->saldo_akhir,
                        'unit' => $listData->bank->namaRek,
                        'tgl_transaksi' => $listData->tgl_transaksi,
                    ]
                ]);

                $datajson = json_decode($response->getBody());

                // dd($datajson);

                if ($datajson->status != 'MSG20003') {
                    Session::flash('error', $datajson->message);

                    // return view('keuangan.client_saldo');
                } else {
                    Session::flash('sukses', "$datajson->status, $datajson->message");

                    $update = SaldoOperasional::find($listData->id);
                    $update->status = true;
                    $update->save();
                }
            }
        }
    }

    public function KirimPengelolaanKas()
    {
        $now = Carbon::now();
        $dataPengelolaan = SaldoPengelolaan::whereDate('tgl_transaksi', '<', $now)
            ->where('status', 0)
            ->get();

        //Jika data Pengelolaan Kas pada hari kemarin tidak ada maka input otomatis
        $cekKemarin = SaldoPengelolaan::whereDate('tgl_transaksi', $now->yesterday())
            ->get();

        // dd($cekKemarin);
        if ($cekKemarin->count() == 0) {
            $cekBank = Bank::where('rekening_id', 1)->get();
            // dd($cekBank);
            foreach ($cekBank as $dataBank) {
                // dd($dataBank);
                $cekRekening = SaldoPengelolaan::where('bank_id', $dataBank->id)
                    ->orderBy('tgl_transaksi', 'desc')
                    ->first();

                if (!empty($cekRekening)) {
                    // dd($cekRekening, 'test');

                    $simpan = new SaldoPengelolaan();
                    $simpan->tgl_transaksi = $now->yesterday();
                    $simpan->bank_id = $cekRekening->bank_id;
                    $simpan->nilai_deposito = $cekRekening->nilai_deposito;
                    $simpan->nilai_bunga = 0;
                    $simpan->status = false;
                    $simpan->save();
                }
            }
        }

        // dd($dataPengelolaan);
        if ($dataPengelolaan->count() > 0) {
            foreach ($dataPengelolaan as $listData) {

                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
                $response = $client->request('POST', 'ws/keuangan/saldo/saldo_pengelolaan_kas', [
                    'headers' => [
                        'token' => session('tokenBios'),
                    ],
                    'form_params' => [
                        'kdbank' => $listData->bank->rekening->kode,
                        'no_bilyet' => $listData->bank->noBilyet,
                        'nilai_deposito' => $listData->nilai_deposito,
                        'nilai_bunga' => $listData->nilai_bunga,
                        'tgl_transaksi' => $listData->tgl_transaksi,
                    ]
                ]);

                $datajson = json_decode($response->getBody());

                // dd($datajson);

                if ($datajson->status != 'MSG20003') {
                    Session::flash('error', $datajson->message);

                    // return view('keuangan.client_saldo');
                } else {
                    Session::flash('sukses', "$datajson->status, $datajson->message");

                    $update = SaldoPengelolaan::find($listData->id);
                    $update->status = true;
                    $update->save();
                }
            }
        }
    }

    public function KirimDanaKelolaan()
    {
        $now = Carbon::now();
        $dataKelolaan = SaldoKeuangan::whereDate('tgl_transaksi', '<', $now)
            ->where('status', 0)
            ->get();

        // dd($dataKelolaan);
        if ($dataKelolaan->count() > 0) {
            foreach ($dataKelolaan as $listData) {

                $client = new \GuzzleHttp\Client(['base_uri' => session('base_url_bios')]);
                $response = $client->request('POST', 'ws/keuangan/saldo/saldo_dana_kelolaan', [
                    'headers' => [
                        'token' => session('tokenBios'),
                    ],
                    'form_params' => [
                        'kdbank' => $listData->bank->rekening->kode,
                        'no_rekening' => $listData->bank->norek,
                        'saldo_akhir' => $listData->saldo,
                        'tgl_transaksi' => $listData->tgl_transaksi,
                    ]
                ]);

                $datajson = json_decode($response->getBody());

                // dd($datajson);

                if ($datajson->status != 'MSG20003') {
                    Session::flash('error', $datajson->message);

                    // return view('keuangan.client_saldo');
                } else {
                    Session::flash('sukses', "$datajson->status, $datajson->message");

                    $update = SaldoKeuangan::find($listData->id);
                    $update->status = true;
                    $update->save();
                }
            }
        }
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
        foreach ($rekening as $dataRek) {
            $simpan = new Rekening();
            $simpan->kode = $dataRek->kode;
            $simpan->uraian = $dataRek->uraian;
            $simpan->save();
        }
        return $rekening;
    }
}
