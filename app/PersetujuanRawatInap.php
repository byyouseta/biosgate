<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersetujuanRawatInap extends Model
{
    protected $fillable = [
        'nama_pj',
        'tempat_lahir_pj',
        'tanggal_lahir_pj',
        'jenis_kelamin_pj',
        'alamat_pj',
        'pekerjaan_pj',
        'no_ktp_pj',
        'no_telepon_pj',
        'hubungan_pj',
        'nama_pasien',
        'tempat_lahir_pasien',
        'tanggal_lahir_pasien',
        'alamat_pasien',
        'no_rm',
        'cara_bayar',
        'cara_bayar_lainnya',
        'kelas_rawat',
        'pindah_kelas_rawat',
        'no_rawat',
        'petugas_id',
        'tanda_tangan'
    ];

    protected $casts = [
        'tanggal_lahir_pj' => 'date',
        'tanggal_lahir_pasien' => 'date',
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
