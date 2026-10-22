<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmpkSatuSehatCompositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smpk_satu_sehat_compositions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('smpk_case_id');

            // Identifier Composition
            $table->string('identifier_system', 255);
            $table->string('identifier_value', 100);

            // Identitas resource SATUSEHAT
            $table->string('composition_id', 100)->nullable()->index();

            // Status dokumen FHIR
            $table->string('status', 30)->default('pending')->index();

            // Versi dan tanggal dokumen
            $table->string('playbook_version', 20)->nullable();
            $table->dateTime('composition_date')->nullable();

            // Referensi klinis
            $table->string('patient_id', 100)->nullable();
            $table->string('encounter_id', 100)->nullable();
            $table->string('practitioner_id', 100)->nullable();

            // Payload dan respons API
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();

            $table->string('http_method', 10)->nullable();
            $table->string('endpoint', 500)->nullable();
            $table->unsignedSmallInteger('http_status')->nullable();

            $table->unsignedInteger('attempts')->default(0);
            $table->dateTime('last_attempt_at')->nullable();
            $table->dateTime('sent_at')->nullable();

            $table->text('last_error')->nullable();

            $table->timestamps();

            $table->unique(
                ['identifier_system', 'identifier_value'],
                'smpk_composition_identifier_unique'
            );

            $table->foreign('smpk_case_id')
                ->references('id')
                ->on('smpk_satu_sehat_cases')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('smpk_satu_sehat_compositions');
    }
}
