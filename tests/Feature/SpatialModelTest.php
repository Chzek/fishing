<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Tests\TestCase;

class SpatialModelTest extends TestCase
{
    use DatabaseTransactions;

    public function test_lake_automatically_synchronizes_latitude_longitude_with_spatial_point(): void
    {
        $lake = Lake::create([
            'name' => 'Spatial Test Lake ' . uniqid(),
            'latitude' => 48.0125,
            'longitude' => -84.6210,
        ]);

        $this->assertInstanceOf(Point::class, $lake->location);
        $this->assertEquals(48.0125, round($lake->location->latitude, 4));
        $this->assertEquals(-84.6210, round($lake->location->longitude, 4));
        $this->assertEquals(4326, $lake->location->srid);

        // Update via Point object and verify latitude/longitude sync
        $lake->location = new Point(48.5000, -84.9000, 4326);
        $lake->save();

        $lake->refresh();
        $this->assertEquals(48.5000, round($lake->latitude, 4));
        $this->assertEquals(-84.9000, round($lake->longitude, 4));
    }

    public function test_record_automatically_synchronizes_latitude_longitude_with_spatial_point(): void
    {
        $angler = Angler::factory()->create();
        $breed = FishBreed::factory()->create();
        $lake = Lake::factory()->create();

        $record = Record::create([
            'anglers_id' => $angler->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'caught' => now(),
            'latitude' => 48.1234,
            'longitude' => -84.5678,
            'length' => 24.5,
            'weight' => 5.2,
        ]);

        $this->assertInstanceOf(Point::class, $record->location);
        $this->assertEquals(48.1234, round($record->location->latitude, 4));
        $this->assertEquals(-84.5678, round($record->location->longitude, 4));

        // Verify GeoJSON serialization
        $geoJson = json_decode($record->location->toJson(), true);
        $this->assertEquals('Point', $geoJson['type']);
        $this->assertEquals([-84.5678, 48.1234], $geoJson['coordinates']);
    }

    public function test_lake_spatial_nearby_query_calculates_spherical_distance_accurately(): void
    {
        $centerLake = Lake::create([
            'name' => 'Center Lake ' . uniqid(),
            'latitude' => 48.0000,
            'longitude' => -84.7000,
        ]);

        $closeLake = Lake::create([
            'name' => 'Close Lake ' . uniqid(),
            'latitude' => 48.0100, // ~0.7 miles away
            'longitude' => -84.7050,
        ]);

        $mediumLake = Lake::create([
            'name' => 'Medium Lake ' . uniqid(),
            'latitude' => 48.0300, // ~2.2 miles away
            'longitude' => -84.7200,
        ]);

        $farLake = Lake::create([
            'name' => 'Far Lake ' . uniqid(),
            'latitude' => 48.5000, // ~35 miles away
            'longitude' => -85.0000,
        ]);

        // Query within 1.5 miles
        $results15 = Lake::nearby($centerLake->latitude, $centerLake->longitude, 1.5, $centerLake->id);
        $this->assertTrue($results15->contains('id', $closeLake->id));
        $this->assertFalse($results15->contains('id', $mediumLake->id));
        $this->assertFalse($results15->contains('id', $farLake->id));

        // Query within 5 miles
        $results5 = Lake::nearby($centerLake->latitude, $centerLake->longitude, 5.0, $centerLake->id);
        $this->assertTrue($results5->contains('id', $closeLake->id));
        $this->assertTrue($results5->contains('id', $mediumLake->id));
        $this->assertFalse($results5->contains('id', $farLake->id));

        // Verify distance ordering
        $this->assertLessThan($results5->firstWhere('id', $mediumLake->id)->distance, $results5->firstWhere('id', $closeLake->id)->distance);
    }
}
