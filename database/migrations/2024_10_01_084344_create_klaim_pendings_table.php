<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKlaimPendingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('klaim_pendings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_sep', 20);
            $table->date('tgl_sep');
            $table->date('tgl_pulang');
            $table->string('kelas_rawat', 10);
            $table->string('poli');
            $table->string('status', 50);
            $table->integer('biaya_pengajuan');
            $table->integer('biaya_tarif_grouper');
            $table->integer('biaya_tarif_rs');
            $table->integer('biaya_disetujui');
            $table->string('jenis_rawat', 2);
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
        Schema::dropIfExists('klaim_pendings');
    }
}
