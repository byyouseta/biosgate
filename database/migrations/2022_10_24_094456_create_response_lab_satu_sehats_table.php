<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponseLabSatuSehatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('response_lab_satu_sehats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('noRawat', 20);
            $table->date('tgl_registrasi');
            $table->string('noOrder', 20);
            $table->string('serviceRequest_id', 50)->nullable();
            $table->string('specimen_id', 50)->nullable();
            $table->string('report_id', 50)->nullable();
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
        Schema::dropIfExists('response_lab_satu_sehats');
    }
}
