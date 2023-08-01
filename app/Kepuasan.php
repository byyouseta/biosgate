<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kepuasan extends Model
{
    protected $fillable = [
        'umur', 'no_hp', 'jk', 'pendidikan', 'pekerjaan', 'penjamin', 'unit', 'pertanyaan1', 'pertanyaan2', 'pertanyaan3',
        'pertanyaan4', 'pertanyaan5', 'pertanyaan6', 'pertanyaan7', 'pertanyaan8', 'pertanyaan9', 'pertanyaan10', 'saran'
    ];
}
