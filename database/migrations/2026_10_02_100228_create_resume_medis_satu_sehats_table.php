<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResumeMedisSatuSehatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resume_medis_satu_sehats', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('no_rawat', 50);
            $table->string('jenis_rawat', 20)->nullable();

            $table->string('encounter_id', 100);
            $table->string('composition_id', 100)->nullable();

            $table->string('status', 30)->default('draft');

            $table->timestamp('tanggal_kirim')->nullable();

            $table->timestamps();

            $table->unique('encounter_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resume_medis_satu_sehats');
    }
}
