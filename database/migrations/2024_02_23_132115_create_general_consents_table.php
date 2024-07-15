<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralConsentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_consents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('noRawat', 20);
            $table->string('keyakinan1')->nullable();
            $table->string('keyakinan2')->nullable();
            $table->string('keyakinan3')->nullable();
            $table->string('keyakinan4')->nullable();
            $table->string('privasi1')->nullable();
            $table->string('privasi2')->nullable();
            $table->string('privasi3')->nullable();
            $table->string('namaPj', 200);
            $table->date('tglLahirPj');
            $table->tinyInteger('umurPj');
            $table->string('alamatPj');
            $table->string('dpjp');
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
        Schema::dropIfExists('general_consents');
    }
}
