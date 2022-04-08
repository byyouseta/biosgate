<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVaksinasiLapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vaksinasi_laps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('lapId');
            $table->integer('lapVaksinId');
            $table->integer('dosisVaksinId');
            $table->string('namaDosis', 50);
            $table->integer('jenisVaksinId');
            $table->string('namaVaksin', 50);
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
        Schema::dropIfExists('vaksinasi_laps');
    }
}
