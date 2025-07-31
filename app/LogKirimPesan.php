<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogKirimPesan extends Model
{
    protected $fillable = [
        'no_rm',
        'tgl_periksa',
        'template_id',
        'status',
        'keterangan'
    ];
}
