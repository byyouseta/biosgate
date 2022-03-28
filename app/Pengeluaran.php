<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $fillable = [
        'kd_akun', 'jumlah', 'tgl_transaksi', 'status'
    ];
}
