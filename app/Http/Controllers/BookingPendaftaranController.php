<?php

namespace App\Http\Controllers;

use App\Jobs\KirimPesanJob;
use App\Jobs\KirimPesanMJob;
use App\Setting;
use App\TemplatePesan;
use Carbon\Carbon;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BookingPendaftaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        session()->put('ibu', 'Berkas RM');
        session()->put('anak', 'Booking Registrasi');
        session()->forget('cucu');

        if (empty($request->get('tanggal'))) {
            $tanggal = Carbon::now();
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
        }

        $data = DB::connection('mysqlkhanza')->table('booking_registrasi')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'booking_registrasi.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'booking_registrasi.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'booking_registrasi.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'booking_registrasi.kd_pj')
            ->select(
                'booking_registrasi.*',
                'pasien.nm_pasien',
                'pasien.no_tlp',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'penjab.png_jawab'
            )
            ->whereYear('booking_registrasi.waktu_kunjungan', Carbon::parse($tanggal)->format('Y'))
            ->whereMonth('booking_registrasi.waktu_kunjungan', Carbon::parse($tanggal)->format('m'))
            ->whereDay('booking_registrasi.waktu_kunjungan', Carbon::parse($tanggal)->format('d'))
            ->get();

        $template = TemplatePesan::all();

        return view('berkas_rm.booking', compact('data', 'template'));
    }

    public function indexMjkn(Request $request)
    {
        session()->put('ibu', 'Berkas RM');
        session()->put('anak', 'Booking MJKN');
        session()->forget('cucu');

        if (empty($request->get('tanggal'))) {
            $tanggal = Carbon::now();
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
        }

        $data = DB::connection('mysqlkhanza')->table('referensi_mobilejkn_bpjs')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'referensi_mobilejkn_bpjs.norm')

            ->join('maping_dokter_dpjpvclaim', 'maping_dokter_dpjpvclaim.kd_dokter_bpjs', '=', 'referensi_mobilejkn_bpjs.kodedokter')
            ->select(
                'referensi_mobilejkn_bpjs.*',
                'pasien.nm_pasien',
                'maping_dokter_dpjpvclaim.nm_dokter_bpjs as nm_dokter'
            )
            ->whereDate('referensi_mobilejkn_bpjs.tanggalperiksa', Carbon::parse($tanggal)->format('Y-m-d'))
            ->get();

        foreach ($data as $listData) {
            $pecahKdPoli = explode('-', $listData->nomorantrean);

            $cariPoli = DB::connection('mysqlkhanza')->table('poliklinik')
                ->select('*')
                ->where('poliklinik.kd_poli', $pecahKdPoli[0])
                ->first();

            $listData->nm_poli = $cariPoli->nm_poli;
        }
        $template = TemplatePesan::all();

        return view('berkas_rm.booking_mjkn', compact('data', 'template'));
    }

    public function getPasien($id)
    {
        $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('pasien')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.no_tlp'
            )
            ->where('no_rkm_medis', $id)
            ->first();

        return response()->json($data);
    }

    public function getTemplate($id)
    {
        $template = TemplatePesan::find($id);

        if ($template) {
            return response()->json([
                'pesan' => $template->isi_pesan // Ganti sesuai nama kolom
            ]);
        } else {
            return response()->json(['pesan' => 'Template tidak ditemukan'], 404);
        }
    }

    public function kirimPesanPasien(Request $request)
    {
        // dd($request);
        $idTemplate = Crypt::decrypt($request->template);
        // dd($idTemplate, $request);
        KirimPesanJob::dispatch($request->no_rm, $request->tgl_periksa, $idTemplate);
        // $template = TemplatePesan::find($idTemplate);
        // $getData = DB::connection('mysqlkhanza')->table('booking_registrasi')
        //     ->join('pasien', 'pasien.no_rkm_medis', '=', 'booking_registrasi.no_rkm_medis')
        //     ->join('dokter', 'dokter.kd_dokter', '=', 'booking_registrasi.kd_dokter')
        //     ->join('poliklinik', 'poliklinik.kd_poli', '=', 'booking_registrasi.kd_poli')
        //     ->join('penjab', 'penjab.kd_pj', '=', 'booking_registrasi.kd_pj')
        //     ->select(
        //         'booking_registrasi.*',
        //         'pasien.nm_pasien',
        //         'pasien.no_tlp',
        //         'dokter.nm_dokter',
        //         'poliklinik.nm_poli',
        //         'penjab.png_jawab'
        //     )
        //     ->where('booking_registrasi.no_rkm_medis', $request->no_rm)
        //     ->where('booking_registrasi.tanggal_periksa', Carbon::parse($request->tgl_periksa)->format('Y-m-d'))
        //     ->first();

        // if ($getData) {
        //     $dataPasien = [
        //         'nama_pasien' => $getData->nm_pasien,
        //         'no_rm' => $getData->no_rkm_medis,
        //         'tgl_kunjungan' => Carbon::parse($getData->tanggal_periksa)->locale('id')->translatedFormat('l, d F Y'),
        //         'nama_poli' => $getData->nm_poli,
        //         'nama_dokter' => $getData->nm_dokter,
        //     ];

        //     $finalMessage = BookingPendaftaranController::generateMessageFromTemplate($template->pesan, $dataPasien)->pesan;

        //     // dd($finalMessage);
        // } else {
        //     return redirect()->back()->withErrors('error', 'Data Kunjungan tidak ditemukan');
        // }

        // $status = WaController::cekStatus();
        // // dd($request);

        // if ($status == 'online') {
        //     $sessionApp = WaController::cekSession();
        // } else {
        //     $message = "Server tidak bisa dijangkau!";

        //     Session::flash('error', $message);

        //     return redirect()->back();
        // }
        // // Sekarang $finalMessage bisa kamu kirim via API WhatsApp
        // // $telp = $getData->no_tlp;
        // $telp = '085647290127';

        // if (substr($telp, 0, 1) === '0') {
        //     $telp = '62' . substr($telp, 1);
        // }

        // // dd($telp);

        // if ($sessionApp == true) {
        //     $setting = Setting::where('nama', 'pesan')->first();

        //     $client = new \GuzzleHttp\Client((['base_uri' => $setting->base_url]));
        //     try {
        //         $response = $client->request('POST', "/client/sendMessage/$setting->key", [
        //             'headers' => [
        //                 'x-api-key' => null,
        //             ],
        //             'json' => [
        //                 "chatId" => "$telp@c.us",
        //                 "contentType" => "string",
        //                 "content" => "$finalMessage"
        //             ]
        //         ]);
        //     } catch (BadResponseException $e) {
        //         if ($e->hasResponse()) {
        //             $response = $e->getResponse();
        //             $test = json_decode($response->getBody());
        //             dd($test, 'error pengiriman pesan');

        //             // $message = "Medication 1 error $test";

        //             Session::flash('error', $test->message);
        //         }
        //     }

        //     $data = json_decode($response->getBody());

        //     if ($data && $data->success == true) {
        //         Session::flash('sukses', 'Pesan berhasil dikirim');
        //     } else {
        //         Session::flash('error', 'Pesan gagal dikirim');
        //     }

        //     return redirect()->back();
        // } else {
        //     $message = "Session id belum tersetting";

        //     Session::flash('error', $message);

        //     return redirect()->back();
        // }

        Session::flash('sukses', 'Pengiriman pesan sedang berlangsung');
        return redirect()->back();
    }

    public function kirimPesanMPasien(Request $request)
    {
        // dd($request);
        $idTemplate = Crypt::decrypt($request->template);
        // dd($idTemplate, $request);
        KirimPesanMJob::dispatch($request->no_rm, $request->tgl_periksa, $idTemplate);

        Session::flash('sukses', 'Pengiriman pesan sedang berlangsung');
        return redirect()->back();
    }

    public static function generateMessageFromTemplate($template, $data)
    {
        foreach ($data as $key => $value) {
            $template = str_replace('@' . $key, $value, $template);
        }

        return (object)['pesan' => $template]; //$template;
    }

    public function kirimBlast(Request $request)
    {
        $request->validate([
            'pasien_ids' => 'required',
            'template_id' => 'required',
        ]);


        $idTemplate = Crypt::decrypt($request->template_id);

        $dataPasien =  explode(',', $request->pasien_ids);

        // dd($request, $dataPasien);
        foreach ($dataPasien as $kirimPesan) {
            KirimPesanJob::dispatch($kirimPesan, $request->tgl_periksa, $idTemplate);
        }

        Session::flash('sukses', 'Pengiriman pesan sedang berlangsung');
        return redirect()->back();
    }

    public function kirimBlastM(Request $request)
    {
        $request->validate([
            'pasien_ids' => 'required',
            'template_id' => 'required',
        ]);


        $idTemplate = Crypt::decrypt($request->template_id);

        $dataPasien =  explode(',', $request->pasien_ids);

        // dd($request, $dataPasien);
        foreach ($dataPasien as $kirimPesan) {
            KirimPesanMJob::dispatch($kirimPesan, $request->tgl_periksa, $idTemplate);
        }

        Session::flash('sukses', 'Pengiriman pesan sedang berlangsung');
        return redirect()->back();
    }

    public static function kirimPesan($no_rm, $tgl_kunjungan, $template_id)
    {
        $template = TemplatePesan::find($template_id);

        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template tidak ditemukan.'], 404);
        }

        $getData = DB::connection('mysqlkhanza')->table('booking_registrasi')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'booking_registrasi.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'booking_registrasi.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'booking_registrasi.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'booking_registrasi.kd_pj')
            ->select(
                'booking_registrasi.*',
                'pasien.nm_pasien',
                'pasien.no_tlp',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'penjab.png_jawab'
            )
            ->where('booking_registrasi.no_rkm_medis', $no_rm)
            ->where('booking_registrasi.tanggal_periksa', Carbon::parse($tgl_kunjungan)->format('Y-m-d'))
            ->first();

        if (!$getData) {
            return response()->json(['success' => false, 'message' => 'Data kunjungan tidak ditemukan.'], 404);
        }

        $dataPasien = [
            'nama_pasien' => $getData->nm_pasien,
            'no_rm' => $getData->no_rkm_medis,
            'tgl_kunjungan' => Carbon::parse($getData->tanggal_periksa)->locale('id')->translatedFormat('l, d F Y'),
            'nama_poli' => $getData->nm_poli,
            'nama_dokter' => $getData->nm_dokter,
        ];

        $finalMessage = BookingPendaftaranController::generateMessageFromTemplate($template->pesan, $dataPasien)->pesan;

        $status = WaController::cekStatus();

        if ($status !== 'online') {
            return response()->json(['success' => false, 'message' => 'Server tidak bisa dijangkau!'], 500);
        }

        $sessionApp = WaController::cekSession();

        if (!$sessionApp) {
            return response()->json(['success' => false, 'message' => 'Session ID belum tersetting.'], 500);
        }

        $telp = $getData->no_tlp;
        // $telp = '085647290127';

        if (substr($telp, 0, 1) === '0') {
            $telp = '62' . substr($telp, 1);
        }

        $setting = Setting::where('nama', 'pesan')->first();

        try {
            $client = new \GuzzleHttp\Client(['base_uri' => $setting->base_url]);
            $response = $client->request('POST', "/client/sendMessage/$setting->key", [
                'headers' => [
                    'x-api-key' => null,
                ],
                'json' => [
                    "chatId" => "$telp@c.us",
                    "contentType" => "string",
                    "content" => "$finalMessage"
                ]
            ]);
        } catch (BadResponseException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody(), true);
            return response()->json(['success' => false, 'message' => $errorResponse['message'] ?? 'Gagal mengirim pesan.'], 500);
        }

        $data = json_decode($response->getBody());

        if ($data && $data->success == true) {
            return response()->json(['success' => true, 'message' => 'Pesan berhasil dikirim.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Pesan gagal dikirim.']);
        }
    }

    public static function kirimPesanM($no_rm, $tgl_kunjungan, $template_id)
    {
        $template = TemplatePesan::find($template_id);

        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template tidak ditemukan.'], 404);
        }

        $getData = DB::connection('mysqlkhanza')->table('referensi_mobilejkn_bpjs')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'referensi_mobilejkn_bpjs.norm')
            ->join('maping_dokter_dpjpvclaim', 'maping_dokter_dpjpvclaim.kd_dokter_bpjs', '=', 'referensi_mobilejkn_bpjs.kodedokter')
            ->select(
                'referensi_mobilejkn_bpjs.*',
                'pasien.nm_pasien',
                'maping_dokter_dpjpvclaim.nm_dokter_bpjs as nm_dokter'
            )
            ->where('referensi_mobilejkn_bpjs.norm', $no_rm)
            ->where('referensi_mobilejkn_bpjs.tanggalperiksa', Carbon::parse($tgl_kunjungan)->format('Y-m-d'))
            ->first();

        $pecahKdPoli = explode('-', $getData->nomorantrean);

        $cariPoli = DB::connection('mysqlkhanza')->table('poliklinik')
            ->select('*')
            ->where('poliklinik.kd_poli', $pecahKdPoli[0])
            ->first();

        $getData->nm_poli = $cariPoli->nm_poli;

        if (!$getData) {
            return response()->json(['success' => false, 'message' => 'Data kunjungan tidak ditemukan.'], 404);
        }

        $dataPasien = [
            'nama_pasien' => $getData->nm_pasien,
            'no_rm' => $getData->norm,
            'tgl_kunjungan' => Carbon::parse($getData->tanggalperiksa)->locale('id')->translatedFormat('l, d F Y'),
            'nama_poli' => $getData->nm_poli,
            'nama_dokter' => $getData->nm_dokter,
        ];

        $finalMessage = BookingPendaftaranController::generateMessageFromTemplate($template->pesan, $dataPasien)->pesan;

        $status = WaController::cekStatus();

        if ($status !== 'online') {
            return response()->json(['success' => false, 'message' => 'Server tidak bisa dijangkau!'], 500);
        }

        $sessionApp = WaController::cekSession();

        if (!$sessionApp) {
            return response()->json(['success' => false, 'message' => 'Session ID belum tersetting.'], 500);
        }

        $telp = $getData->no_tlp;
        // $telp = '085647290127';

        if (substr($telp, 0, 1) === '0') {
            $telp = '62' . substr($telp, 1);
        }

        $setting = Setting::where('nama', 'pesan')->first();

        try {
            $client = new \GuzzleHttp\Client(['base_uri' => $setting->base_url]);
            $response = $client->request('POST', "/client/sendMessage/$setting->key", [
                'headers' => [
                    'x-api-key' => null,
                ],
                'json' => [
                    "chatId" => "$telp@c.us",
                    "contentType" => "string",
                    "content" => "$finalMessage"
                ]
            ]);
        } catch (BadResponseException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody(), true);
            return response()->json(['success' => false, 'message' => $errorResponse['message'] ?? 'Gagal mengirim pesan.'], 500);
        }

        $data = json_decode($response->getBody());

        if ($data && $data->success == true) {
            return response()->json(['success' => true, 'message' => 'Pesan berhasil dikirim.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Pesan gagal dikirim.']);
        }
    }
}
