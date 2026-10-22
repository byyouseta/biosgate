<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountSatuSehatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_satu_sehats', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Identitas kunjungan dari SIMRS Khanza
            $table->string('no_rawat', 20)->index();
            $table->enum('jenis_rawat', [
                'ralan',
                'igd',
                'ranap',
            ])->index();

            // Data pasien dan kunjungan SATUSEHAT
            $table->string('patient_id', 64)->nullable()->index();
            $table->string('encounter_id', 64)->nullable()->index();

            // Informasi penjamin dari SIMRS
            $table->string('kd_pj', 20)->nullable()->index();
            $table->string('nm_penjab', 100)->nullable();

            // Referensi resource SATUSEHAT
            $table->string('coverage_id', 64)->nullable()->index();
            $table->string('account_id', 64)->nullable()->unique();

            // Status resource Account pada SATUSEHAT
            $table->string('account_status', 30)->nullable();

            // Status proses sinkronisasi lokal
            $table->enum('status', [
                'pending',
                'sent',
                'failed',
            ])->default('pending')->index();

            // Informasi proses dan respons API
            $table->text('pesan_error')->nullable();
            $table->longText('response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('linked_at')->nullable();

            $table->timestamps();

            // Mempermudah pencarian Account berdasarkan kunjungan
            $table->index(
                ['no_rawat', 'jenis_rawat'],
                'account_rawat_jenis_idx'
            );

            $table->index(
                ['status', 'created_at'],
                'account_status_created_idx'
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
        Schema::dropIfExists('account_satu_sehats');
    }
}
