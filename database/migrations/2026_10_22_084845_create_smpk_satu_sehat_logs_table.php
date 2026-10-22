<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmpkSatuSehatLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smpk_satu_sehat_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('smpk_case_id');
            $table->unsignedBigInteger('smpk_observation_id')->nullable();
            $table->unsignedBigInteger('smpk_composition_id')->nullable();

            $table->unsignedInteger('attempt_no')->default(1);

            $table->string('http_method', 10)->nullable();
            $table->string('endpoint', 500)->nullable();
            $table->unsignedSmallInteger('http_status')->nullable();

            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();

            // success, failed, timeout, validation_error, dll.
            $table->string('result', 30)->index();

            $table->text('error_message')->nullable();

            $table->dateTime('attempted_at')->index();
            $table->timestamps();

            $table->index(
                ['smpk_case_id', 'attempted_at'],
                'smpk_log_case_attempt_idx'
            );

            $table->foreign('smpk_case_id')
                ->references('id')
                ->on('smpk_satu_sehat_cases')
                ->onDelete('restrict');

            $table->foreign('smpk_observation_id')
                ->references('id')
                ->on('smpk_satu_sehat_observations')
                ->onDelete('set null');

            $table->foreign('smpk_composition_id')
                ->references('id')
                ->on('smpk_satu_sehat_compositions')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('smpk_satu_sehat_logs');
    }
}
