<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TarifOperasi extends Model
{
    protected $connection = 'mysqlkhanzadummy';
    protected $table = 'paket_operasi';
    // protected $primaryKey = 'kd_jenis_prw';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kode_paket', 'nm_perawatan', 'kategori', 'operator1', 'operator2', 'operator3',
        'asisten_operator1', 'asisten_operator2', 'asisten_operator3', 'instrumen', 'dokter_anak', 'perawaat_resusitas',
        'dokter_anestesi', 'asisten_anestesi', 'asisten_anestesi2', 'bidan', 'bidan2', 'bidan3',
        'perawat_luar', 'sewa_ok', 'alat', 'akomodasi', 'bagian_rs', 'omloop', 'omloop2', 'omloop3',
        'omloop4', 'omloop5', 'sarpras', 'dokter_pjanak', 'dokter_umum', 'status', 'kd_pj', 'kelas'
    ];
}
