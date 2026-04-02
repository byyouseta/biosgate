<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTindakanIgdSatuSehat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tindakan_igd_satu_sehats', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('response_igd_satu_sehat_id');

            $table->foreign('response_igd_satu_sehat_id')
                ->references('id')
                ->on('response_igd_satu_sehats') // ⚠ pastikan nama tabel benar
                ->onDelete('cascade');

            $table->string('procedure_id')->nullable();
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
        Schema::dropIfExists('tindakan_igd_satu_sehats');
    }
}
