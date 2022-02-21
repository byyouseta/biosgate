<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AkunBayar extends Model
{
    protected $connection = 'mysqlkhanza';
    protected $table = 'akun_bayar';
}
