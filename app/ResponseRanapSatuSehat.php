<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResponseRanapSatuSehat extends Model
{
    protected $fillable = [
        'noRawat',
        'tgl_registrasi',
        'encounter_id',
        'assesmen_nadi',
        'assesmen_pernapasan',
        'assesmen_sistol',
        'assesmen_diastol',
        'assesmen_suhu',
        'status_psikologis',
        'diagnosis_primer',
        'diagnosis_sekunder',
        'kondisi_stabil',
        'cara_keluar'
    ];

    public function responseRadiologiSatuSehat()
    {
        return $this->hasMany('App\ResponseRadiologiSatuSehat', 'noRawat', 'noRawat');
    }

    public function tindakanRanapSatuSehat()
    {
        return $this->hasMany('App\TindakanRanapSatuSehat', 'response_ranap_satu_sehat_id', 'id');
    }
}
