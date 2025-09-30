<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarePlanRanapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('care_plan_ranaps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('encounter_id', 50);
            $table->string('endpoint')->nullable(); // alamat API
            $table->string('method', 10)->default('POST'); // GET/POST/PUT/DELETE
            $table->json('request_payload')->nullable(); // data dikirim
            $table->json('response_payload')->nullable(); // data diterima
            $table->integer('status_code')->nullable(); // http status
            $table->string('careplan_id', 50)->nullable();
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
        Schema::dropIfExists('care_plan_ranaps');
    }
}
