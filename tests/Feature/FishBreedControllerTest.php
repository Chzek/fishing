<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use Fishinglog\Models\FishBreed;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FishBreedControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_view_fish_taxonomy_index_with_telemetry()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $family = \Fishinglog\Models\FishFamily::factory()->create(['name' => 'Esocidae']);
        $breed = FishBreed::factory()->create([
            'name' => 'Northern Pike',
            'fish_families_id' => $family->id,
        ]);

        $response = $this->get('/fish');
        $response->assertStatus(200);
        $response->assertSee('Fish Species & Taxonomy Guide', false);
        $response->assertSee('Northern Pike');
        $response->assertSee('Esocidae');
    }

    #[Test]
    public function it_can_filter_fish_index_by_family()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pikeFamily = \Fishinglog\Models\FishFamily::factory()->create(['name' => 'Esocidae']);
        $bassFamily = \Fishinglog\Models\FishFamily::factory()->create(['name' => 'Centrarchidae']);

        $pike = FishBreed::factory()->create([
            'name' => 'Northern Pike',
            'fish_families_id' => $pikeFamily->id,
        ]);
        $bass = FishBreed::factory()->create([
            'name' => 'Largemouth Bass',
            'fish_families_id' => $bassFamily->id,
        ]);

        $response = $this->get('/fish?family=' . $pikeFamily->id);
        $response->assertStatus(200);
        $response->assertSee('Northern Pike');
        $response->assertDontSee('Largemouth Bass');
    }

    #[Test]
    public function it_can_search_fish_index_by_species_name_or_family()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $family = \Fishinglog\Models\FishFamily::factory()->create(['name' => 'Salmonidae']);
        $trout = FishBreed::factory()->create([
            'name' => 'Rainbow Trout',
            'fish_families_id' => $family->id,
        ]);
        $walleye = FishBreed::factory()->create([
            'name' => 'Walleye',
        ]);

        $response = $this->get('/fish?search=Rainbow');
        $response->assertStatus(200);
        $response->assertSee('Rainbow Trout');
        $response->assertDontSee('Walleye');

        $familySearchResponse = $this->get('/fish?search=Salmonidae');
        $familySearchResponse->assertStatus(200);
        $familySearchResponse->assertSee('Rainbow Trout');
    }

    #[Test]
    public function it_can_update_fish_breed_without_image()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $breed = FishBreed::factory()->create([
            'name' => 'Old Name',
            'image' => 'old_image.jpg',
        ]);

        $response = $this->put('/fish/breed', [
            'id' => $breed->id,
            'name' => 'New Name',
            'fish_families_id' => $breed->fish_families_id,
        ]);

        $response->assertRedirect('/fish/' . $breed->id);

        $this->assertDatabaseHas('fish_breeds', [
            'id' => $breed->id,
            'name' => 'New Name',
            'image' => 'old_image.jpg',
        ]);
    }
}
