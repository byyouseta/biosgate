<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class HakKewajibanPasien extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public static function cekHakKewajiban($id)
    {

        $id = Crypt::decrypt($id);

        $cek = HakKewajibanPasien::where('noRawat', $id)
            ->first();

        if (!empty($cek)) {
            return true;
        } else {
            return false;
        }
    }
}
