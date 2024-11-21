<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SSOController extends Controller
{
    public function redirectToSSOServer()
    {
        // Redirect pengguna ke server SSO dengan parameter yang diperlukan
        $query = http_build_query([
            'client_id' => config('services.sso.client_id'),
            'redirect_uri' => route('sso.callback'),
            'response_type' => 'code',
            'scope' => '', // Scope bisa disesuaikan jika diperlukan
        ]);

        return redirect(config('services.sso.url') . '/oauth/authorize?' . $query);
    }

    public function handleSSOCallback(Request $request)
    {
        // Menukar Authorization Code dengan Access Token
        $response = Http::asForm()->post(config('services.sso.url') . '/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.sso.client_id'),
            'client_secret' => config('services.sso.client_secret'),
            'redirect_uri' => route('sso.callback'),
            'code' => $request->code,
        ]);

        $accessToken = $response->json()['access_token'];

        // Mengambil data user dari server SSO menggunakan access token
        $userResponse = Http::withToken($accessToken)->get(config('services.sso.url') . '/api/user');

        $userArray = $userResponse->json();

        // Cari user berdasarkan email, atau buat user baru
        $user = User::updateOrCreate([
            'email' => $userArray['email'],
        ], [
            'name' => $userArray['name'],
            'email' => $userArray['email'],
        ]);

        // Login user ke aplikasi client
        Auth::login($user);

        return redirect('/home'); // Redirect ke halaman home setelah login berhasil
    }
}
