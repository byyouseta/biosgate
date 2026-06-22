<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MappingLidNumber extends Model
{
    protected $fillable = [
        'phone',
        'wid',
        'last_checked_at'
    ];
}
