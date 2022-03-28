<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaldoKeuangan extends Model
{
    protected $fillable = [
        'kd_bank', 'norek', 'saldo', 'kd_rek', 'tgl_transaksi', 'status'
    ];
}
