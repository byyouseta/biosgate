<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterLoinc extends Model
{
    protected $connection = 'mysqlkhanza';
    protected $table = 'fhir_master_loinc';
}
