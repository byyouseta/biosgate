<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorScheduleController extends Controller
{
    public function index()
    {
        $jadwal = DB::connection('mysqlkhanza')->table('jadwal')
            ->join('dokter', 'jadwal.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'jadwal.kd_poli', '=', 'poliklinik.kd_poli')
            ->select(
                'dokter.kd_dokter',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'jadwal.hari_kerja',
                'jadwal.jam_mulai',
                'jadwal.jam_selesai'
            )
            ->get();

        return response()->json([
            'success' => true,
            'data' => $jadwal
        ]);
    }
}
