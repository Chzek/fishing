<?php

namespace Tests\Unit;

use Fishinglog\Services\WeatherTelemetryService;
use PHPUnit\Framework\TestCase;

class BarometricPressureVelocityTest extends TestCase
{
    protected WeatherTelemetryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WeatherTelemetryService();
    }

    public function test_it_classifies_rapid_drop_pre_frontal_surge(): void
    {
        // 3-hour drop of 2.5 hPa (from 1015.0 at hour 15 to 1012.5 at hour 18)
        $hourly = [
            ['hour' => 15, 'pressure' => 1015.0],
            ['hour' => 16, 'pressure' => 1014.2],
            ['hour' => 17, 'pressure' => 1013.3],
            ['hour' => 18, 'pressure' => 1012.5],
        ];

        $intel = $this->service->calculatePressureVelocity($hourly, null, null, 18);

        $this->assertEquals('rapid_drop', $intel['classification']);
        $this->assertEquals(-2.5, $intel['velocity_3h']);
        $this->assertStringContainsString('Pre-Frontal Surge', $intel['badge_label']);
        $this->assertStringContainsString('Topwater', $intel['lure_recommendations']);
    }

    public function test_it_classifies_falling_barometer_active_window(): void
    {
        // 3-hour drop of 1.2 hPa
        $hourly = [
            ['hour' => 15, 'pressure' => 1014.0],
            ['hour' => 18, 'pressure' => 1012.8],
        ];

        $intel = $this->service->calculatePressureVelocity($hourly, null, null, 18);

        $this->assertEquals('falling', $intel['classification']);
        $this->assertEquals(-1.2, $intel['velocity_3h']);
        $this->assertStringContainsString('Active Feeding Window', $intel['badge_label']);
    }

    public function test_it_classifies_stable_barometer(): void
    {
        // 3-hour change of +0.2 hPa
        $hourly = [
            ['hour' => 15, 'pressure' => 1013.0],
            ['hour' => 18, 'pressure' => 1013.2],
        ];

        $intel = $this->service->calculatePressureVelocity($hourly, null, null, 18);

        $this->assertEquals('stable', $intel['classification']);
        $this->assertEquals(0.2, $intel['velocity_3h']);
        $this->assertStringContainsString('Consistent Depth', $intel['badge_label']);
    }

    public function test_it_classifies_rising_barometer(): void
    {
        // 3-hour rise of 1.4 hPa
        $hourly = [
            ['hour' => 15, 'pressure' => 1012.0],
            ['hour' => 18, 'pressure' => 1013.4],
        ];

        $intel = $this->service->calculatePressureVelocity($hourly, null, null, 18);

        $this->assertEquals('rising', $intel['classification']);
        $this->assertEquals(1.4, $intel['velocity_3h']);
        $this->assertStringContainsString('Fish Tight to Cover', $intel['badge_label']);
    }

    public function test_it_classifies_rapid_rise_post_front_high_pressure(): void
    {
        // 3-hour rise of 2.3 hPa
        $hourly = [
            ['hour' => 15, 'pressure' => 1010.0],
            ['hour' => 18, 'pressure' => 1012.3],
        ];

        $intel = $this->service->calculatePressureVelocity($hourly, null, null, 18);

        $this->assertEquals('rapid_rise', $intel['classification']);
        $this->assertEquals(2.3, $intel['velocity_3h']);
        $this->assertStringContainsString('Finesse Required', $intel['badge_label']);
        $this->assertStringContainsString('Ned rig', $intel['lure_recommendations']);
    }

    public function test_it_handles_fallback_delta_and_trend(): void
    {
        $intel = $this->service->calculatePressureVelocity(null, -2.0, 'falling');

        $this->assertEquals('falling', $intel['classification']);
        $this->assertEquals(-1.2, $intel['velocity_3h']);
    }
}
