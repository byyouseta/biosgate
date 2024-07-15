<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollumnToResponseIgdSatuSehat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('response_igd_satu_sehats', function (Blueprint $table) {
            $table->string('service_request', 50)->after('cara_keluar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('response_igd_satu_sehats', function (Blueprint $table) {
            $table->dropColumn(
                'service_request'
            );
        });
    }
}
