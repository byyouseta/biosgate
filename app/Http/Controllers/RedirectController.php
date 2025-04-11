<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
}
