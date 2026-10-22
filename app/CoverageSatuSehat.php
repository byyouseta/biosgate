<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoverageSatuSehat extends Model
{
    protected $fillable = [
        'no_rkm_medis',
        'patient_id',
        'kd_pj',
        'nm_penjab',
        'jenis_penjamin',
        'nomor_peserta',
        'coverage_id',
        'coverage_status',
        'status',
        'pesan_error',
        'response',
        'last_checked_at',
    ];

    protected $casts = [
        'response' => 'array',
        'last_checked_at' => 'datetime',
    ];
}
