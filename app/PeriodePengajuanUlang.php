<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeriodePengajuanUlang extends Model
{
    protected $fillable = [
        'periode',
        'keterangan'
    ];

    public function dataPengajuanUlang()
    {
        return $this->hasMany('App\DataPengajuanUlang');
    }
}
