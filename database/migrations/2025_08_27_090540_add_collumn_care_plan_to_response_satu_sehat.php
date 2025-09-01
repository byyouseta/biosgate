<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollumnCarePlanToResponseSatuSehat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('response_satu_sehats', function (Blueprint $table) {
            $table->string('goal_id', 50)->after('composition_id')->nullable();
            $table->string('careplan_id', 50)->after('goal_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('response_satu_sehat', function (Blueprint $table) {
            $table->dropColumn(
                'goal_id',
                'careplan_id'
            );
        });
    }
}
