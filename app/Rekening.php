<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    protected $fillable = [
        'kode', 'uraian'
    ];

    public function SaldoKeuangan()
    {
        return $this->hasMany('App\SaldoKeuangan');
    }

    public function Bank()
    {
        return $this->hasMany('App\Bank');
    }
}
