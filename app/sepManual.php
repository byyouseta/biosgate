<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sepManual extends Model
{
    protected $fillable = [
        'noRawat', 'noSep', 'tandaTangan'
    ];
}
