<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class SepController extends Controller
{
    public function index()
    {
        $data = "testtesttest";
        $secretKey = "secretkey";
        // Computes the timestamp
        date_default_timezone_set('UTC');
        $date = Carbon::now()->locale('UTC')->format('Y-m-d H:i:s');
        $tStamp = strval(time() - strtotime($date));
        // Computes the signature by hashing the salt with the secret key as the key
        $signature = hash_hmac('sha256', $data . "&" . $tStamp, $secretKey, true);

        // base64 encode�
        $encodedSignature = base64_encode($signature);

        // urlencode�
        // $encodedSignature = urlencode($encodedSignature);

        dd($data, $tStamp, $encodedSignature, $date);
    }
}
