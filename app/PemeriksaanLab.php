<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PemeriksaanLab extends Model
{
    protected $fillable = [
        'id', 'lapId', 'lapPemeriksaanId', 'jenisPemeriksaanId', 'namaPemeriksaan', 'hasilPemeriksaanId', 'tgl_hasil'
    ];

    public function PelaporanCovid()
    {
        return $this->belongsTo('App\PelaporanCovid', 'lapId', 'lapId');
    }
}
