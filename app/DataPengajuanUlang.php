<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DataPengajuanUlang extends Model
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
        'periode_pengajuan_ulang_id'
    ];

    public function periodePengajuanUlang()
    {
        return $this->belongsTo('App\PeriodePengajuanUlang');
    }
}
