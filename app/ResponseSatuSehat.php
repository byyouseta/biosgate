<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResponseSatuSehat extends Model
{
    protected $fillable = [
        'noRawat', 'tgl_registrasi', 'encounter_id', 'condition_id', 'condition2_id', 'heart_id', 'respiratory_id', 'systol_id', 'diastol_id', 'temperature_id',
        'procedure_id', 'composition_id'
    ];
}
