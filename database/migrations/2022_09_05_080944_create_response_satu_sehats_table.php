<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponseSatuSehatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('response_satu_sehats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('noRawat', 10);
            $table->string('encounter_id', 50);
            $table->string('condition_id', 50);
            $table->string('condition2_id', 50);
            $table->string('heart_id', 50);
            $table->string('respiratory_id', 50);
            $table->string('systol_id', 50);
            $table->string('diastol_id', 50);
            $table->string('temperature_id', 50);
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
        Schema::dropIfExists('response_satu_sehats');
    }
}
