<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use Fishinglog\Models\FishBreed;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FishBreedControllerTest extends TestCase
{
    use DatabaseTransactions;

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
    public function it_can_view_species_dossier_with_telemetry()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $family = \Fishinglog\Models\FishFamily::factory()->create(['name' => 'Salmonidae']);
        $breed = FishBreed::factory()->create([
            'name' => 'Atlantic Salmon',
            'fish_families_id' => $family->id,
            'image' => 'atlantic_salmon',
        ]);

        $angler = \Fishinglog\Models\Angler::factory()->create(['firstName' => 'John', 'lastName' => 'Fisherman']);
        $lake = \Fishinglog\Models\Lake::factory()->create(['name' => 'Lake Huron']);
        $lure = \Fishinglog\Models\Lure::factory()->create(['name' => 'Silver Spoon']);

        \Fishinglog\Models\Record::factory()->create([
            'fish_breeds_id' => $breed->id,
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'lures_id' => $lure->id,
            'length' => 24.50,
            'weight' => 6.20,
            'caught' => '2026-06-15',
        ]);

        $response = $this->get('/fish/' . $breed->id);
        $response->assertStatus(200);
        $response->assertSee('Atlantic Salmon');
        $response->assertSee('Salmonidae');
        $response->assertSee('24.5');
        $response->assertSee('6.2');
        $response->assertSee('Silver Spoon');
        $response->assertSee('John Fisherman');
        $response->assertSee('Lake Huron');
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
