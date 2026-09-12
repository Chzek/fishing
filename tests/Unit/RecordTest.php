<?php

namespace Tests\Unit;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use MatanYadaev\EloquentSpatial\Objects\Point;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecordTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function record_belongs_to_angler()
    {
        $angler = Angler::factory()->create();
        $record = Record::factory()->create(['anglers_id' => $angler->id]);

        $this->assertInstanceOf(Angler::class, $record->angler);
        $this->assertSame($angler->id, $record->angler->id);
    }

    #[Test]
    public function record_belongs_to_lake()
    {
        $lake = Lake::factory()->create();
        $record = Record::factory()->create(['lakes_id' => $lake->id]);

        $this->assertInstanceOf(Lake::class, $record->lake);
        $this->assertSame($lake->id, $record->lake->id);
    }

    #[Test]
    public function record_belongs_to_fish_breed()
    {
        $family = FishFamily::factory()->create();
        $breed = FishBreed::factory()->create(['fish_families_id' => $family->id]);
        $record = Record::factory()->create(['fish_breeds_id' => $breed->id]);

        $this->assertInstanceOf(FishBreed::class, $record->fishBreed);
        $this->assertSame($breed->id, $record->fishBreed->id);
    }

    #[Test]
    public function record_belongs_to_optional_lure()
    {
        $lure = Lure::factory()->create();
        $record = Record::factory()->create([
            'lures_id' => $lure->id,
        ]);

        $this->assertInstanceOf(Lure::class, $record->lure);
        $this->assertSame($lure->id, $record->lure->id);
    }

    #[Test]
    public function record_has_morph_many_photos()
    {
        $record = Record::factory()->create();
        $photo = $record->photos()->create([
            'path' => 'photos/test-catch.jpg',
        ]);

        $this->assertCount(1, $record->photos);
        $this->assertTrue($record->photos->contains($photo));
    }

    #[Test]
    public function record_automatically_syncs_spatial_point_location()
    {
        $record = Record::factory()->create([
            'latitude' => 45.1234,
            'longitude' => -78.5678,
        ]);

        $this->assertInstanceOf(Point::class, $record->location);
        $this->assertSame(45.1234, $record->location->latitude);
        $this->assertSame(-78.5678, $record->location->longitude);
    }
}
