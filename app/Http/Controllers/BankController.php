<?php

namespace App\Http\Controllers;

use App\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class BankController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'Master');
        session()->put('anak', 'Bank');

        $data = Bank::all();
        $bank = BankController::RefBank();

        return view('masters.banks', compact('data', 'bank'));
    }

    public function store(Request $request)
    {
        $str = $request->nama;
        $split = explode("-", $str);
        // dd($request);

        $data = new Bank();
        // $lokal->id = 1;
        $data->kd_bank = $split[0];
        $data->nama = $split[1];
        $data->norek = $request->norek;
        $data->save();

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        return redirect('/master/bank');
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);

        $data = Bank::find($id);
        $bank = BankController::RefBank();

        return view('masters.banks_edit', compact('data', 'bank'));
    }

    public function update($id, Request $request)
    {

        $str = $request->nama;
        $split = explode("-", $str);

        $data = Bank::find($id);
        $data->kd_bank = $split[0];
        $data->nama = $split[1];
        $data->norek = $request->norek;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diupdate!');

        return redirect('/master/bank');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = Bank::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }

    public static function RefBank()
    {
        ClientController::token();

        //Ambil Data Ref Akun
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', 'ws/ref/bank');
        $bank = json_decode($response->getBody());
        $bank = $bank->data;

        // dd($bank);
        return $bank;
    }
}
