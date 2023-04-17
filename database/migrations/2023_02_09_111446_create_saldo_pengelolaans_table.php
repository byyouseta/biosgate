<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaldoPengelolaansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saldo_pengelolaans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tgl_transaksi');
            $table->integer('bank_id');
            $table->bigInteger('nilai_deposito');
            $table->bigInteger('nilai_bunga');
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
        Schema::dropIfExists('saldo_pengelolaans');
    }
}
