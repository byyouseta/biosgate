<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::connection('mysqlkhanza')->table('dokter')
            ->select(
                'kd_dokter',
                'nm_dokter',
                'jk',
                'kd_sps',
                'status'
            )
            ->where('status', '1')
            ->whereNotIn('kd_sps', ['-', 'Kons']);

        if ($request->filled('search')) {

            $query->where(
                'nm_dokter',
                'like',
                '%' . $request->search . '%'
            );
        }

        $doctors = $query->orderBy('nm_dokter')
            ->get();

        return response()->json([
            'success' => true,
            'total' => $doctors->count(),
            'data' => $doctors
        ]);
    }

    public function show($kd_dokter)
    {
        $doctor = DB::connection('mysqlkhanza')->table('dokter')
            ->where('kd_dokter', $kd_dokter)
            ->first();

        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Dokter tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $doctor
        ]);
    }

    public function schedules($kd_dokter)
    {
        $doctor = DB::connection('mysqlkhanza')->table('dokter')
            ->where('kd_dokter', $kd_dokter)
            ->first();

        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Dokter tidak ditemukan'
            ], 404);
        }

        $schedules = Cache::remember(
            'doctor_schedule_' . $kd_dokter,
            300,
            function () use ($kd_dokter) {
                return DB::connection('mysqlkhanza')
                    ->table('jadwal')
                    ->join('poliklinik', 'jadwal.kd_poli', '=', 'poliklinik.kd_poli')
                    ->where('jadwal.kd_dokter', $kd_dokter)
                    ->select(
                        'jadwal.hari_kerja',
                        'jadwal.jam_mulai',
                        'jadwal.jam_selesai',
                        'poliklinik.nm_poli'
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
            'doctor' => [
                'kd_dokter' => $doctor->kd_dokter,
                'nm_dokter' => $doctor->nm_dokter
            ],
            'schedules' => $schedules
        ]);
    }
}
