<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollumnToResponseIgdSatuSehats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('response_igd_satu_sehats', function (Blueprint $table) {
            $table->string('triase_transportasi', 50)->after('encounter_id')->nullable();
            $table->string('triase_rujukan', 50)->after('triase_transportasi')->nullable();
            $table->string('triase_kondisi', 50)->after('triase_rujukan')->nullable();
            $table->string('asesmen_nyeri', 50)->after('triase_kondisi')->nullable();
            $table->string('asesmen_skala_nyeri', 50)->after('asesmen_nyeri')->nullable();
            $table->string('asesmen_lokasi_nyeri', 50)->after('asesmen_skala_nyeri')->nullable();
            $table->string('asesmen_penyebab_nyeri', 50)->after('asesmen_lokasi_nyeri')->nullable();
            $table->string('asesmen_tingkat_kesadaran', 50)->after('asesmen_penyebab_nyeri')->nullable();
            $table->string('asesmen_nadi', 50)->after('asesmen_tingkat_kesadaran')->nullable();
            $table->string('asesmen_pernapasan', 50)->after('asesmen_nadi')->nullable();
            $table->string('asesmen_sistol', 50)->after('asesmen_pernapasan')->nullable();
            $table->string('asesmen_diastol', 50)->after('asesmen_sistol')->nullable();
            $table->string('asesmen_suhu', 50)->after('asesmen_diastol')->nullable();
            $table->string('asesmen_fisik_kepala', 50)->after('asesmen_suhu')->nullable();
            $table->string('asesmen_fisik_mata', 50)->after('asesmen_fisik_kepala')->nullable();
            $table->string('asesmen_fisik_gigimulut', 50)->after('asesmen_fisik_mata')->nullable();
            $table->string('asesmen_fisik_leher', 50)->after('asesmen_fisik_gigimulut')->nullable();
            $table->string('asesmen_fisik_thoraks', 50)->after('asesmen_fisik_leher')->nullable();
            $table->string('asesmen_fisik_abdomen', 50)->after('asesmen_fisik_thoraks')->nullable();
            $table->string('asesmen_fisik_genitasanus', 50)->after('asesmen_fisik_abdomen')->nullable();
            $table->string('diagnosis_awal', 50)->after('asesmen_fisik_genitasanus')->nullable();
            $table->string('diagnosis_kerja', 50)->after('diagnosis_awal')->nullable();
            $table->string('kondisi_stabil', 50)->after('diagnosis_kerja')->nullable();
            $table->string('cara_keluar')->after('kondisi_stabil')->nullable();
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
                'triase_transportasi',
                'triase_rujukan',
                'triase_kondisi',
                'asesmen_nyeri',
                'asesmen_skala_nyeri',
                'asesmen_lokasi_nyeri',
                'asesmen_tingkat_kesadaran',
                'asesmen_nadi',
                'asesmen_pernapasan',
                'asesmen_sistol',
                'asesmen_diastol',
                'asesmen_suhu',
                'asesmen_fisik_kepala',
                'asesmen_fisik_mata',
                'asesmen_fisik_gigimulut',
                'asesmen_fisik_leher',
                'asesmen_fisik_thoraks',
                'asesmen_fisik_abdomen',
                'asesmen_fisik_genitasanus',
                'diagnosis_awal',
                'diagnosis_kerja',
                'kondisi_stabil',
                'cara_keluar'
            );
        });
    }
}
