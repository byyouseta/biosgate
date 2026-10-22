<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmpkSatuSehatObservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smpk_satu_sehat_observations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('smpk_case_id');

            // Identitas variabel internal
            $table->string('variable_key', 100);
            $table->string('variable_name', 255);

            // Identitas terminologi FHIR
            $table->string('loinc_code', 50)->nullable();
            $table->string('loinc_display', 255)->nullable();

            // Jenis nilai yang akan dimasukkan ke Observation
            $table->string('value_type', 50)->nullable();
            $table->json('value_data')->nullable();

            // ID resource dari SATUSEHAT
            $table->string('observation_id', 100)->nullable()->index();

            // Payload request dan response
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();

            // Status pengiriman
            $table->string('status', 30)
                ->default('pending')
                ->index();

            $table->unsignedInteger('attempts')->default(0);
            $table->unsignedSmallInteger('http_status')->nullable();

            $table->dateTime('last_attempt_at')->nullable();
            $table->dateTime('sent_at')->nullable();

            $table->text('last_error')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Satu variabel hanya memiliki satu record lokal per kasus
            $table->unique(
                ['smpk_case_id', 'variable_key'],
                'smpk_observation_variable_unique'
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
        Schema::dropIfExists('smpk_satu_sehat_observations');
    }
}
