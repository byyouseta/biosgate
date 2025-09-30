<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class carePlanRanap extends Model
{
    protected $fillable = [
        'encounter_id',
        'endpoint',
        'method',
        'request_payload',
        'response_payload',
        'status_code',
        'careplan_id'
    ];
}
