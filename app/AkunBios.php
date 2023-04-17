<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AkunBios extends Model
{
    protected $fillable = [
        'akun', 'uraian'
    ];
}
