<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    protected $fillable = [
        'id', 'nama', 'kecamatan_id'
    ];

    public static function NamaKel($id)
    {
        $cek = Kelurahan::find($id);

        return $cek;
    }
}
