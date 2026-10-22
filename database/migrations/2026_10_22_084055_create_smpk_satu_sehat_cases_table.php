<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmpkSatuSehatCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smpk_satu_sehat_cases', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Identitas sumber di SIMRS Khanza
            $table->string('no_rawat', 30)->unique();
            $table->string('no_rkm_medis', 30)->nullable()->index();

            // Identitas SATUSEHAT
            $table->string('patient_id', 100)->nullable()->index();
            $table->string('encounter_id', 100)->nullable()->index();

            // Data kejadian kematian
            $table->dateTime('deceased_at')->nullable();
            $table->string('death_location', 255)->nullable();

            // Status proses integrasi
            $table->string('status', 30)
                ->default('pending')
                ->index();

            $table->unsignedInteger('attempts')->default(0);
            $table->dateTime('last_attempt_at')->nullable();
            $table->dateTime('sent_at')->nullable();

            $table->text('last_error')->nullable();
            $table->json('source_snapshot')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Mencegah satu sumber dibuat menjadi dua kasus lokal.
            $table->unique(
                ['no_rawat', 'no_rkm_medis'],
                'smpk_cases_source_unique'
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
        Schema::dropIfExists('smpk_satu_sehat_cases');
    }
}
