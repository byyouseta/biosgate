<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penyakit extends Model
{
    protected $connection = 'mysqlkhanza';
    protected $table = 'penyakit';

    public function KankerDiagnosaMasuk()
    {
        return $this->hasOne('App\PelaporanKanker', 'kd_penyakit', 'id_diagnosa_masuk');
    }

    public function KankerDiagnosaUtama()
    {
        return $this->hasOne('App\PelaporanKanker', 'kd_penyakit', 'id_diagnosa_utama');
    }

    public function KankerDiagnosaSekunder()
    {
        return $this->hasOne('App\PelaporanKanker', 'kd_penyakit', 'id_diagnosa_sekunder1');
    }

    public function KankerDiagnosaSekunder2()
    {
        return $this->hasOne('App\PelaporanKanker', 'kd_penyakit', 'id_diagnosa_sekunder2');
    }

    public function KankerDiagnosaSekunder3()
    {
        return $this->hasOne('App\PelaporanKanker', 'kd_penyakit', 'id_diagnosa_sekunder3');
    }

    public function KankerKematian1a()
    {
        return $this->hasOne('App\PelaporanKanker', 'kd_penyakit', 'id_sebab_kematian_langsung_1a');
    }

    public function KankerKematian1b()
    {
        return $this->hasOne('App\PelaporanKanker', 'kd_penyakit', 'id_sebab_kematian_antara_1b');
    }

    public function KankerKematian1c()
    {
        return $this->hasOne('App\PelaporanKanker', 'kd_penyakit', 'id_sebab_kematian_antara_1c');
    }

    public function KankerKematian1d()
    {
        return $this->hasOne('App\PelaporanKanker', 'kd_penyakit', 'id_sebab_kematian_dasar_1d');
    }
}
