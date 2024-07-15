<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeriodeKlaim extends Model
{
    protected $fillable = [
        'periode', 'keterangan'
    ];

    public function dataPengajuan()
    {
        return $this->hasMany('App\DataPengajuanKlaim');
    }

    public function dataPengajuanKronis()
    {
        return $this->hasMany('App\DataPengajuanKronis');
    }
}
