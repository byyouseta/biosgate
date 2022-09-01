<?php

namespace App\Http\Controllers;

use App\Setting;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SatuSehatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function tokenSehat()
    {
        $setting = Setting::where('nama', 'satusehat')->first();
        // dd($setting);
        session()->put('base_url', $setting->base_url);
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => $setting->base_url]);
            $response = $client->request('POST', 'oauth2/v1/accesstoken?grant_type=client_credentials', [
                'headers' => [
                    'Content-Type' => "application/x-www-form-urlencoded"
                ],
                'form_params' => [
                    'client_id' => $setting->satker,
                    'client_secret' => $setting->key,
                ]
            ]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }

            // $id = Crypt::encrypt($id);
            // dd($test);
            Session::flash('error', $test->message);

            return redirect()->back()->withInput();
        }

        $data = json_decode($response->getBody());

        // dd($data, $data->status);

        if ($data->status == "approved") {
            session()->put('tokenSatuSehat', $data->access_token);

            $expired = Carbon::now()->addHour();
            session()->put('expiredSatuSehat', $expired);
        }
        // dd(session('tokenSatuSehat'), session('expiredSatuSehat'), $expired);
    }

    public static function getTokenSehat()
    {
        $now = Carbon::now();

        if ((Session::get('tokenSatuSehat') <= $now) && (empty(Session::get('tokenSatuSehat')))) {
            SatuSehatController::tokenSehat();
        }

        // dd(Session::get('tokenSatuSehat'));
    }

    public function patientSehat()
    {
        $nik = 3171022809990001;
        SatuSehatController::getTokenSehat();
        $access_token = Session::get('tokenSatuSehat');
        // dd($access_token);
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('GET', 'fhir-r4/v1/Patient?identifier=https://fhir.kemkes.go.id/id/nik|' . $nik, [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ]
            ]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }
            // dd($test);
            // $id = Crypt::encrypt($id);
            Session::flash('error', $test->message);

            return redirect()->back()->withInput();
        }

        $data = json_decode($response->getBody());
        $data = $data->entry;

        // dd($data->resource->id);

        foreach ($data as $responseData) {
            dd($responseData->resource);
        }
    }

    public function practitioner()
    {
        $nik = 3273246309870001;
        SatuSehatController::getTokenSehat();
        $access_token = Session::get('tokenSatuSehat');
        // dd($access_token);
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('GET', 'fhir-r4/v1/Practitioner?identifier=https://fhir.kemkes.go.id/id/nik|' . $nik, [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ]
            ]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }
            dd($test);
            // $id = Crypt::encrypt($id);
            Session::flash('error', $test->message);

            return redirect()->back()->withInput();
        }

        $data = json_decode($response->getBody());
        $data = $data->entry;

        // dd($data->resource->id);

        foreach ($data as $responseData) {
            dd($responseData->resource);
        }
    }
}
