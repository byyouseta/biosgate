<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTindakanRanapSatuSehatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tindakan_ranap_satu_sehats', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('response_ranap_satu_sehat_id');

            $table->foreign('response_ranap_satu_sehat_id')
                ->references('id')
                ->on('response_ranap_satu_sehats') // ⚠ pastikan nama tabel benar
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
        Schema::dropIfExists('tindakan_ranap_satu_sehats');
    }
}
