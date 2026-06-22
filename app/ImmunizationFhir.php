<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImmunizationFhir extends Model
{
    protected $fillable = [
        'kd_jenis_prw',
        'kode_barang',
        'kode_kfa',
        'display_kfa',
        'alasan_imunisasi_id',
    ];

    public function alasanImunisasi()
    {
        return $this->belongsTo(AlasanImunisasi::class, 'alasan_imunisasi_id');
    }
}
