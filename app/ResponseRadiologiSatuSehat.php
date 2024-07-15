<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ResponseRadiologiSatuSehat extends Model
{
    public static function getDataPasien($noRawat)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien'
            )
            ->where('reg_periksa.no_rawat', $noRawat)
            ->first();

        if (!empty($data)) {
            return $data;
        } else {
            return null;
        }
    }
}
