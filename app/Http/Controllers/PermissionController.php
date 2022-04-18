<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use DB;
use Illuminate\Support\Facades\Crypt;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:permission-list|permission-create|permission-edit|permission-delete', ['only' => ['index']]);
        $this->middleware('permission:permission-create', ['only' => ['store']]);
        $this->middleware('permission:permission-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:permission-delete', ['only' => ['delete']]);
    }

    public function index()
    {
        session()->put('ibu', 'Master Data');
        session()->put('anak', 'List Akses');
        session()->forget('cucu');

        $data = Permission::all();

        return view('permissions.index', compact('data'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nama' => 'required|unique:permissions,name',

        ], [
            'nama.unique' => 'Nama Permission sudah pernah digunakan!'
        ]);

        // $tambah = new Permission();
        // $tambah->name = $request->nama;
        // $tambah->save();

        Permission::create(['name' => $request->input('nama')]);

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        return redirect('/permission');
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $data = Permission::find($id);
        return view('permissions.edit', ['data' => $data]);
    }

    public function update($id, Request $request)
    {

        $this->validate($request, [
            'nama' => 'required|unique:permissions,name,' . $id,

        ], [
            'nama.unique' => 'Nama Permission sudah pernah digunakan!'
        ]);

        $update = Permission::find($id);
        $update->name = $request->nama;
        // $update->keterangan = $request->keterangan;
        $update->save();

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect('/permission');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = Permission::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect('/permission');
    }
}
