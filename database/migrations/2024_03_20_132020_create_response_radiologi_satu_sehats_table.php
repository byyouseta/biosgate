<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponseRadiologiSatuSehatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('response_radiologi_satu_sehats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('noRawat', 20);
            $table->date('tgl_registrasi');
            $table->string('no_order', 20);
            $table->string('accession_no', 20);
            $table->string('encounter_id', 50);
            $table->string('service_request_id', 50);
            $table->string('imaging_study_id', 50)->nullable();
            $table->string('observation_id', 50)->nullable();
            $table->string('diagnostic_report_id', 50)->nullable();
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
        Schema::dropIfExists('response_radiologi_satu_sehats');
    }
}
