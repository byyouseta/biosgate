<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubinstalasiKankersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subinstalasi_kankers', function (Blueprint $table) {
            $table->string('kode_instalasi_unit', 3);
            $table->string('kode_gabung_sub_instalasi_unit', 5);
            $table->string('sub_instalasi_unit', 100);
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
        Schema::dropIfExists('subinstalasi_kankers');
    }
}
