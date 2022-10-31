<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollumnToResponseSatuSehats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('response_satu_sehats', function (Blueprint $table) {
            $table->string('procedure_id', 50)->after('temperature_id')->nullable();
            $table->string('composition_id', 50)->after('procedure_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('response_satu_sehats', function (Blueprint $table) {
            $table->dropColumn('procedure_id', 'composition_id');
        });
    }
}
