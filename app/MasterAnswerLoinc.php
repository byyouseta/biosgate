<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterAnswerLoinc extends Model
{
    protected $connection = 'mysqlkhanza';
    protected $table = 'fhir_master_answerloinc';
}
