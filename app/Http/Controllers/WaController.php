<?php

namespace App\Http\Controllers;

use App\LogKirimPesan;
use App\MappingLidNumber;
use App\Setting;
use App\TemplatePesan;
use App\WebhookMessage;
use Carbon\Carbon;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


class WaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'Pesan');
        session()->put('anak', 'Setting');
        session()->forget('cucu');

        $status = WaController::cekStatus();
        $setting = Setting::where('nama', 'pesan')->first();
        // dd($status);

        if ($status == 'online') {
            $sessionApp = WaController::cekSession();
        } else {
            $sessionApp = null;
        }
        // dd($connection, $status, $getSession);

        return view('pesan.setting_pesan', compact(
            'status',
            'setting',
            'sessionApp'
        ));
    }

    public function createSession()
    {
        $setting = Setting::where('nama', 'pesan')->first();

        $sessionApp = WaController::cekSession();
        $status = WaController::cekStatus();

        // dd($sessionApp, $status);

        if ($sessionApp == true && $status == 'online') {
            // WaController::deleteSession();
            Session::flash('warning', 'Session sudah dibuat');

            return redirect('/pesan');
        } elseif ($sessionApp == false && $status == 'online') {
            $client = new \GuzzleHttp\Client((['base_uri' => $setting->base_url]));
            try {
                $response = $client->request('GET', "/session/start/$setting->key", [
                    'headers' => [
                        'x-api-key' => null,
                    ]
                ]);
            } catch (BadResponseException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $test = json_decode($response->getBody());
                    // dd($test, 'error create session');

                    Session::flash('error', $test->error);

                    return redirect()->route('wa.index');
                }
            }
        }

        $createSession = json_decode($response->getBody());
        // $qrCode = $dataSession->data;

        // dd($createSession);
        if ($createSession && $createSession->success == false) {
            // if ($createSession->error == 'Session already exists for: rsupgate') {
            Session::flash('warning', $createSession->message);
            // }
        } elseif ($createSession && $createSession->success == true) {

            Session::flash('sukses', $createSession->message);
        }


        // return redirect()->route('wa.index');


        // dd($status);

        return view('pesan.setting_pesan', compact(
            'setting',
            'status',
            'sessionApp'
        ));
    }

    public function getQr()
    {
        $setting = Setting::where('nama', 'pesan')->first();

        $sessionApp = WaController::cekSession();
        $status = WaController::cekStatus();

        //Get image
        $client = new \GuzzleHttp\Client((['base_uri' => $setting->base_url]));
        try {
            $response = $client->request('GET', "/session/qr/$setting->key", [
                'headers' => [
                    'x-api-key' => null,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());
                dd($test, 'error get image');
            }
        }

        $qrCode = json_decode($response->getBody());

        if ($qrCode && $qrCode->success == true) {

            return view('pesan.setting_pesan', compact(
                'setting',
                'status',
                'qrCode',
                'sessionApp'
            ));
        } else {
            Session::flash('error', $qrCode->message);

            return redirect()->back();
        }
    }

    public static function cekSession()
    {
        $setting = Setting::where('nama', 'pesan')->first();
        // dd($setting);

        $client = new \GuzzleHttp\Client((['base_uri' => $setting->base_url]));
        try {
            $response = $client->request('GET', "/session/status/$setting->key", [
                'headers' => [
                    'x-api-key' => null,
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());
                // dd($test);

                // $message = "Medication 1 error $test";
                if ($test) {
                    Session::flash('error', $test->message);
                }
            }
        }

        $data = json_decode($response->getBody());

        // dd($data, $setting);
        if ($data && $data->success == false) {
            return false;
        } elseif ($data && $data->success == true) {
            return true;
        }
    }

    //Cek server API Whatsapp online atau tidak dengan ping
    public static function cekStatus()
    {
        $setting = Setting::where('nama', 'pesan')->first();
        //inisiasi data awal
        $status = 'offline';

        if (!empty($setting)) {
            try {
                $client = new \GuzzleHttp\Client((['base_uri' => $setting->base_url]));
                try {
                    $response = $client->request('GET', "/ping");
                } catch (BadResponseException $e) {
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();
                        $test = json_decode($response->getBody());
                        // dd($test);

                        // $message = "Medication 1 error $test";
                        if ($test) {
                            Session::flash('error', $test->message);
                        }
                    }
                }

                $data = json_decode($response->getBody());

                // dd($data, $response);

                // Cek ServerLama
                // $connection = @fsockopen($ipServer, $portServer);

                if ($data && $data->success == 'true') {

                    $status = 'online';
                } else {
                    $status = 'offline';
                }
            } catch (\Throwable $th) {
                $message = $th->getMessage();
                // Session::flash('error', $message);
            }

            return $status;
        }
    }

    public function deleteSession()
    {
        $setting = Setting::where('nama', 'pesan')->first();

        $client = new \GuzzleHttp\Client((['base_uri' => $setting->base_url]));
        try {
            $response = $client->request('GET', "/session/terminate/$setting->key");
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());
                // dd($test);

                // $message = "Medication 1 error $test";
                if ($test) {
                    Session::flash('error', $test->message);
                }
            }
        }

        $data = json_decode($response->getBody());

        // dd($data);
        if ($data && $data->success == false) {
            Session::flash('error', $data->message);
        } elseif ($data && $data->success == true) {
            Session::flash('error', $data->message);
        }

        return redirect()->back();
    }

    public function kirimPesan()
    {
        session()->put('ibu', 'Pesan');
        session()->put('anak', 'Kirim Pesan');
        session()->forget('cucu');

        $template = TemplatePesan::all();

        return view('pesan.kirim', compact('template'));
    }

    public function getPenerima(Request $request)
    {
        $search = $request->q;

        $dataPasien =  DB::connection('mysqlkhanza')->table('pasien')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.tmp_lahir',
                'pasien.no_tlp'
            )
            ->where('nm_pasien', 'like', "%$search%")
            ->where(function ($query) {
                $query->whereNotNull('pasien.no_tlp')
                    ->where('pasien.no_tlp', '!=', '-')
                    ->where('pasien.no_tlp', '!=', '');
            })
            ->orderBy('pasien.nm_pasien', 'ASC')
            ->limit(10)
            ->get();

        return response()->json($dataPasien);
    }

    public function kirim(Request $request)
    {
        $request->validate([
            'penerima' => 'required' //|starts_with:62
        ], [
            'penerima.starts_with' => 'Format nomor yang digunakan salah'
        ]);

        $status = WaController::cekStatus();
        // dd($request);

        if ($status == 'online') {
            $sessionApp = WaController::cekSession();
        } else {
            $message = "Server tidak bisa dijangkau!";

            Session::flash('error', $message);

            return redirect()->back();
        }

        $telp = $request->penerima;


        if (substr($telp, 0, 1) === '0') {
            $telp = '62' . substr($telp, 1);

            $cekMapping = MappingLidNumber::where('phone', $telp)->first();
            if (!$cekMapping) {
                WaController::getLidNumber($telp);
            } elseif (Carbon::parse($cekMapping->last_checked_at)->addDays(7)->isPast()) {
                // Jika sudah lebih dari 7 hari sejak terakhir cek, update lid
                WaController::getLidNumber($telp);
            }
        }

        if ($sessionApp == true) {
            $setting = Setting::where('nama', 'pesan')->first();

            $client = new \GuzzleHttp\Client((['base_uri' => $setting->base_url]));
            try {
                $response = $client->request('POST', "/client/sendMessage/$setting->key", [
                    'headers' => [
                        'x-api-key' => null,
                    ],
                    'json' => [
                        "chatId" => "$telp@c.us",
                        "contentType" => "string",
                        "content" => "$request->pesan"
                    ]
                ]);
            } catch (BadResponseException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $test = json_decode($response->getBody());
                    dd($test, 'error pengiriman pesan');

                    Session::flash('error', $test->message);
                }
            }

            $data = json_decode($response->getBody());

            if ($data && $data->success == true) {
                Session::flash('sukses', 'Pesan berhasil dikirim');
            } else {
                Session::flash('error', 'Pesan gagal dikirim');
            }

            return redirect()->back();
        } else {
            $message = "Session id belum tersetting";

            Session::flash('error', $message);

            return redirect()->back();
        }
    }

    public static function getLidNumber($phone)
    {
        $setting = Setting::where('nama', 'pesan')->first();

        try {
            $client = new \GuzzleHttp\Client(['base_uri' => $setting->base_url]);
            $response = $client->request('POST', "/client/getNumberId/$setting->key", [
                'headers' => [
                    'x-api-key' => null,
                ],
                'json' => [
                    "number" => "$phone@c.us"
                ]
            ]);
        } catch (BadResponseException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody(), true);
            return response()->json(['success' => false, 'message' => $errorResponse['message'] ?? 'Gagal ambil lid.'], 500);
        }

        $data = json_decode($response->getBody());

        if ($data && $data->success == true) {
            $lid = $data->result->_serialized;

            // Simpan mapping nomor telepon dengan lid
            $mapping = MappingLidNumber::updateOrCreate(
                ['phone' => $phone],
                ['wid' => $lid, 'last_checked_at' => Carbon::now()]
            );

            return response()->json(['success' => true, 'wid' => $lid]);
        } else {
            return response()->json(['success' => false, 'message' => 'Gagal ambil lid.'], 500);
        }
    }

    public static function kirimAutoBalas($phone, $message)
    {
        $getTemplate = TemplatePesan::where('nama', 'balas otomatis')->first();

        if (!$getTemplate) {
            return response()->json(['success' => false, 'message' => 'Template tidak ditemukan.'], 404);
        }

        //Dinonaktifkan jika nanti mau buat template khusus untuk balas otomatis dengan data dinamis pasien
        // $dataPasien = [
        //     'nama_pasien' => $getData->nm_pasien,
        //     'no_rm' => $getData->no_rkm_medis,
        //     'tgl_kunjungan' => Carbon::parse($getData->tanggal_periksa)->locale('id')->translatedFormat('l, d F Y'),
        //     'nama_poli' => $getData->nm_poli,
        //     'nama_dokter' => $getData->nm_dokter,
        // ];

        // $finalMessage = BookingPendaftaranController::generateMessageFromTemplate($template->pesan, $dataPasien)->pesan;

        $finalMessage = $getTemplate->pesan;

        $status = WaController::cekStatus();

        if ($status !== 'online') {
            return response()->json(['success' => false, 'message' => 'Server tidak bisa dijangkau!'], 500);
        }

        $sessionApp = WaController::cekSession();

        if (!$sessionApp) {
            return response()->json(['success' => false, 'message' => 'Session ID belum tersetting.'], 500);
        }

        $telp = $phone;
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

    public static function kirimJson(Request $request): JsonResponse
    {
        // 🔹 Validasi manual (biar JSON, bukan redirect)
        $validator = \Validator::make($request->all(), [
            'penerima' => 'required|regex:/^[0-9+]+$/', // Hanya angka dan tanda +
        ], [
            'penerima.required' => 'Nomor penerima wajib diisi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // 🔹 Cek status server WA
            $status = self::cekStatus();

            if ($status !== 'online') {
                return response()->json([
                    'success' => false,
                    'message' => 'Server tidak bisa dijangkau'
                ], 500);
            }

            // 🔹 Ambil session
            $sessionApp = self::cekSession();

            if (!$sessionApp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session WA tidak ditemukan'
                ], 500);
            }

            // 🔹 Ambil data
            $telp = $request->input('penerima');
            $pesan    = $request->input('pesan');

            $telp = preg_replace('/[^0-9]/', '', $telp);

            if (str_starts_with($telp, '0')) {
                $telp = '62' . substr($telp, 1);

                $cekMapping = MappingLidNumber::where('phone', $telp)->first();
                if (!$cekMapping) {
                    WaController::getLidNumber($telp);
                } elseif ($cekMapping->last_checked_at && Carbon::parse($cekMapping->last_checked_at)->addDays(7)->isPast()) {
                    // Jika sudah lebih dari 7 hari sejak terakhir cek, update lid
                    WaController::getLidNumber($telp);
                }
            }

            // 🔹 Contoh kirim (sesuaikan dengan function kamu)
            $setting = Setting::where('nama', 'pesan')->first();
            $client = new \GuzzleHttp\Client(['base_uri' => $setting->base_url, 'timeout' => 10]);
            $response = $client->request('POST', "/client/sendMessage/$setting->key", [
                'headers' => [
                    'x-api-key' => null,
                ],
                'json' => [
                    "chatId" => "$telp@c.us",
                    "contentType" => "string",
                    "content" => "$pesan"
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal koneksi ke server WA'
                ], 500);
            }

            $kirim = json_decode($response->getBody()->getContents());

            if (!$kirim || ($kirim->success ?? false) == false) {
                return response()->json([
                    'success' => false,
                    'message' => $kirim->message ?? 'Gagal mengirim pesan'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dikirim',
                'data'    => $kirim
            ]);
        } catch (\Throwable $e) {
            activity('wa-json')->withProperties([
                'request' => $request->all()
            ])->log('Error kirimJson: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function simpanTemplate(Request $request)
    {
        $this->validate($request, [
            'nama' => 'required',
            'pesan' => 'required',
            'default' => 'required',
        ]);

        $simpan = new TemplatePesan();
        $simpan->nama = $request->nama;
        $simpan->pesan = $request->pesan;
        if ($request->default == true) {
            $cekDefault = TemplatePesan::where('default', TRUE)
                ->first();
            if ($cekDefault) {
                $cekDefault->default = FALSE;
                $cekDefault->save();
            }
        }
        $simpan->default = $request->default;
        $simpan->save();

        Session::flash('sukses', 'Data berhasil ditambahkan');

        return redirect()->back();
    }

    public function defaultTemplate($id)
    {
        $id = Crypt::decrypt($id);

        $cekDefault = TemplatePesan::where('default', TRUE)
            ->first();

        if ($cekDefault) {
            $cekDefault->default = FALSE;
            $cekDefault->save();
        }

        $simpan = TemplatePesan::find($id);
        // dd($cekDefault);
        if ($cekDefault && $cekDefault->id == $simpan->id) {
            $simpan->default = FALSE;
        } else {
            $simpan->default = TRUE;
        }
        $simpan->save();

        Session::flash('sukses', 'Defauld template berhasil diganti');

        return redirect()->back();
    }

    public function editTemplate($id)
    {
        $id = Crypt::decrypt($id);

        $data = TemplatePesan::find($id);

        // dd($data);
        return response()->json($data);
    }

    public function updateTemplate(Request $request)
    {
        $this->validate($request, [
            'template_id' => 'required',
            'nama' => 'required',
            'pesan' => 'required',
            'default' => 'required',
        ]);

        $simpan = TemplatePesan::find($request->template_id);
        $simpan->nama = $request->nama;
        $simpan->pesan = $request->pesan;
        if ($request->default == true) {
            $cekDefault = TemplatePesan::where('default', TRUE)
                ->first();
            if ($cekDefault && ($cekDefault->id != $request->template_id)) {
                $cekDefault->default = FALSE;
                $cekDefault->save();
            }
        }
        $simpan->default = $request->default;
        $simpan->save();

        Session::flash('sukses', 'Template berhasil diperbaharui');

        return redirect()->back();
    }

    public function deleteTemplate($id)
    {
        $id = Crypt::decrypt($id);

        $hapus = TemplatePesan::find($id);
        $hapus->delete();

        Session::flash('sukses', 'Defauld template berhasil dihapus');

        return redirect()->back();
    }

    public function kotakPesan(Request $request)
    {
        session()->put('ibu', 'Pesan');
        session()->put('anak', 'Kotak Pesan');
        session()->forget('cucu');

        $myNumber = env('NO_PESAN') . "@c.us";

        $conversations = WebhookMessage::selectRaw('
        CASE
            WHEN `from` = ? THEN `to`
            ELSE `from`
        END as chat_with,
        MAX(timestamp) as last_message_time,
        MAX(body) as last_message
    ', [$myNumber])
            ->where(function ($query) use ($myNumber) {
                $query->where('from', $myNumber)
                    ->orWhere('to', $myNumber);
            })
            ->groupBy('chat_with')
            ->orderByDesc('last_message_time')
            // ->limit(100)
            ->get();

        // $dataPercakapan = WaController::percakapan($request->no_pasien);

        // $percakapan = $dataPercakapan[0];
        // $myNumber = $dataPercakapan[1];
        // $targetNumber = $dataPercakapan[2];

        return view('pesan.kotak_pesan', compact('conversations'));
    }

    public function percakapan($id)
    {
        $myNumber = env('NO_PESAN') . "@c.us";
        $targetNumber = "$id@c.us"; //jadi $id nie

        $chat = WebhookMessage::where(function ($query) use ($myNumber, $targetNumber) {
            $query->where('from', $myNumber)->where('to', $targetNumber);
        })
            ->orWhere(function ($query) use ($myNumber, $targetNumber) {
                $query->where('from', $targetNumber)->where('to', $myNumber);
            })
            ->orderBy('timestamp')
            ->get();

        // dd($messages);

        // return array('chat', 'myNumber', 'targetNumber');

        return view('pesan.detail_pesan', compact(
            'chat',
            'myNumber',
            'targetNumber'
        ));
    }

    public function status(Request $request)
    {
        session()->put('ibu', 'Pesan');
        session()->put('anak', 'Status Pesan');
        session()->forget('cucu');

        if ($request->input('tanggal'))
            $date = $request->input('tanggal');
        else {
            $date = Carbon::now();
        }

        $data = LogKirimPesan::whereDate('created_at', $date)->get();

        foreach ($data as $list) {
            $getData = DB::connection('mysqlkhanza')->table('booking_registrasi')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'booking_registrasi.no_rkm_medis')
                ->select(
                    'booking_registrasi.*',
                    'pasien.nm_pasien',
                    'pasien.no_tlp'
                )
                ->where('booking_registrasi.no_rkm_medis', $list->no_rm)
                ->whereDate('booking_registrasi.tanggal_periksa', Carbon::parse($list->tgl_periksa)->format('Y-m-d'))
                ->first();
            if (isset($getData)) {
                $list->nama_pasien = $getData->nm_pasien;
                $list->no_telp = $getData->no_tlp;
            } else {
                $getData = DB::connection('mysqlkhanza')->table('referensi_mobilejkn_bpjs')
                    ->join('pasien', 'pasien.no_rkm_medis', '=', 'referensi_mobilejkn_bpjs.norm')
                    ->join('maping_dokter_dpjpvclaim', 'maping_dokter_dpjpvclaim.kd_dokter_bpjs', '=', 'referensi_mobilejkn_bpjs.kodedokter')
                    ->select(
                        'referensi_mobilejkn_bpjs.*',
                        'pasien.nm_pasien',
                        'maping_dokter_dpjpvclaim.nm_dokter_bpjs as nm_dokter'
                    )
                    ->where('referensi_mobilejkn_bpjs.norm', $list->no_rm)
                    ->whereDate('referensi_mobilejkn_bpjs.tanggalperiksa', Carbon::parse($list->tgl_periksa)->format('Y-m-d'))
                    ->first();

                if ($getData) {
                    $list->nama_pasien = $getData->nm_pasien;
                    $list->no_telp = $getData->nohp;
                }
            }
        }

        return view('pesan.status_pesan', compact('data'));
    }
}
