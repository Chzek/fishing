<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RecordPrecisionTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function trophy_fish_weight_over_ten_pounds_can_be_saved()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create();
        $lure = Lure::factory()->create();

        // 15.75 lb Largemouth Bass / Pike / Salmon
        $response = $this->post('/record', [
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'lures_id' => $lure->id,
            'weight' => 15.75,
            'length' => 32.50,
            'temperature' => 65,
            'released' => 1,
            'caught' => '2026-08-01',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('records', [
            'anglers_id' => $angler->id,
            'weight' => 15.75,
            'length' => 32.50,
        ]);
    }

    #[Test]
    public function trophy_fish_length_over_one_hundred_inches_can_be_saved()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create();

        // 105.5 inch Sturgeon / Tarpon
        $response = $this->post('/record', [
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'weight' => 185.50,
            'length' => 105.50,
            'released' => 1,
            'caught' => '2026-08-01',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('records', [
            'anglers_id' => $angler->id,
            'weight' => 185.50,
            'length' => 105.50,
        ]);
    }
}
