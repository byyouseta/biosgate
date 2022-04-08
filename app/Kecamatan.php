<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $fillable = [
        'id', 'nama', 'kab_kota_id'
    ];

    public static function NamaKec($id)
    {
        $cek = Kecamatan::find($id);

        return $cek;
    }
}
