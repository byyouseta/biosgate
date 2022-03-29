<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaldoKeuangansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saldo_keuangans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('bank_id');
            $table->bigInteger('saldo');
            $table->string('kd_rek', 3);
            $table->date('tgl_transaksi');
            $table->boolean('status');
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
        Schema::dropIfExists('saldo_keuangans');
    }
}
