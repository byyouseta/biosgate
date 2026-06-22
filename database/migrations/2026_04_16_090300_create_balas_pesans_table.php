<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBalasPesansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balas_pesans', function (Blueprint $table) {
            $table->bigIncrements('id');
            // nomor hp user
            $table->string('no_hp')->index();

            // terakhir kali kita balas
            $table->timestamp('last_replied_at')->nullable();

            // jumlah total reply ke user ini
            $table->unsignedInteger('reply_count')->default(0);

            // flag apakah sudah pernah auto reply
            $table->boolean('auto_replied')->default(false);

            // optional: simpan isi pesan terakhir user
            $table->text('last_message')->nullable();
            $table->index(['no_hp', 'last_replied_at']);
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
        Schema::dropIfExists('balas_pesans');
    }
}
