<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KlaimCair extends Model
{
    protected $guard = [];

    public static function getCair($no_sep)
    {
        $cek = KlaimCair::where('no_sep', $no_sep)->first();

        if ($cek) {
            return number_format($cek->disetujui, 0, ',', '.');
        } else {
            return null;
        }
    }

    public static function getBill($no_rawat)
    {
        //dari Payroll gak update nantinya
        // $cek = DB::connection('mysqlpayroll')->table('remun_barus')
        //     ->select(
        //         'remun_barus.no_rawat',
        //         'remun_barus.biaya_riil'

        //     )
        //     ->where('remun_barus.no_rawat', $no_rawat)
        //     ->first();

        $cek = DB::connection('mysqlkhanza')->table('billing')
            ->where('billing.no_rawat', '=', $no_rawat)
            ->sum('billing.totalbiaya');

        if ($cek) {
            return number_format($cek, 0, ',', '.');
        } else {
            return null;
        }
    }
}
