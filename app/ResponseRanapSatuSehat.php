<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResponseRanapSatuSehat extends Model
{
    protected $fillable = [
        'noRawat',
        'tgl_registrasi',
        'encounter_id',
        'assesmen_nadi',
        'assesmen_pernapasan',
        'assesmen_sistol',
        'assesmen_diastol',
        'assesmen_suhu',
        'status_psikologis',
        'diagnosis_primer',
        'diagnosis_sekunder',
        'kondisi_stabil',
        'cara_keluar'
    ];
}
