<?php

namespace Tests\Unit;

use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class FishFamilyTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_fish_family_has_many_breeds()
    {
        $family = FishFamily::factory()->create();

        $breed1 = FishBreed::factory()->create(['fish_families_id' => $family->id]);
        $breed2 = FishBreed::factory()->create(['fish_families_id' => $family->id]);

        $this->assertEquals(2, $family->breeds()->count());
        $this->assertTrue($family->breeds->contains($breed1));
        $this->assertTrue($family->breeds->contains($breed2));
    }
}
