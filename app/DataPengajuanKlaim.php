<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DataPengajuanKlaim extends Model
{
    protected $fillable = [
        'no_rawat',
        'no_sep',
        'no_kartu',
        'nama_pasien',
        'jk',
        'umur_daftar',
        'status_umur',
        'tgl_registrasi',
        'tgl_lahir',
        'kode_poli',
        'nama_poli',
        'jenis_rawat',
        'periode_klaim_id'
    ];

    public function periodeKlaim()
    {
        return $this->belongsTo('App\PeriodeKlaim');
    }

    public function fraudRajal()
    {
        return $this->hasOne('App\FraudRajal');
    }

    public static function cekPengajuan($no_rawat, $jenis_rawat)
    {
        $cek = DataPengajuanKlaim::where('no_rawat', $no_rawat)
            ->where('jenis_rawat', $jenis_rawat)
            ->first();

        if (empty($cek)) {
            return null;
        } else {
            return $cek;
        }
    }

    public static function cekPengajuanSEP($noSep)
    {
        $cek = DataPengajuanKlaim::where('no_sep', $noSep)
            ->first();

        if (empty($cek)) {
            return null;
        } else {
            return $cek;
        }
    }
}
