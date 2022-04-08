<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TerapiLap extends Model
{
    protected $fillable = [
        'id', 'lapId', 'lapTerapiId', 'terapiId', 'desc', 'jumlah'
    ];

    public function PelaporanCovid()
    {
        return $this->belongsTo('App\PelaporanCovid', 'lapId', 'lapId');
    }
}
