<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePelaporanKankersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pelaporan_kankers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('idReg');
            $table->string('noRawat', 20);
            $table->string('nik', 20);
            $table->string('nama_pasien', 100);
            $table->tinyInteger('id_jenis_kelamin');
            $table->date('tanggal_lahir');
            $table->string('alamat', 200);
            $table->string('id_provinsi', 20);
            $table->string('id_kab_kota', 20);
            $table->string('id_kecamatan', 20);
            $table->string('id_kelurahan', 20);
            $table->string('alamat_tinggal', 200);
            $table->string('id_provinsi_tinggal', 20);
            $table->string('id_kab_kota_tinggal', 20);
            $table->string('id_kecamatan_tinggal', 20);
            $table->string('id_kelurahan_tinggal', 20);
            $table->string('kontak_pasien', 20);
            $table->date('tanggal_masuk');
            $table->integer('id_cara_masuk_pasien');
            $table->integer('id_asal_rujukan_pasien')->nullable();
            $table->string('asal_rujukan_pasien_fasyankes_lainnya', 100)->nullable();
            $table->string('id_diagnosa_masuk', 20);
            $table->string('id_sub_instalasi_unit', 20);
            $table->string('id_diagnosa_utama', 20)->nullable();
            $table->string('id_diagnosa_sekunder1', 20)->nullable();
            $table->string('id_diagnosa_sekunder2', 20)->nullable();
            $table->string('id_diagnosa_sekunder3', 20)->nullable();
            $table->date('tanggal_diagnosa')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->integer('id_cara_keluar');
            $table->integer('id_keadaan_keluar');
            $table->string('id_sebab_kematian_langsung_1a', 20)->nullable();
            $table->string('id_sebab_kematian_antara_1b', 20)->nullable();
            $table->string('id_sebab_kematian_antara_1c', 20)->nullable();
            $table->string('id_sebab_kematian_dasar_1d', 20)->nullable();
            $table->string('id_kondisi_yg_berkontribusi_thdp_kematian', 20)->nullable();
            $table->string('sebab_dasar_kematian', 200)->nullable();
            $table->integer('id_cara_bayar');
            $table->string('nomor_bpjs', 20)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pelaporan_kankers');
    }
}
