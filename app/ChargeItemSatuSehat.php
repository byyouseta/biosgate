<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChargeItemSatuSehat extends Model
{
    protected $fillable = [
        'no_rawat',
        'no_transaksi',
        'jenis_rawat',
        'source_table',
        'jenis_item',
        'kode_item',
        'nama_item',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'patient_id',
        'encounter_id',
        'kptl_code',
        'kptl_display',
        'performer_id',
        'occurrence_start',
        'occurrence_end',
        'service_type',
        'service_id',
        'charge_item_id',
        'status',
        'pesan_error',
        'response',
        'sent_at'
    ];
}
