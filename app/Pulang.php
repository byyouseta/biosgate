<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pulang extends Model
{
    protected $fillable = [
        'lapId', 'lapPulangId', 'tgl_pulang', 'statusPulangId', 'statusPulang', 'penyebabKematianId', 'penyebabKematian', 'penyebabKematianLangsungId',
        'penyebabKematianLangsung', 'statusPasienMeninggalId', 'statusPasienMeninggal', 'komorbidCoinsidenId', 'komorbidCoinsiden'
    ];

    public function PelaporanCovid()
    {
        return $this->belongsTo('App\PelaporanCovid', 'lapId', 'lapId');
    }
}
