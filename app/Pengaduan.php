<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $fillable = [
        'nama', 'no_hp', 'email', 'penerima', 'punya_rm', 'no_rm', 'nama_pasien', 'lahir_pasien', 'waktu_kejadian', 'tempat_kejadian', 'pembiayaan', 'pendaftaran_online',
        'pendaftaran_rajal', 'pendaftaran_ranap', 'pendaftaran_igd', 'admin_bpjs', 'petugas_dr_sp', 'petugas_dr_umum', 'petugas_dr_gigi', 'petugas_perawat',
        'petugas_bidan', 'petugas_psikolog', 'petugas_apoteker', 'petugas_radiografer', 'petugas_fisioterapi', 'petugas_konselor',
        'petugas_ahli_gizi', 'petugas_administrasi', 'petugas_kebersihan', 'petugas_parkir', 'petugas_satpam', 'petugas_kasir',
        'petugas_rohaniawan', 'petugas_lainnya', 'layanan_poli_reg', 'layanan_poli_eks', 'layanan_ranap', 'layanan_igd', 'layanan_icu',
        'layanan_farmasi', 'layanan_jenazah', 'layanan_lab', 'layanan_mcu', 'layanan_hemodialisa', 'layanan_fisioterapi', 'layanan_radioterapi',
        'layanan_radiologi', 'layanan_lainnya', 'fasilitas_parkir', 'fasilitas_taman', 'fasilitas_ambulan', 'fasilitas_toilet', 'fasilitas_tunggu',
        'fasilitas_lainnya', 'deskripsi', 'nilai_gangguan'
    ];
}
