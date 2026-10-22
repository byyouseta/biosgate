<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargeItemSatuSehatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charge_item_satu_sehats', function (Blueprint $table) {
            $table->bigIncrements('id');
            /*
             * Identitas transaksi Khanza
             */
            $table->string('no_rawat', 17)->index();
            $table->string('no_transaksi', 50)->nullable();

            $table->enum('jenis_rawat', [
                'ralan',
                'igd',
                'ranap'
            ])->nullable();

            /*
             * Kategori item tagihan:
             * tindakan, lab, rad, obat, kamar, administrasi, dll.
             */
            $table->string('source_table', 100)->nullable(); //Tabel sumber data dari Khanza
            $table->string('jenis_item', 30); //Membedakan tindakan, lab, radiologi, obat, dan lainnya
            $table->string('kode_item', 50); //Kode tindakan, pemeriksaan, atau obat dari Khanza
            $table->string('nama_item', 255)->nullable();

            /*
             * Kuantitas dan harga
             */
            $table->decimal('jumlah', 12, 3)->default(1);
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);

            /*
             * Referensi SATUSEHAT
             */
            $table->string('patient_id', 64)->nullable();
            $table->string('encounter_id', 64)->nullable();
            $table->string('kptl_code', 50)->nullable();
            $table->string('kptl_system', 255)->nullable();
            $table->string('kptl_display', 255)->nullable();

            $table->string('performer_id', 64)->nullable();
            $table->dateTime('occurrence_start')->nullable();
            $table->dateTime('occurrence_end')->nullable();

            /*
             * Resource pelayanan yang berhubungan
             * Contoh: Procedure, DiagnosticReport,
             * ImagingStudy, MedicationDispense.
             */
            $table->string('service_type', 50)->nullable(); //Referensi resource pelayanan yang terkait
            $table->string('service_id', 64)->nullable();

            /*
             * ID ChargeItem dari SATUSEHAT
             */
            $table->string('charge_item_id', 64)->nullable()->unique();

            /*
             * Status pengiriman lokal
             */
            $table->enum('status', [
                'pending',
                'sent',
                'failed'
            ])->default('pending')->index();

            $table->text('pesan_error')->nullable();
            $table->longText('response')->nullable();

            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            /*
             * Indeks untuk pencarian transaksi dan pencegahan
             * duplikasi berdasarkan identitas item Khanza.
             */
            $table->index(
                ['no_rawat', 'jenis_item'],
                'charge_item_rawat_jenis_idx'
            );

            $table->index(
                ['status', 'created_at'],
                'charge_item_status_created_idx'
            );

            $table->index(
                ['no_transaksi', 'jenis_item'],
                'charge_item_transaksi_jenis_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charge_item_satu_sehats');
    }
}
