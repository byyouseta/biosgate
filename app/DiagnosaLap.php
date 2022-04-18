<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiagnosaLap extends Model
{
    protected $fillable = [
        'id', 'lapId', 'lapDiagnosaId', 'diagnosaLevelId', 'namaDiagnosa', 'diagnosaId'
    ];

    public function PelaporanCovid()
    {
        return $this->belongsTo('App\PelaporanCovid', 'lapId', 'lapId');
    }

    public static function DiagnosaCek($id, $ids)
    {
        $cek = DiagnosaLap::where('diagnosaId', $id)
            ->where('lapId', $ids)
            ->get();
        // dd($cek);

        return $cek;
    }
}
