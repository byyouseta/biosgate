<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogErrorSatuSehat extends Model
{
    protected $fillable = [
        'subject', 'keterangan'
    ];
}
