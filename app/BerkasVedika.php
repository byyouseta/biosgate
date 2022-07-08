<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BerkasVedika extends Model
{
    protected $fillable = [
        'no_rawat', 'master_berkas_vedika_id', 'nama_berkas', 'file'
    ];

    public function masterBerkas()
    {
        return $this->belongsTo('App\MasterBerkasVedika', 'master_berkas_vedika_id', 'id');
    }
}
