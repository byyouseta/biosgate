<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PolyclinicController extends Controller
{
    public function index()
    {
        $poli = Cache::remember(
            'polyclinics',
            300,
            function () {
                return DB::connection('mysqlkhanza')->table('poliklinik')
                    ->select(
                        'kd_poli',
                        'nm_poli',
                        'registrasi'
                    )
                    ->where('status', '1')
                    ->orderBy('nm_poli')
                    ->get();
            }
        );

        return response()->json([
            'success' => true,
            'data' => $poli
        ]);
    }

    public function doctors($kd_poli)
    {
        $doctors = Cache::remember(
            'poli_doctors_' . $kd_poli,
            300,
            function () use ($kd_poli) {
                return DB::connection('mysqlkhanza')
                    ->table('jadwal')
                    ->join('dokter', 'jadwal.kd_dokter', '=', 'dokter.kd_dokter')
                    ->join('poliklinik', 'jadwal.kd_poli', '=', 'poliklinik.kd_poli')
                    ->where('jadwal.kd_poli', $kd_poli)
                    ->select(
                        'dokter.kd_dokter',
                        'dokter.nm_dokter',
                        'poliklinik.nm_poli'
                    )
                    ->distinct()
                    ->orderBy('dokter.nm_dokter')
                    ->get();
            }
        );

        return response()->json([
            'success' => true,
            'data' => $doctors
        ]);
    }

    public function schedules($kd_poli)
    {
        $schedules = Cache::remember(
            'poli_schedules_' . $kd_poli,
            300,
            function () use ($kd_poli) {
                return DB::connection('mysqlkhanza')->table('jadwal')
                    ->join('dokter', 'jadwal.kd_dokter', '=', 'dokter.kd_dokter')
                    ->join('poliklinik', 'jadwal.kd_poli', '=', 'poliklinik.kd_poli')
                    ->where('jadwal.kd_poli', $kd_poli)
                    ->select(
                        'dokter.kd_dokter',
                        'dokter.nm_dokter',
                        'poliklinik.nm_poli',
                        'jadwal.hari_kerja',
                        'jadwal.jam_mulai',
                        'jadwal.jam_selesai'
                    )
                    ->orderByRaw(
                        "FIELD(
                        jadwal.hari_kerja,
                        'Senin',
                        'Selasa',
                        'Rabu',
                        'Kamis',
                        'Jumat',
                        'Sabtu',
                        'Minggu'
                    )"
                    )
                    ->get();
            }
        );

        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }
}
