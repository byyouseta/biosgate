<?php

namespace App\Http\Controllers;

use App\BalasPesan;
use App\Jobs\KirimPesanCustom;
use App\MappingLidNumber;
use App\Setting;
use App\WebhookMessage;
use Carbon\Carbon;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class RedirectController extends Controller
{
    public static function viewerRadiologi($accession)
    {
        $data = RadiologiController::checkPacs($accession);

        // dd($data);

        $url_viewer = env('URL_VIEWERPACS');

        if (!empty($data)) {
            return redirect("$url_viewer/viewer/$data");
        } else {
            Session::forget('sukses');
            Session::put('error', "Accession $accession tidak ditemukan");

            return view('info');
        }
    }

    //Untuk API dari WA Gateway / Webhook
    public function handle(Request $request)
    {
        $payload = $request->all();
        $message = $payload['data']['message'] ?? null;
        $sessionId = $payload['sessionId'] ?? null;

        // Jika tidak ada message, log dan abaikan
        if (!$message) {
            WebhookMessage::create([
                'session_id' => $sessionId,
                'raw' => $payload,
            ]);
            return response()->json(['status' => 'no_message']);
        }

        $from = $message['from'] ?? '';
        $to = $message['to'] ?? '';
        $type = $message['type'] ?? '';

        // Filter: abaikan pesan dari grup dan broadcast
        if (str_contains($from, '@g.us') || str_contains($to, '@g.us')) {
            return response()->json(['status' => 'ignored_group']);
        }

        if (str_contains($from, '@broadcast') || str_contains($to, '@broadcast')) {
            return response()->json(['status' => 'ignored_broadcast']);
        }

        // Filter: hanya simpan tipe 'chat'
        if ($type !== 'chat') {
            return response()->json(['status' => 'ignored_non_chat']);
        }

        $messageId = $message['id']['_serialized'] ?? null;

        if (!$messageId || WebhookMessage::where('message_id', $messageId)->exists()) {
            return response()->json(['status' => 'duplicate_ignored']);
        }

        // Simpan pesan pribadi
        // WebhookMessage::create([
        //     'session_id' => $sessionId,
        //     'from'       => $message['from'] ?? '',
        //     'to'         => $message['to'] ?? '',
        //     'body'       => $message['body'] ?? '',
        //     'type'       => $message['type'] ?? '',
        //     'timestamp'  => isset($message['timestamp']) ? Carbon::createFromTimestamp($message['timestamp']) : now(),
        //     'message_id' => is_array($message['id']) ? ($message['id']['_serialized'] ?? '') : ($message['id'] ?? ''),
        //     'raw'        => $message, // ini simpan semua isi array message
        // ]);
        WebhookMessage::updateOrCreate(
            ['message_id' => $message['id']['_serialized'] ?? ''],
            [
                'session_id' => $sessionId,
                'from'       => $message['from'] ?? '',
                'to'         => $message['to'] ?? '',
                'body'       => $message['body'] ?? '',
                'type'       => $message['type'] ?? '',
                'timestamp'  => isset($message['timestamp']) ? Carbon::createFromTimestamp($message['timestamp']) : now(),
                'raw'        => $message,
            ]
        );

        // Cek apakah pesan dari nomor yang diinginkan jika ya, balas otomatis
        $from = $message['from'] ?? '';
        if (!str_contains($from, '@c.us')) {
            activity('webhook')->log('Received message: ' . $from . ' - ' . ($message['body'] ?? ''));
            $cekLid = MappingLidNumber::where('wid', $from)->first();
            if ($cekLid) {
                $nomor = $cekLid->phone;

                goto NomorDariLid; // langsung ke proses auto reply
            }
            goto hasil; // tidak kirim karena mapping juga tidak ada

        }

        $pecah = explode('@', $message['from']);
        $nomor = $pecah[0] ?? '';

        NomorDariLid:
        $pecah2 = explode('@', $message['to']);
        $nomor2 = $pecah2[0] ?? '';

        if ($nomor != env('NO_PESAN') && $nomor2 == env('NO_PESAN') && env('AUTO_BALAS') == true) {
            $this->autoReply($nomor, $message['body']);
        }

        hasil:
        return response()->json(['status' => 'stored']);
    }

    //Untuk balasan otomatis, dengan log aktivitas jika gagal kirim
    public function autoReply($phone, $message)
    {
        $cekReplay = BalasPesan::where('no_hp', $phone)->first();

        if (!$cekReplay) {

            $kirim = WaController::kirimAutoBalas($phone, $message);

            if (!$kirim || !isset($kirim->original['success']) || !$kirim->original['success']) {
                activity('auto_reply')->log('Gagal kirim ke ' . $phone);
                return;
            }

            BalasPesan::create([
                'no_hp' => $phone,
                'last_replied_at' => now(),
                'reply_count' => 1,
                'last_message' => $message
            ]);
        } else {
            // Cek jika sudah pernah dibalas sebelumnya, dan jika terakhir dibalas kurang dari 60 menit yang lalu, maka tidak perlu balas lagi
            if (now()->diffInMinutes($cekReplay->last_replied_at) < 60) {
                return;
            }

            $kirim = WaController::kirimAutoBalas($phone, $message);

            if (!$kirim || !isset($kirim->original['success']) || !$kirim->original['success']) {
                activity('auto_reply')->log('Gagal kirim ke ' . $phone);
                return;
            }

            $cekReplay->update([
                'last_replied_at' => now(),
                'reply_count' => $cekReplay->reply_count + 1,
                'last_message' => $message
            ]);
        }
    }

    //untuk mengirim OTP, dengan format pesan yang sudah ditentukan, dan log error jika gagal kirim
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required|numeric',
            'aplikasi' => 'required|string',
        ]);

        // Kirim ke WhatsApp API kamu
        // $status = WaController::cekStatus();

        // if ($status == 'online') {
        //     $sessionApp = WaController::cekSession();
        // } else {
        //     $message = "Server tidak bisa dijangkau!";

        //     return response()->json(['status' => 'failed', 'error' => $message], 500);
        // }

        $telp = $request->phone;

        if (substr($telp, 0, 1) === '0') {
            $telp = '62' . substr($telp, 1);
        }


        // if ($sessionApp == true) {
        // $setting = Setting::where('nama', 'pesan')->first();

        $formatPesan = "Untuk mengaktifkan reset Password $request->aplikasi RSUP Surakarta, masukkan kode OTP: $request->otp. Berlaku selama 5 menit. JANGAN PERNAH membagikan kode ini kepada orang lain dalam keadaan apa pun.";

        $client = new \GuzzleHttp\Client((['base_uri' => env('SERVER_API_WA')]));
        try {
            $response = $client->request('POST', "/client/sendMessage/" . env('SESSION_WA'), [
                'headers' => [
                    'x-api-key' => null,
                ],
                'json' => [
                    "chatId" => "$telp@c.us",
                    "contentType" => "string",
                    "content" => "$formatPesan"
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());
                // dd($test, 'error pengiriman pesan');

                // $message = "Medication 1 error $test";

                return response()->json(['status' => 'failed', 'error' => $test->body()], 500);
            }
        }

        $data = json_decode($response->getBody());

        if ($data && $data->success == true) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'failed', 'error' => 'Gagal mengirim pesan'], 500);
        }
        // }
    }

    //Untuk kirim pesan dengan format bebas, dengan log error jika gagal kirim
    public function sendPesan(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'message' => 'required|string',
        ]);

        //Metode Kirim Lama
        // $response = app(\App\Http\Controllers\WaController::class)->kirimApi($request);

        // // kalau mau ambil hasilnya
        // $data = $response->getData(true);

        // if ($data && isset($data['success']) && $data['success'] == true) {
        //     return response()->json(['status' => 'success', 'data' => $data]);
        // } else {
        //     $errorMessage = $data['message'] ?? 'Gagal mengirim pesan';
        //     return response()->json(['status' => 'failed', 'error' => $errorMessage], 500);
        // }

        $telp = $request->phone;
        $pesan = $request->message;

        if (substr($telp, 0, 1) === '0') {
            $telp = '62' . substr($telp, 1);
        }

        $client = new \GuzzleHttp\Client((['base_uri' => env('SERVER_API_WA')]));
        try {
            $response = $client->request('POST', "/client/sendMessage/" . env('SESSION_WA'), [
                'headers' => [
                    'x-api-key' => null,
                ],
                'json' => [
                    "chatId" => "$telp@c.us",
                    "contentType" => "string",
                    "content" => "$pesan"
                ]
            ]);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());
                // dd($test, 'error pengiriman pesan');

                return response()->json(['status' => 'failed', 'error' => $test->body()], 500);
            }
        }

        $data = json_decode($response->getBody());

        if ($data && $data->success == true) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'failed', 'error' => 'Gagal mengirim pesan'], 500);
        }
    }
}
