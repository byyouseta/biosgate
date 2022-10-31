<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResponseMedicationSatuSehat extends Model
{
    protected $fillable = [
        'noRawat', 'tgl_registrasi', 'noResep', 'medication1', 'medicationRequest', 'medication2', 'medicationDispence'
    ];
}
