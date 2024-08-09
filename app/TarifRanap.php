<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TarifRanap extends Model
{
    protected $connection = 'mysqlkhanzadummy';
    protected $table = 'jns_perawatan_inap';
    // protected $primaryKey = 'kd_jenis_prw';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kd_jenis_prw', 'nm_perawatan', 'kd_kategori', 'material', 'bhp', 'tarif_tindakandr',
        'tarif_tindakanpr', 'kso', 'menejemen', 'total_byrdr', 'total_byrpr', 'total_byrdrpr', 'kd_pj', 'kd_bangsal', 'status', 'kelas'
    ];
}
