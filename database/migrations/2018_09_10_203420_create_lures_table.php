<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lures', function (Blueprint $table) {
            $table->increments('id');
            $table->string('color');
            $table->string('name');
            $table->string('size');
            $table->timestamps();

            $table->unique(array('name','color','size'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lures');
    }
}
