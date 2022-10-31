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
            $table->string('noRawat', 20);
            $table->string('encounter_id', 50);
            $table->string('condition_id', 50)->nullable();
            $table->string('condition2_id', 50)->nullable();
            $table->string('heart_id', 50)->nullable();
            $table->string('respiratory_id', 50)->nullable();
            $table->string('systol_id', 50)->nullable();
            $table->string('diastol_id', 50)->nullable();
            $table->string('temperature_id', 50)->nullable();
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
