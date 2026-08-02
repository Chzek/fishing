<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('lake_daily_weather')) {
            Schema::create('lake_daily_weather', function (Blueprint $table) {
                $table->id();
                $table->integer('lakes_id')->unsigned();
                $table->date('date');
                $table->decimal('air_temp_max', 5, 2)->nullable();
                $table->decimal('air_temp_min', 5, 2)->nullable();
                $table->decimal('air_temp_mean', 5, 2)->nullable();
                $table->decimal('barometric_pressure', 6, 2)->nullable();
                $table->decimal('wind_speed_max', 5, 2)->nullable();
                $table->integer('wind_direction_dominant')->nullable();
                $table->string('weather_condition')->nullable();
                $table->integer('weather_code')->nullable();
                $table->timestamps();

                $table->unique(['lakes_id', 'date']);
                $table->foreign('lakes_id')->references('id')->on('lakes')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lake_daily_weather');
    }
};
