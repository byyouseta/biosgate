<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebhookMessage extends Model
{
    protected $fillable = [
        'id',
        'session_id',
        'from',
        'to',
        'body',
        'type',
        'timestamp',
        'message_id',
        'raw'
    ];

    protected $casts = [
        'raw' => 'array',
    ];
}
