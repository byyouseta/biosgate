<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersetujuanRawatInapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persetujuan_rawat_inaps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_pj');
            $table->string('tempat_lahir_pj', 100);
            $table->date('tanggal_lahir_pj');
            $table->enum('jenis_kelamin_pj', ['L', 'P']);
            $table->string('alamat_pj', 255);
            $table->string('pekerjaan_pj', 100);
            $table->string('no_ktp_pj', 20);
            $table->string('no_telepon_pj', 20);
            $table->enum('hubungan_pj', ['Suami', 'Istri', 'Anak', 'Orang Tua', 'Wali', 'Diri Sendiri', 'Lainnya']);
            $table->string('nama_pasien');
            $table->string('tempat_lahir_pasien', 100);
            $table->date('tanggal_lahir_pasien');
            $table->enum('jenis_kelamin_pasien', ['L', 'P']);
            $table->string('alamat_pasien', 255);
            $table->string('no_rm', 20);
            $table->string('no_rawat', 20);
            $table->string('cara_bayar', 50);
            $table->string('cara_bayar_lainnya', 100)->nullable();
            $table->string('kelas_rawat', 50)->nullable();
            $table->string('pindah_kelas_rawat', 50)->nullable();
            $table->enum('status_persetujuan', ['Setuju', 'Menolak'])->default('Setuju');
            $table->unsignedBigInteger('petugas_id');
            $table->text('tanda_tangan')->nullable();
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
        Schema::dropIfExists('persetujuan_rawat_inaps');
    }
}
