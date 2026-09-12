<?php

namespace Tests\Unit;

use Fishinglog\Services\SolunarService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SolunarServiceTest extends TestCase
{
    protected SolunarService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SolunarService();
    }

    #[Test]
    public function it_calculates_julian_date_accurately(): void
    {
        // Jan 1, 2000 corresponds to JD 2451544.5
        $jd = $this->service->calculateJulianDate(2000, 1, 1);
        $this->assertEquals(2451544.5, $jd);
    }

    #[Test]
    public function it_computes_solunar_data_with_expected_structure(): void
    {
        $lat = 47.9944;
        $lng = -84.7619;
        $date = '2026-07-15';

        $data = $this->service->getSolunarData($lat, $lng, $date);

        $this->assertEquals('2026-07-15', $data['date']);
        $this->assertEquals($lat, $data['coordinates']['latitude']);
        $this->assertEquals($lng, $data['coordinates']['longitude']);

        // Moon assertions
        $this->assertArrayHasKey('phase', $data['moon']);
        $this->assertArrayHasKey('emoji', $data['moon']);
        $this->assertArrayHasKey('illumination', $data['moon']);
        $this->assertGreaterThanOrEqual(0, $data['moon']['illumination']);
        $this->assertLessThanOrEqual(100, $data['moon']['illumination']);

        // Major & Minor windows
        $this->assertCount(2, $data['majorWindows']);
        $this->assertCount(2, $data['minorWindows']);
        $this->assertEquals('2 Hours', $data['majorWindows'][0]['duration']);
        $this->assertEquals('1 Hour', $data['minorWindows'][0]['duration']);

        // Sun times
        $this->assertArrayHasKey('sunrise', $data['sun']);
        $this->assertArrayHasKey('sunset', $data['sun']);

        // Rating
        $this->assertArrayHasKey('score', $data['rating']);
        $this->assertGreaterThanOrEqual(1, $data['rating']['score']);
        $this->assertLessThanOrEqual(5, $data['rating']['score']);

        // 24-hour timeline
        $this->assertCount(24, $data['hourlyIntensity']);
    }

    #[Test]
    public function it_generates_correct_feeding_window_durations(): void
    {
        $data = $this->service->getSolunarData(48.0, -84.0, '2026-06-20');

        foreach ($data['majorWindows'] as $maj) {
            $this->assertNotEmpty($maj['start']);
            $this->assertNotEmpty($maj['end']);
            $this->assertNotEmpty($maj['display']);
        }

        foreach ($data['minorWindows'] as $min) {
            $this->assertNotEmpty($min['start']);
            $this->assertNotEmpty($min['end']);
            $this->assertNotEmpty($min['display']);
        }
    }
}
