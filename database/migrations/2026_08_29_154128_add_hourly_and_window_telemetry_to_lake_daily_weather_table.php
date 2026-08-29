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
        Schema::table('lake_daily_weather', function (Blueprint $table) {
            $table->json('hourly_telemetry')->nullable()->after('weather_condition');
            $table->decimal('window_pressure_start', 6, 2)->nullable()->after('hourly_telemetry');
            $table->decimal('window_pressure_end', 6, 2)->nullable()->after('window_pressure_start');
            $table->decimal('window_pressure_delta', 6, 2)->nullable()->after('window_pressure_end');
            $table->string('pressure_trend', 20)->nullable()->after('window_pressure_delta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lake_daily_weather', function (Blueprint $table) {
            $table->dropColumn([
                'hourly_telemetry',
                'window_pressure_start',
                'window_pressure_end',
                'window_pressure_delta',
                'pressure_trend',
            ]);
        });
    }
};
