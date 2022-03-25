<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'Setting');
        session()->forget('anak');

        $data = Setting::all();

        return view('setting', compact('data'));
    }

    public function store(Request $request)
    {
        $lokal = new Setting();
        // $lokal->id = 1;
        $lokal->nama = $request->nama;
        $lokal->base_url = $request->base_url;
        $lokal->satker = $request->kode_satker;
        $lokal->key = $request->key;
        $lokal->save();

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        return redirect('/setting');
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);

        $data = Setting::find($id);

        return view('setting_edit', compact('data'));
    }

    public function update($id, Request $request)
    {
        $lokal = Setting::find($id);
        $lokal->nama = $request->nama;
        $lokal->base_url = $request->base_url;
        $lokal->satker = $request->kode_satker;
        $lokal->key = $request->key;
        $lokal->save();

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        return redirect('/setting');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = Setting::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }
}
