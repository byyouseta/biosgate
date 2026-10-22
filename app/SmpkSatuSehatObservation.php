<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmpkSatuSehatObservation extends Model
{
    protected $fillable = [
        'smpk_case_id',
        'variable_key',
        'variable_name',
        'loinc_code',
        'loinc_display',
        'value_type',
        'value_data',
        'observation_id',
        'request_payload',
        'response_payload',
        'status',
        'attempts',
        'http_status',
        'last_attempt_at',
        'sent_at',
        'last_error',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'value_data' => 'array',
            'request_payload' => 'array',
            'response_payload' => 'array',
            'metadata' => 'array',
            'last_attempt_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function smpkCase()
    {
        return $this->belongsTo(
            SmpkSatuSehatCase::class,
            'smpk_case_id'
        );
    }

    public function logs()
    {
        return $this->hasMany(
            SmpkSatuSehatLog::class,
            'smpk_resource_id'
        );
    }
}
