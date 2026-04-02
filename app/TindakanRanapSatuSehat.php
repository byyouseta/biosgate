<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TindakanRanapSatuSehat extends Model
{
    protected $fillable = [
        'response_ranap_satu_sehat_id',
        'procedure_id'
    ];

    public function responseRanapSatuSehat()
    {
        return $this->belongsTo('App\ResponseRanapSatuSehat', 'response_ranap_satu_sehat_id', 'id');
    }
}
