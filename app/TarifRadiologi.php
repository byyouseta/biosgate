<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TarifRadiologi extends Model
{
    protected $connection = 'mysqlkhanzadummy';
    protected $table = 'jns_perawatan_radiologi';
    // protected $primaryKey = 'kd_jenis_prw';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kd_jenis_prw', 'nm_perawatan', 'bagian_rs', 'bhp', 'tarif_tindakan_dokter', 'tarif_perujuk',
        'tarif_tindakan_petugas', 'kso', 'menejemen', 'total_byr', 'kd_pj', 'status', 'kelas'
    ];
}
