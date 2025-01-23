<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTambahanRadiologisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tambahan_radiologis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_rawat', 20);
            $table->string('no_rawat_tambahan', 20);
            $table->string('no_order', 15);
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
        Schema::dropIfExists('tambahan_radiologis');
    }
}
