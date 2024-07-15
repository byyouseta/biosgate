<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHakKewajibanPasiensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hak_kewajiban_pasiens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('noRawat', 20);
            $table->boolean('hak1')->nullable();
            $table->boolean('hak2')->nullable();
            $table->boolean('hak3')->nullable();
            $table->boolean('hak4')->nullable();
            $table->boolean('hak5')->nullable();
            $table->boolean('hak6')->nullable();
            $table->boolean('hak7')->nullable();
            $table->boolean('hak8')->nullable();
            $table->boolean('hak9')->nullable();
            $table->boolean('hak10')->nullable();
            $table->boolean('hak11')->nullable();
            $table->boolean('hak12')->nullable();
            $table->boolean('hak13')->nullable();
            $table->boolean('hak14')->nullable();
            $table->boolean('hak15')->nullable();
            $table->boolean('hak16')->nullable();
            $table->boolean('hak17')->nullable();
            $table->boolean('hak18')->nullable();
            $table->boolean('kewajiban1')->nullable();
            $table->boolean('kewajiban2')->nullable();
            $table->boolean('kewajiban3')->nullable();
            $table->boolean('kewajiban4')->nullable();
            $table->boolean('kewajiban5')->nullable();
            $table->boolean('kewajiban6')->nullable();
            $table->boolean('kewajiban7')->nullable();
            $table->boolean('kewajiban8')->nullable();
            $table->string('statusPj', 50);
            $table->string('namaPj', 100);
            $table->text('tandaTangan')->nullable();
            $table->integer('user_id');
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
        Schema::dropIfExists('hak_kewajiban_pasiens');
    }
}
