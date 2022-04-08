<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VaksinasiLap extends Model
{
    protected $fillable = [
        'id', 'lapId', 'lapVaksinId', 'dosisVaksinId', 'namaDosis', 'jenisVaksinId', 'namaVaksin'
    ];

    public function PelaporanCovid()
    {
        return $this->belongsTo('App\PelaporanCovid', 'lapId', 'lapId');
    }
}
