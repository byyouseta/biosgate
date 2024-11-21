<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasienSehat extends Model
{
    protected $fillable = [
        'nik',
        'satu_sehat_id'
    ];

    public static function getIdSehat($ktp)
    {
        $cek = PasienSehat::where('nik', $ktp)
            ->first();

        if ($cek) {
            return $cek->satu_sehat_id;
        } else {
            return null;
        }
    }
}
