<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponseRanapSatuSehatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('response_ranap_satu_sehats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('noRawat', 20);
            $table->date('tgl_registrasi');
            $table->string('encounter_id', 50);
            $table->string('asesmen_nadi', 50)->nullable();
            $table->string('asesmen_pernapasan', 50)->nullable();
            $table->string('asesmen_sistol', 50)->nullable();
            $table->string('asesmen_diastol', 50)->nullable();
            $table->string('asesmen_suhu', 50)->nullable();
            $table->string('status_psikologis', 50)->nullable();
            $table->string('diagnosis_primer', 50)->nullable();
            $table->string('diagnosis_sekunder', 50)->nullable();
            $table->string('kondisi_stabil', 50)->nullable();
            $table->string('cara_keluar')->nullable();
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
        Schema::dropIfExists('response_ranap_satu_sehats');
    }
}
