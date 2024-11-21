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
            return $cek->disetujui;
        } else {
            // $cek = KlaimPending::where('no_sep', $no_sep)->first();

            // dd($cek);
            return null;
        }
    }

    public static function getBill($no_rawat)
    {
        $cek = DB::connection('mysqlkhanza')->table('billing')
            ->where('billing.no_rawat', '=', $no_rawat)
            ->sum('billing.totalbiaya');

        if ($cek) {
            return $cek;
        } else {
            return null;
        }
    }

    public static function getObatKronis($no_rawat)
    {
        $cek = DB::connection('mysqlkhanza')->table('resep_obat')
            ->join('resep_obat_kronis', 'resep_obat_kronis.no_resep', '=', 'resep_obat.no_resep')
            ->select(
                'resep_obat.no_rawat',
                'resep_obat.status',
                'resep_obat_kronis.no_resep',
                'resep_obat_kronis.obat_kronis'
            )
            ->where('resep_obat.no_rawat', '=', $no_rawat)
            // ->get();
            ->sum(DB::raw("CAST(REPLACE(REPLACE(resep_obat_kronis.obat_kronis, ',', ''), '.', '') AS UNSIGNED)"));
        // dd($cek);

        // $nilai = 0;

        // foreach ($cek as $data) {
        //     $nilai = $nilai + KlaimCair::_toInt($data->obat_kronis);
        // }

        // return $nilai;

        return $cek;
    }

    static function _toInt($str)
    {
        return (int)preg_replace("/([^0-9\\.])/i", "", $str);
    }
}
