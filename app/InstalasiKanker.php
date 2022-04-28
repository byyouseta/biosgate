<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstalasiKanker extends Model
{
    protected $fillable = [
        'kode_instalasi_unit', 'instalasi_unit'
    ];

    public function PelaporanKanker()
    {
        return $this->hasMany('App\PelaporanKanker');
    }
}
