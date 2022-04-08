<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKomorbidLapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('komorbid_laps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('lapId');
            $table->integer('lapKomorbidId');
            $table->string('komorbidId', 5);
            $table->string('desc', 100);
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
        Schema::dropIfExists('komorbid_laps');
    }
}
