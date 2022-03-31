<?php

namespace App\Http\Controllers;

use App\AkunBayar;
use Illuminate\Http\Request;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        session()->put('ibu', 'Daskboard');
        session()->forget('anak');
        session()->forget('cucu');

        $data = AkunBayar::all();

        return view('home', compact('data'));
    }
}
