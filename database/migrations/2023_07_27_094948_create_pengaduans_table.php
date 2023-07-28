<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengaduansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengaduans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama', 100);
            $table->string('no_hp', 20);
            $table->string('email', 128)->nullable();
            $table->tinyInteger('penerima');
            $table->boolean('punya_rm');
            $table->string('no_rm', 8)->nullable();
            $table->string('nama_pasien', 100)->nullable();
            $table->date('lahir_pasien')->nullable();
            $table->dateTime('waktu_kejadian');
            $table->string('tempat_kejadian', 100);
            $table->tinyInteger('pembiayaan');
            $table->boolean('pendaftaran_online')->default(false);
            $table->boolean('pendaftaran_rajal')->default(false);
            $table->boolean('pendaftaran_ranap')->default(false);
            $table->boolean('pendaftaran_igd')->default(false);
            $table->boolean('admin_bpjs')->default(false);
            $table->boolean('petugas_dr_sp')->default(false);
            $table->boolean('petugas_dr_umum')->default(false);
            $table->boolean('petugas_dr_gigi')->default(false);
            $table->boolean('petugas_perawat')->default(false);
            $table->boolean('petugas_bidan')->default(false);
            $table->boolean('petugas_psikolog')->default(false);
            $table->boolean('petugas_apoteker')->default(false);
            $table->boolean('petugas_radiografer')->default(false);
            $table->boolean('petugas_fisioterapi')->default(false);
            $table->boolean('petugas_konselor')->default(false);
            $table->boolean('petugas_ahli_gizi')->default(false);
            $table->boolean('petugas_administrasi')->default(false);
            $table->boolean('petugas_kebersihan')->default(false);
            $table->boolean('petugas_parkir')->default(false);
            $table->boolean('petugas_satpam')->default(false);
            $table->boolean('petugas_kasir')->default(false);
            $table->boolean('petugas_rohaniawan')->default(false);
            $table->boolean('petugas_lainnya')->default(false);
            $table->boolean('layanan_poli_reg')->default(false);
            $table->boolean('layanan_poli_eks')->default(false);
            $table->boolean('layanan_ranap')->default(false);
            $table->boolean('layanan_igd')->default(false);
            $table->boolean('layanan_icu')->default(false);
            $table->boolean('layanan_farmasi')->default(false);
            $table->boolean('layanan_jenazah')->default(false);
            $table->boolean('layanan_lab')->default(false);
            $table->boolean('layanan_mcu')->default(false);
            $table->boolean('layanan_hemodialisa')->default(false);
            $table->boolean('layanan_fisioterapi')->default(false);
            $table->boolean('layanan_radioterapi')->default(false);
            $table->boolean('layanan_radiologi')->default(false);
            $table->boolean('layanan_lainnya')->default(false);
            $table->boolean('fasilitas_parkir')->default(false);
            $table->boolean('fasilitas_taman')->default(false);
            $table->boolean('fasilitas_ambulan')->default(false);
            $table->boolean('fasilitas_toilet')->default(false);
            $table->boolean('fasilitas_tunggu')->default(false);
            $table->boolean('fasilitas_lainnya')->default(false);
            $table->text('deskripsi');
            $table->integer('nilai_gangguan');
            $table->integer('keluhan_id')->nullable();
            $table->tinyInteger('status_keluhan_id')->nullable();
            $table->string('no_tiket', 12);
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
        Schema::dropIfExists('pengaduans');
    }
}
