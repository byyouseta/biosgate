<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaldoOperasional extends Model
{
    public function bank()
    {
        return $this->belongsTo('App\Bank');
    }
}
