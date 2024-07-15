<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VedikaVerif extends Model
{
    protected $fillable = [
        'id', 'noRawat', 'verifikasi', 'status'
    ];

    public function verificator()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public static function cekVerif($id, $statusRawat)
    {
        $cek = VedikaVerif::where('noRawat', $id)
            ->where('statusRawat', $statusRawat)
            ->first();

        return $cek;
    }
}
