<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TindakanIgdSatuSehat extends Model
{
    protected $fillable = [
        'response_igd_satu_sehat_id',
        'procedure_id'
    ];

    public function responseIgdSatuSehat()
    {
        return $this->belongsTo('App\ResponseIgdSatuSehat', 'response_igd_satu_sehat_id', 'id');
    }
}
