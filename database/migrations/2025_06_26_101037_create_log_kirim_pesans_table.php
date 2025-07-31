<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogKirimPesansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_kirim_pesans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_rm', 10);
            $table->date('tgl_periksa')->nullable();
            $table->integer('template_id');
            $table->enum('status', ['pending', 'berhasil', 'gagal'])->default('pending');
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
        Schema::dropIfExists('log_kirim_pesans');
    }
}
