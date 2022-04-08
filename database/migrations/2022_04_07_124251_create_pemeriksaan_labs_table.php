<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemeriksaanLabsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pemeriksaan_labs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('lapId');
            $table->integer('lapPemeriksaanId');
            $table->integer('jenisPemeriksaanId');
            $table->string('namaPemeriksaan', 50);
            $table->boolean('hasilPemeriksaanId');
            $table->date('tgl_hasil');
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
        Schema::dropIfExists('pemeriksaan_labs');
    }
}
