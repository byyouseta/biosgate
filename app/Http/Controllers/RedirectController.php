<?php

namespace App\Http\Controllers;

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

    //Untuk API dari WA

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


        return response()->json(['status' => 'stored']);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required|numeric',
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

        $formatPesan = "Untuk mengaktifkan reset Password ePayroll RSUP Surakarta, masukkan kode OTP: $request->otp. Berlaku selama 5 menit. JANGAN PERNAH membagikan kode ini kepada orang lain dalam keadaan apa pun.";

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
}
