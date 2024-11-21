<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KlaimPending extends Model
{
    protected $guard = [];

    public static function getPending($no_sep)
    {
        $cek = KlaimPending::where('no_sep', $no_sep)->first();

        if ($cek) {
            return $cek;
        } else {
            return null;
        }
    }
}
