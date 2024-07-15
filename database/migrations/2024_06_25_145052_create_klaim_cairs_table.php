<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKlaimCairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('klaim_cairs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_sep', 25);
            $table->date('tgl_verif');
            $table->integer('riil');
            $table->integer('diajukan');
            $table->integer('disetujui');
            $table->string('jenis_rawat', 2);
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
        Schema::dropIfExists('klaim_cairs');
    }
}
