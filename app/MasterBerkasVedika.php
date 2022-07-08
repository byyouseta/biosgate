<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterBerkasVedika extends Model
{
    protected $fillable = [
        'nama', 'keterangan'
    ];

    public function berkasVedika()
    {
        return $this->hasMany('App\BerkasVedika');
    }
}
