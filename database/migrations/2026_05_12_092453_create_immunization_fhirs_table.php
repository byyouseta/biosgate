<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImmunizationFhirsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('immunization_fhirs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kd_jenis_prw')->unique();
            $table->string('kode_barang', 15);
            $table->string('kode_kfa', 10);
            $table->string('display_kfa');
            $table->integer('alasan_imunisasi_id');
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
        Schema::dropIfExists('immunization_fhirs');
    }
}
