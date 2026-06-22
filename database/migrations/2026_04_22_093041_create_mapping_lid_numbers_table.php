<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMappingLidNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapping_lid_numbers', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Nomor asli (untuk manusia / referensi)
            $table->string('phone', 20)->index();

            // WhatsApp ID (bisa @c.us atau @lid)
            $table->string('wid')->nullable()->index();

            // Waktu terakhir cek ke WA
            $table->timestamp('last_checked_at')->nullable();

            // Optional: hindari duplikat nomor
            $table->unique('phone');
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
        Schema::dropIfExists('mapping_lid_numbers');
    }
}
