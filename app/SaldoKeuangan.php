<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaldoKeuangan extends Model
{
    protected $fillable = [
        'bank_id', 'saldo', 'kd_rek', 'tgl_transaksi', 'status'
    ];

    public function bank()
    {
        return $this->belongsTo('App\Bank');
    }
}
