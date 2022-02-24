<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleUpdate extends Model
{
    protected $fillable = [
        'waktu_mulai', 'waktu_selesai'
    ];
}
