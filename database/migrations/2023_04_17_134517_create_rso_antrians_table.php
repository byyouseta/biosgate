<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRsoAntriansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rso_antrians', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kodeBooking', 30);
            $table->string('jenisPasien', 10);
            $table->string('nik', 18);
            $table->string('kodePoli', 6);
            $table->string('namaPoli', 30);
            $table->boolean('pasienBaru');
            $table->string('noRm', 10);
            $table->date('tglPeriksa');
            $table->string('kodeDokter', 20);
            $table->string('namaDokter', 50);
            $table->time('jamPraktek')->nullable();
            $table->smallInteger('jenisKunjungan');
            $table->string('nomorReferensi', 20)->nullable();
            $table->string('nomorAntrean', 5);
            $table->string('angkaAntrean', 5)->nullable();
            $table->integer('estimasi')->nullable();
            $table->string('keterangan', 200)->nullable();
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
        Schema::dropIfExists('rso_antrians');
    }
}
