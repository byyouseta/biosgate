<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResponseLabSatuSehat extends Model
{
    protected $fillable = [
        'noRawat', 'tgl_registrasi', 'noOrder', 'serviceRequest_id', 'specimen_id', 'report_id'
    ];

    public function responseObservation()
    {
        return $this->hasMany('App\ResponseObservationLab');
    }
}
