<?php

namespace App\Http\Controllers;

use App\Kepuasan;
use App\Pengaduan;
use Illuminate\Http\Request;

class DataSurveiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function pengaduan()
    {
        session()->put('ibu', 'Survei');
        session()->put('anak', 'Data Pengaduan');
        session()->forget('cucu');

        $data = Pengaduan::all();

        return view('survei.data_pengaduan', compact('data'));
    }

    public function kepuasan()
    {
        session()->put('ibu', 'Survei');
        session()->put('anak', 'Data Kepuasan');
        session()->forget('cucu');

        $data = Kepuasan::all();

        return view('survei.data_kepuasan', compact('data'));
    }
}
