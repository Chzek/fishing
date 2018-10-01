<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('anglers_id')->required();
            $table->integer('lakes_id')->required();
            $table->integer('fish_breeds_id')->required();
            $table->integer('lures_id')->nullable();
            $table->float('weight', 3, 2)->required();
            $table->float('length', 4, 2)->required();
            $table->float('temperature', 5, 2)->nullable();
            $table->boolean('released')->required()->default(false);
            $table->date('caught')->required();
            $table->integer('trip_id')->nullable();
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
        Schema::dropIfExists('records');
    }
}
