<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponseMedicationSatuSehatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('response_medication_satu_sehats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('noRawat', 20);
            $table->date('tgl_registrasi');
            $table->string('noResep', 20);
            $table->string('medication1', 50)->nullable();
            $table->string('medicationRequest', 50)->nullable();
            $table->string('medication2', 50)->nullable();
            $table->string('medicationDispence', 50)->nullable();
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
        Schema::dropIfExists('response_medication_satu_sehats');
    }
}
