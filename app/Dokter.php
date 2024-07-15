<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    protected $connection = 'mysqlkhanza';
    protected $table = 'dokter';
}
