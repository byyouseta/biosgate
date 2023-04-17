<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kd_bank', 'nama', 'norek', 'namaRek', 'cabang', 'rekening_id', 'noBilyet', 'default', 'tgl_buka'
    ];

    public function SaldoKelolaan()
    {
        return $this->hasMany('App\SaldoKeuangan');
    }

    public function SaldoOperasional()
    {
        return $this->hasMany('App\SaldoOperasional');
    }

    public function SaldoPengelolaan()
    {
        return $this->hasMany('App\SaldoPengelolaan');
    }

    public function Rekening()
    {
        return $this->belongsTo('App\Rekening');
    }
}
