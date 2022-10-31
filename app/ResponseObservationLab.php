<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResponseObservationLab extends Model
{
    protected $fillable = [
        'response_lab_satu_sehat_id', 'observation_id'
    ];

    public function responseLab()
    {
        return $this->belongsTo('App\ResponseLabSatuSehat');
    }
}
