<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PelaporanKanker extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'idReg', 'noRawat', 'nik', 'nama_pasien', 'id_jenis_kelamin', 'tanggal_lahir', 'alamat', 'id_provinsi', 'id_kab_kota', 'id_kecamatan', 'id_kelurahan', 'alamat_tinggal',
        'id_provinsi_tinggal', 'id_kab_kota_tinggal', 'id_kecamatan_tinggal', 'id_kelurahan_tinggal', 'kontak_pasien', 'tanggal_masuk', 'id_cara_masuk_pasien',
        'id_asal_rujukan_pasien', 'asal_rujukan_pasien_fasyankes_lainnya', 'id_diagnosa_masuk', 'id_sub_instalasi_unit', 'id_diagnosa_utama', 'id_diagnosa_sekunder1',
        'id_diagnosa_sekunder2', 'id_diagnosa_sekunder3', 'tanggal_diagnosa', 'tanggal_keluar', 'id_cara_keluar', 'id_keadaan_keluar', 'id_sebab_kematian_langsung_1a',
        'id_sebab_kematian_antara_1b', 'id_sebab_kematian_antara_1c', 'id_sebab_kematian_dasar_1d', 'id_kondisi_yg_berkontribusi_thdp_kematian', 'sebab_dasar_kematian',
        'id_cara_bayar', 'nomor_bpjs'
    ];

    public function diagnosaMasuk()
    {
        return $this->belongsTo('App\Penyakit', 'id_diagnosa_masuk', 'kd_penyakit');
    }

    public function diagnosaUtama()
    {
        return $this->belongsTo('App\Penyakit', 'id_diagnosa_utama', 'kd_penyakit');
    }

    public function diagnosaSekunder()
    {
        return $this->belongsTo('App\Penyakit', 'id_diagnosa_sekunder1', 'kd_penyakit');
    }

    public function diagnosaSekunder2()
    {
        return $this->belongsTo('App\Penyakit', 'id_diagnosa_sekunder2', 'kd_penyakit');
    }

    public function diagnosaSekunder3()
    {
        return $this->belongsTo('App\Penyakit', 'id_diagnosa_sekunder3', 'kd_penyakit');
    }

    public function kematian1a()
    {
        return $this->belongsTo('App\Penyakit', 'id_sebab_kematian_langsung_1a', 'kd_penyakit');
    }

    public function kematian1b()
    {
        return $this->belongsTo('App\Penyakit', 'id_sebab_kematian_antara_1b', 'kd_penyakit');
    }

    public function kematian1c()
    {
        return $this->belongsTo('App\Penyakit', 'id_sebab_kematian_antara_1c', 'kd_penyakit');
    }

    public function kematian1d()
    {
        return $this->belongsTo('App\Penyakit', 'id_sebab_kematian_dasar_1d', 'kd_penyakit');
    }

    public function SubInstalasi()
    {
        return $this->belongsTo('App\SubinstalasiKanker', 'id_sub_instalasi_unit', 'kode_gabung_sub_instalasi_unit');
    }

    public function Instalasi()
    {
        return $this->belongsTo('App\InstalasiKanker', 'id_instalasi_unit', 'kode_instalasi_unit');
    }
}
