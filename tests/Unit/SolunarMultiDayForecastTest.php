<?php

namespace Tests\Unit;

use Fishinglog\Services\SolunarService;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class SolunarMultiDayForecastTest extends TestCase
{
    protected SolunarService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SolunarService();
    }

    public function test_it_computes_multi_day_forecast_with_expected_structure(): void
    {
        $lat = 47.9944;
        $lng = -84.7619;
        $startDate = '2026-07-15';
        $days = 7;

        $result = $this->service->getMultiDayForecast($lat, $lng, $startDate, $days);

        $this->assertIsArray($result);
        $this->assertEquals('2026-07-15', $result['startDate']);
        $this->assertEquals('2026-07-21', $result['endDate']);
        $this->assertEquals(7, $result['daysCount']);
        $this->assertCount(7, $result['forecast']);

        $this->assertArrayHasKey('peakDay', $result);
        $this->assertNotNull($result['peakDay']);
        $this->assertArrayHasKey('date', $result['peakDay']);
        $this->assertArrayHasKey('score', $result['peakDay']);
        $this->assertGreaterThanOrEqual(1, $result['peakDay']['score']);
        $this->assertLessThanOrEqual(5, $result['peakDay']['score']);

        foreach ($result['forecast'] as $dayForecast) {
            $this->assertArrayHasKey('date', $dayForecast);
            $this->assertArrayHasKey('moon', $dayForecast);
            $this->assertArrayHasKey('rating', $dayForecast);
            $this->assertArrayHasKey('majorWindows', $dayForecast);
            $this->assertArrayHasKey('minorWindows', $dayForecast);
            $this->assertArrayHasKey('waveCurve', $dayForecast);
            $this->assertCount(2, $dayForecast['majorWindows']);
            $this->assertCount(2, $dayForecast['minorWindows']);
        }
    }

    public function test_it_computes_quick_solunar_summary(): void
    {
        $lat = 45.0000;
        $lng = -80.0000;
        $date = '2026-08-10';

        $summary = $this->service->getQuickSolunarSummary($lat, $lng, $date);

        $this->assertIsArray($summary);
        $this->assertEquals('2026-08-10', $summary['date']);
        $this->assertArrayHasKey('rating', $summary);
        $this->assertArrayHasKey('moon', $summary);
        $this->assertArrayHasKey('majorWindows', $summary);
        $this->assertArrayHasKey('minorWindows', $summary);
    }
}
