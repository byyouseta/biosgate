<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollumnToSoapTambahans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('soap_tambahans', function (Blueprint $table) {
            $table->string('kd_dokter', 20)->after('no_rawat_tambahan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('soap_tambahans', function (Blueprint $table) {
            $table->dropColumn('kd_dokter');
        });
    }
}
