<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BalasPesan extends Model
{
    protected $fillable = [
        'no_hp',
        'last_replied_at',
        'reply_count',
        'auto_replied',
        'last_message',
    ];
}
