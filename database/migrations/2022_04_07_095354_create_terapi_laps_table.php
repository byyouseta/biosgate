<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTerapiLapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terapi_laps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('lapId');
            $table->integer('lapTerapiId');
            $table->string('TerapiId', 5);
            $table->string('desc', 100);
            $table->integer('jumlah');
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
        Schema::dropIfExists('terapi_laps');
    }
}
