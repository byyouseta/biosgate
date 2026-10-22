<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoverageSatuSehatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coverage_satu_sehats', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Identitas pasien
            $table->string('no_rkm_medis', 20)->index();
            $table->string('patient_id', 64)->index();

            // Identitas penjamin dari SIMKES/Khanza
            $table->string('kd_pj', 20)->index();
            $table->string('nm_penjab', 100)->nullable();
            $table->string('jenis_penjamin', 30)->nullable();

            // Nomor peserta sesuai penjamin
            $table->string('nomor_peserta', 50)->nullable();

            // Resource Coverage SATUSEHAT
            $table->string('coverage_id', 64)->nullable()->unique();
            $table->string('coverage_status', 30)->nullable();

            // Status pencarian/pemetaan
            $table->enum('status', [
                'pending',
                'found',
                'not_found',
                'failed',
            ])->default('pending')->index();

            $table->text('pesan_error')->nullable();
            $table->longText('response')->nullable();
            $table->timestamp('last_checked_at')->nullable();

            $table->timestamps();

            // Satu pemetaan per pasien, penjamin, dan nomor peserta
            $table->unique(
                ['no_rkm_medis', 'kd_pj', 'nomor_peserta'],
                'coverage_pasien_penjamin_peserta_unique'
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
        Schema::dropIfExists('coverage_satu_sehats');
    }
}
