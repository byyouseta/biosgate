<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DataPengajuanKronis extends Model
{
    protected $guarded = [];

    public function periodeKlaim()
    {
        return $this->belongsTo('App\PeriodeKlaim');
    }

    public static function cekPengajuanKronis($no_rawat)
    {
        $cek = DataPengajuanKronis::where('no_rawat', $no_rawat)
            ->first();

        if (empty($cek)) {
            return null;
        } else {
            return $cek;
        }
    }
}
