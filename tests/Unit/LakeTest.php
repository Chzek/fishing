<?php

namespace Tests\Unit;

use Fishinglog\Models\FishingZone;
use Fishinglog\Models\Lake;
use Fishinglog\Models\LakeDailyWeather;
use Fishinglog\Models\Record;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use MatanYadaev\EloquentSpatial\Objects\Point;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LakeTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_can_create_a_lake_and_sync_spatial_location()
    {
        $lake = Lake::factory()->create([
            'name' => 'Wawa Lake',
            'latitude' => 47.9944,
            'longitude' => -84.7619,
        ]);

        $this->assertDatabaseHas('lakes', [
            'id' => $lake->id,
            'name' => 'Wawa Lake',
        ]);
        $this->assertInstanceOf(Point::class, $lake->location);
        $this->assertSame(47.9944, $lake->location->latitude);
        $this->assertSame(-84.7619, $lake->location->longitude);
    }

    #[Test]
    public function lake_has_records_relationship()
    {
        $lake = Lake::factory()->create();
        $record = Record::factory()->create(['lakes_id' => $lake->id]);

        $this->assertCount(1, $lake->records);
        $this->assertTrue($lake->records->contains($record));
    }

    #[Test]
    public function lake_belongs_to_optional_fishing_zone()
    {
        $zone = FishingZone::create([
            'code' => 'FMZ10',
            'name' => 'Fisheries Management Zone 10',
            'province' => 'Ontario',
        ]);

        $lake = Lake::factory()->create(['fishing_zone_id' => $zone->id]);

        $this->assertInstanceOf(FishingZone::class, $lake->fishingZone);
        $this->assertSame($zone->id, $lake->fishingZone->id);
    }

    #[Test]
    public function lake_has_many_daily_weather_entries()
    {
        $lake = Lake::factory()->create();
        $weather = LakeDailyWeather::create([
            'lakes_id' => $lake->id,
            'date' => '2026-08-01',
            'temperature_min' => 14.5,
            'temperature_max' => 26.2,
            'weather_code' => 1,
            'source' => 'Open-Meteo',
        ]);

        $this->assertCount(1, $lake->dailyWeather);
        $this->assertTrue($lake->dailyWeather->contains($weather));
    }
}
