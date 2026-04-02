<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollumnToFraud extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fraud_rajals', function (Blueprint $table) {
            $table->boolean('selesai')->default(false)->after('keterangan');
        });
        Schema::table('fraud_ranaps', function (Blueprint $table) {
            $table->boolean('selesai')->default(false)->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fraud_rajals', function (Blueprint $table) {
            $table->dropColumn('selesai');
        });
        Schema::table('fraud_ranaps', function (Blueprint $table) {
            $table->dropColumn('selesai');
        });
    }
}
