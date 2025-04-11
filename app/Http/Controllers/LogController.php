<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        session()->put('ibu', 'Master Data');
        session()->put('anak', 'User Logs');
        session()->forget('cucu');

        if ($request->input('tanggal'))
            $date = $request->input('tanggal');
        else {
            $date = Carbon::now();
        }

        // Jika ada filter tanggal, ambil log berdasarkan tanggal
        if ($date) {
            $logs = Activity::whereDate('created_at', $date)->get();
        } else {
            $logs = Activity::get();
        }

        // dd($logs, $date);

        return view('masters.logs', compact('logs'));
    }
}
