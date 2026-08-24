<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralConsent extends Model
{
    protected $fillable = [
        'noRawat',
        'keyakinan1',
        'keyakinan2',
        'keyakinan3',
        'keyakinan4',
        'privasi1',
        'privasi2',
        'privasi3',
        'namaPj',
        'tglLahirPj',
        'umurPj',
        'alamatPj',
        'dpjp',
        'user_id',
        'tandaTangan'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
