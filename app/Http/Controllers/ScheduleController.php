<?php

namespace App\Http\Controllers;

use App\ScheduleUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'Setting');
        session()->put('anak', 'Schedule Update');
        session()->forget('cucu');

        $data = ScheduleUpdate::all();

        return view('schedule', compact('data'));
    }

    public function store(Request $request)
    {
        // dd($request);

        $data = new ScheduleUpdate();
        $data->waktu_mulai = $request->waktu_mulai;
        $data->waktu_selesai = $request->waktu_selesai;
        $data->save();

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        return redirect('/schedule');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = ScheduleUpdate::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }
}
