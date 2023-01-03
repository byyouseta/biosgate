<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSepManualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sep_manuals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('noRawat', 20);
            $table->string('no_sep', 25);
            $table->text('tandaTangan');
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
        Schema::dropIfExists('sep_manuals');
    }
}
