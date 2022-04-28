<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubinstalasiKanker extends Model
{
    protected $fillable = [
        'kode_gabung_sub_instalasi_unit', 'sub_instalasi_unit'
    ];

    public function PelaporanKanker()
    {
        return $this->hasMany('App\PelaporanKanker');
    }
}
