<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountSatuSehat extends Model
{
    protected $fillable = [
        'no_rawat',
        'jenis_rawat',
        'patient_id',
        'encounter_id',
        'kd_pj',
        'nm_penjab',
        'coverage_id',
        'account_id',
        'account_status',
        'status',
        'pesan_error',
        'response',
        'sent_at',
        'linked_at',
    ];

    protected function casts(): array
    {
        return [
            'response' => 'array',
            'sent_at' => 'datetime',
            'linked_at' => 'datetime',
        ];
    }
}
