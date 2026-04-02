<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResponseIgdSatuSehat extends Model
{
    public function tindakanIgdSatuSehat()
    {
        return $this->hasMany('App\TindakanIgdSatuSehat');
    }
}
