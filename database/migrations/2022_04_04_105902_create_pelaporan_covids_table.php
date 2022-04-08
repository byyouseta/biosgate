<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePelaporanCovidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pelaporan_covids', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('lapId');
            $table->string('noRawat', 20);
            $table->string('kewarganegaraan', 4);
            $table->string('nik', 20);
            $table->string('noPassport', 20)->nullable();
            $table->smallInteger('asalPasien');
            $table->string('noRm', 8);
            $table->string('namaPasien', 100);
            $table->string('inisial', 10);
            $table->date('tgl_lahir');
            $table->string('email', 100)->nullable();
            $table->string('nohp', 20);
            $table->string('jk', 1);
            $table->string('provinsi', 3);
            $table->string('kabKota', 5);
            $table->string('kecamatan', 7);
            $table->date('tgl_masuk');
            $table->smallInteger('pekerjaan');
            $table->smallInteger('jenis_pasien');
            $table->smallInteger('varian_covid');
            $table->smallInteger('status_pasien');
            $table->boolean('status_coinsiden');
            $table->smallInteger('status_rawat');
            $table->smallInteger('alat_oksigen')->nullable();
            $table->boolean('penyintas');
            $table->date('tgl_gejala');
            $table->smallInteger('kelompok_gejala');
            $table->boolean('demam');
            $table->boolean('batuk');
            $table->boolean('pilek');
            $table->boolean('sakit_tenggorokan');
            $table->boolean('sesak_napas');
            $table->boolean('lemas');
            $table->boolean('nyeri_otot');
            $table->boolean('mual_muntah');
            $table->boolean('diare');
            $table->boolean('anosmia');
            $table->boolean('napas_cepat');
            $table->boolean('frek_napas');
            $table->boolean('distres_pernapasan');
            $table->boolean('lainnya');
            $table->boolean('status_pulang');
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
        Schema::dropIfExists('pelaporan_covids');
    }
}
