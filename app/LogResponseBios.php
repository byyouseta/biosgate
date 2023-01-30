<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogResponseBios extends Model
{
    protected $fillable = [
        'tanggal', 'nama_fungsi', 'status_terkirim'
    ];
}
