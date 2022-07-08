<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBerkasVedikasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('berkas_vedikas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_rawat', 20);
            $table->integer('master_berkas_vedika_id');
            $table->string('lokasi_berkas', 100)->nullable();
            $table->string('file', 100);
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
        Schema::dropIfExists('berkas_vedikas');
    }
}
