<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataPengajuanUlangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_pengajuan_ulangs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_rawat', 20);
            $table->string('no_sep', 25);
            $table->string('no_kartu', 15);
            $table->string('nama_pasien');
            $table->string('jk', 1);
            $table->smallInteger('umur_daftar')->nullable();
            $table->string('status_umur', 10)->nullable();
            $table->date('tgl_registrasi');
            $table->date('tgl_lahir');
            $table->string('kode_poli', 5)->nullable();
            $table->string('nama_poli', 50);
            $table->string('jenis_rawat', 15);
            $table->integer('periode_pengajuan_ulang_id');
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
        Schema::dropIfExists('data_pengajuan_ulangs');
    }
}
