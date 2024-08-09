<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FraudRanap extends Model
{
    protected $fillable = [
        'data_pengajuan_klaim_id', 'periode_klaim_id', 'up_coding', 'phantom_billing', 'cloning', 'inflated_bills', 'pemecahan',
        'rujukan_semu', 'repeat_billing', 'prolonged_los', 'manipulasi_kels',
        're_admisi', 'kesesuaian_tindakan', 'tagihan_tindakan', 'klarifikasi', 'keterangan'
    ];

    public function periodeKlaim()
    {
        return $this->belongsTo('App\PeriodeKlaim');
    }

    public function dataPengajuan()
    {
        return $this->belongsTo('App\DataPengajuanKlaim', 'data_pengajuan_klaim_id', 'id');
    }

    public static function checkFraud($id)
    {
        $cek = FraudRanap::where('data_pengajuan_klaim_id', $id)->get();

        // dd($id, $cek);

        if ($cek->count() > 0) {
            // dd('true');
            return true;
        } else {
            return false;
        }
    }
}
