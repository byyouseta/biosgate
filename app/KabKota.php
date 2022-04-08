<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KabKota extends Model
{
    protected $fillable = [
        'id', 'nama', 'provinsi_id'
    ];

    public static function NamaKabKota($id)
    {
        $cek = KabKota::find($id);

        return $cek;
    }
}
