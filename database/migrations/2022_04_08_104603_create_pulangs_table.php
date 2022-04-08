<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePulangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pulangs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('lapId');
            $table->integer('lapPulangId');
            $table->date('tgl_pulang');
            $table->integer('statusPulangId');
            $table->string('statusPulang', 50);
            $table->integer('penyebabKematianId');
            $table->string('penyebabKematian', 50)->nullable();
            $table->integer('penyebabKematianLangsungId');
            $table->string('penyebabKematianLangsung', 50)->nullable();
            $table->integer('statusPasienMeninggalId');
            $table->string('statusPasienMeninggal', 50)->nullable();
            $table->integer('komorbidCoinsidenId');
            $table->string('komorbidCoinsiden', 50)->nullable();
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
        Schema::dropIfExists('pulangs');
    }
}
