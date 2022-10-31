<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasienSehat extends Model
{
    protected $fillable = [
        'nik', 'satu_sehat_id'
    ];
}
