<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    protected $fillable = [
        'id', 'nama',
    ];

    public static function NamaProvinsi($id)
    {
        $cek = Provinsi::find($id);

        return $cek;
    }
}
