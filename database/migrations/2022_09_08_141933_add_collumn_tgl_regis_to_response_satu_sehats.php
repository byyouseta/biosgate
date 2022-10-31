<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollumnTglRegisToResponseSatuSehats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('response_satu_sehats', function (Blueprint $table) {
            $table->date('tgl_registrasi')->after('noRawat');
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
            $table->dropColumn('tgl_registrasi');
        });
    }
}
