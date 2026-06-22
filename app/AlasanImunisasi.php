<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlasanImunisasi extends Model
{
    protected $fillable = [
        'display',
        'system',
        'code',
    ];

    public function immunizationFhir()
    {
        return $this->hasMany(ImmunizationFhir::class);
    }
}
