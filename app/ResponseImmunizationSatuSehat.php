<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResponseImmunizationSatuSehat extends Model
{
    protected $fillable = [
        'noRawat',
        'tgl_registrasi',
        'kode_barang',
        'kd_jenis_prw',
        'encounter_id',
        'patient_id',
        'practitioner_id',
        'immunization_id',
        'kode_kfa',
        'display_kfa',
        'tanggal_kirim',
        'response_raw'
    ];
}
