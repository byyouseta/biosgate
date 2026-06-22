<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponseImmunizationSatuSehatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('response_immunization_satu_sehats', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Registrasi pasien
            $table->string('noRawat', 25)->index();
            $table->date('tgl_registrasi')->nullable();

            // Data tindakan / vaksin
            $table->string('kode_barang', 30)->nullable()->index();
            $table->string('kd_jenis_prw', 30)->nullable()->index();

            // Relasi SATUSEHAT
            $table->string('encounter_id')->nullable()->index();
            $table->string('patient_id')->nullable()->index();
            $table->string('practitioner_id')->nullable()->index();

            // Response Immunization
            $table->string('immunization_id')->nullable()->unique();

            // Data vaksin
            $table->string('kode_kfa', 30)->nullable()->index();
            $table->string('display_kfa')->nullable();

            // Tracking
            $table->timestamp('tanggal_kirim')->nullable();
            $table->text('response_raw')->nullable();
            $table->timestamps();

            // Hindari double pengiriman
            $table->unique(
                ['noRawat', 'kode_barang', 'kd_jenis_prw'],
                'immunization_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('response_immunization_satu_sehats');
    }
}
