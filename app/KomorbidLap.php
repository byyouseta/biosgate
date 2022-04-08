<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KomorbidLap extends Model
{
    protected $fillable = [
        'id', 'lapId', 'lapKomorbidId', 'komorbidId', 'desc'
    ];

    public function PelaporanCovid()
    {
        return $this->belongsTo('App\PelaporanCovid', 'lapId', 'lapId');
    }
}
