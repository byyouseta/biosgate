<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFraudRanapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fraud_ranaps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('data_pengajuan_klaim_id');
            $table->integer('periode_klaim_id');
            $table->boolean('up_coding')->nullable();
            $table->boolean('phantom_billing')->nullable();
            $table->boolean('cloning')->nullable();
            $table->boolean('inflated_bills')->nullable();
            $table->boolean('pemecahan')->nullable();
            $table->boolean('rujukan_semu')->nullable();
            $table->boolean('repeat_billing')->nullable();
            $table->boolean('prolonged_los')->nullable();
            $table->boolean('manipulasi_kels')->nullable();
            $table->boolean('re_admisi')->nullable();
            $table->boolean('kesesuaian_tindakan')->nullable();
            $table->boolean('tagihan_tindakan')->nullable();
            $table->text('klarifikasi')->nullable();
            $table->text('keterangan')->nullable();
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
        Schema::dropIfExists('fraud_ranaps');
    }
}
