<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRsoTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rso_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kodeBooking', 30);
            $table->string('taskid1', 20)->nullable();
            $table->string('taskid2', 20)->nullable();
            $table->string('taskid3', 20)->nullable();
            $table->string('taskid4', 20)->nullable();
            $table->string('taskid5', 20)->nullable();
            $table->string('taskid6', 20)->nullable();
            $table->string('taskid7', 20)->nullable();
            $table->string('taskid99', 20)->nullable();
            $table->boolean('statusKirim');
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
        Schema::dropIfExists('rso_tasks');
    }
}
