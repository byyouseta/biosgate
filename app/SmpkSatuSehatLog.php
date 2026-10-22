<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmpkSatuSehatLog extends Model
{
    protected $fillable = [
        'smpk_case_id',
        'smpk_observation_id',
        'smpk_composition_id',
        'attempt_no',
        'http_method',
        'endpoint',
        'http_status',
        'request_payload',
        'response_payload',
        'result',
        'error_message',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'request_payload'  => 'array',
            'response_payload' => 'array',
            'attempted_at'     => 'datetime',
        ];
    }

    public function smpkCase(): BelongsTo
    {
        return $this->belongsTo(
            SmpkSatuSehatCase::class,
            'smpk_case_id'
        );
    }

    public function observation(): BelongsTo
    {
        return $this->belongsTo(
            SmpkSatuSehatObservation::class,
            'smpk_observation_id'
        );
    }

    public function composition(): BelongsTo
    {
        return $this->belongsTo(
            SmpkSatuSehatComposition::class,
            'smpk_composition_id'
        );
    }
}
