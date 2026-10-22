<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResumeMedisSatuSehat extends Model
{
    protected $table = 'resume_medis_satu_sehats';

    protected $fillable = [
        'no_rawat',
        'jenis_rawat',
        'encounter_id',
        'composition_id',
        'status',
        'tanggal_kirim'
    ];
}
