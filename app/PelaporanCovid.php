<?php

namespace App;

use finfo;
use Illuminate\Database\Eloquent\Model;

class PelaporanCovid extends Model
{
    protected $fillable = [
        'lapId', 'noRawat',
        'kewarganegaraan', 'nik', 'noPassport',
        'asalPasien', 'noRm', 'namaPasien', 'inisial',
        'tgl_lahir', 'email', 'nohp', 'jk',
        'provinsi', 'kabKota', 'kecamatan', 'tgl_masuk',
        'jenis_pasien', 'varian_covid', 'status_pasien', 'status_coinsiden',
        'status_rawat', 'alat_oksigen', 'penyintas', 'tgl_gejala',
        'kelompok_gejala', 'demam', 'batuk', 'pilek',
        'sakit_tenggorokan', 'sesak_napas', 'lemas', 'nyeri_otot',
        'mual_muntah', 'diare', 'anosmia', 'napas_cepat',
        'frek_napas', 'distres_pernapasan', 'lainnya', 'status_update'
    ];

    public function KomorbidLap()
    {
        return $this->hasMany('App\KomorbidLap', 'lapId', 'lapId');
    }

    public function TerapiLap()
    {
        return $this->hasMany('App\TerapiLap', 'lapId', 'lapId');
    }

    public function VaksinLap()
    {
        return $this->hasMany('App\VaksinasiLap', 'lapId', 'lapId');
    }

    public function PemeriksaanLab()
    {
        return $this->hasMany('App\PemeriksaanLab', 'lapId', 'lapId');
    }

    public function DiagnosaLap()
    {
        return $this->hasMany('App\Diagnosalap', 'lapId', 'lapId');
    }

    public function Pulang()
    {
        return $this->hasMany('App\Pulang', 'lapId', 'lapId');
    }

    public static function cekLapor($id)
    {
        $cek = PelaporanCovid::where('noRawat', '=', $id)
            ->count();
        return $cek;
    }
}
