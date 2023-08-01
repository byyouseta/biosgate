<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKepuasansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kepuasans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_hp', 20);
            $table->tinyInteger('umur');
            $table->tinyInteger('jk');
            $table->tinyInteger('pendidikan');
            $table->tinyInteger('pekerjaan');
            $table->tinyInteger('penjamin');
            $table->tinyInteger('unit');
            $table->tinyInteger('pertanyaan1');
            $table->tinyInteger('pertanyaan2');
            $table->tinyInteger('pertanyaan3');
            $table->tinyInteger('pertanyaan4')->nullable();
            $table->tinyInteger('pertanyaan5');
            $table->tinyInteger('pertanyaan6');
            $table->tinyInteger('pertanyaan7');
            $table->tinyInteger('pertanyaan8');
            $table->tinyInteger('pertanyaan9');
            $table->boolean('pertanyaan10');
            $table->text('saran');
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
        Schema::dropIfExists('kepuasans');
    }
}
