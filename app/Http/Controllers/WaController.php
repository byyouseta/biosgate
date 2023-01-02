<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

class WaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'Pesan');
        session()->put('anak', 'Setting');
        session()->forget('cucu');

        $data = Setting::where('nama', 'pesan')->first();
        //inisiasi data awal
        $ipServer = null;
        $portServer = null;
        $status = 'offline';

        if (!empty($data)) {
            $pecah = explode(':', $data->base_url);
            // dd($data, $pecah);

            $host = $pecah[0];
            $port = $pecah[1];

            // foreach ($ports as $port) {
            $connection = @fsockopen($host, $port);

            if (is_resource($connection)) {
                // echo '<h2>' . $host . ':' . $port . ' ' . '(' . getservbyport($port, 'tcp') . ') is open.</h2>' . "\n";

                fclose($connection);

                $status = 'online';
            }
            // else {
            //     echo '<h2>' . $host . ':' . $port . ' is not responding.</h2>' . "\n";
            // }
            // }
        }



        // dd($connection, $status);

        return view('pesan.setting_pesan', compact('ipServer', 'portServer', 'status'));
    }
}
