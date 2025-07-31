<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'base_url',
        'satker',
        'key',
    ];
}
