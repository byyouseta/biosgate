<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaldoPengelolaan extends Model
{
    public function bank()
    {
        return $this->belongsTo('App\Bank');
    }
}
