<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KamarController extends Controller
{
    public function index()
    {
        $dataKamar = DB::connection('mysqlkhanza')->table('kamar')
            ->join('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->select(
                'kamar.kd_kamar',
                'kamar.kd_bangsal',
                'kamar.trf_kamar',
                'kamar.status',
                'kamar.kelas',
                'kamar.statusdata',
                'bangsal.nm_bangsal',
                'bangsal.status as status_bangsal'
            )
            ->orderBy('kamar.kd_bangsal')
            ->get();

        if ($dataKamar->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data kamar tidak ditemukan'
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'data' => $dataKamar
            ], 200);
        }
    }
}
