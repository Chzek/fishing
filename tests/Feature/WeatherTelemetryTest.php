<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;

use Fishinglog\Services\WeatherTelemetryService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WeatherTelemetryTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_fetches_and_caches_daily_weather_for_a_lake()
    {
        Http::fake([
            'archive-api.open-meteo.com/*' => Http::response([
                'daily' => [
                    'time' => ['2026-07-15'],
                    'temperature_2m_max' => [82.7],
                    'temperature_2m_min' => [71.2],
                    'temperature_2m_mean' => [77.1],
                    'surface_pressure_mean' => [978.6],
                    'wind_speed_10m_max' => [6.9],
                    'wind_direction_10m_dominant' => [64],
                    'weather_code' => [3],
                ],
            ], 200),
        ]);

        $lake = Lake::factory()->create([
            'latitude' => 46.3812,
            'longitude' => -94.3210,
        ]);

        $service = new WeatherTelemetryService();
        $weather = $service->fetchForLakeAndDate($lake, '2026-07-15');

        $this->assertNotNull($weather);
        $this->assertEquals(82.7, $weather->air_temp_max);
        $this->assertEquals(978.6, $weather->barometric_pressure);
        $this->assertEquals('Overcast ☁️', $weather->weather_condition);

        $this->assertDatabaseHas('lake_daily_weather', [
            'lakes_id' => $lake->id,
            'date' => '2026-07-15',
            'weather_code' => 3,
        ]);
    }

    #[Test]
    public function it_handles_offline_or_failed_weather_api_gracefully()
    {
        Http::fake([
            '*' => Http::response(null, 500),
        ]);

        $lake = Lake::factory()->create([
            'latitude' => 46.3812,
            'longitude' => -94.3210,
        ]);

        $service = new WeatherTelemetryService();
        $weather = $service->fetchForLakeAndDate($lake, '2026-07-15');

        $this->assertNull($weather);
    }

    #[Test]
    public function it_runs_weather_sync_artisan_command()
    {
        Http::fake([
            '*' => Http::response([
                'daily' => [
                    'time' => ['2026-07-15'],
                    'temperature_2m_max' => [80.0],
                    'temperature_2m_min' => [70.0],
                    'temperature_2m_mean' => [75.0],
                    'surface_pressure_mean' => [1013.25],
                    'wind_speed_10m_max' => [5.0],
                    'wind_direction_10m_dominant' => [180],
                    'weather_code' => [0],
                ],
            ], 200),
        ]);

        $lake = Lake::factory()->create([
            'latitude' => 46.3812,
            'longitude' => -94.3210,
        ]);

        Record::factory()->create([
            'lakes_id' => $lake->id,
            'caught' => '2026-07-15',
        ]);

        $this->artisan('weather:sync')
            ->assertExitCode(0);

        $this->assertDatabaseHas('lake_daily_weather', [
            'lakes_id' => $lake->id,
            'date' => '2026-07-15',
            'weather_condition' => 'Clear sky ☀️',
        ]);
    }
}
