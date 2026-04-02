<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollumnKodeTindakanToResponseLabSatuSehats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('response_lab_satu_sehats', function (Blueprint $table) {
            $table->string('kd_jenis_prw', 10)->nullable()->after('noOrder');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('response_lab_satu_sehats', function (Blueprint $table) {
            $table->dropColumn('kd_jenis_prw');
        });
    }
}
