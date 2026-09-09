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

    #[Test]
    public function it_renders_tactical_angler_intelligence_and_trophy_benchmarks_on_species_show()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $family = \Fishinglog\Models\FishFamily::factory()->create(['name' => 'Centrarchidae']);
        $smallmouth = FishBreed::factory()->create([
            'name' => 'Smallmouth Bass',
            'fish_families_id' => $family->id,
        ]);

        $angler1 = \Fishinglog\Models\Angler::factory()->create(['firstName' => 'Bob', 'lastName' => 'Angler']);
        $angler2 = \Fishinglog\Models\Angler::factory()->create(['firstName' => 'Alice', 'lastName' => 'Pro']);
        $lake1 = \Fishinglog\Models\Lake::factory()->create(['name' => 'Lake Nipissing']);
        $lake2 = \Fishinglog\Models\Lake::factory()->create(['name' => 'French River']);
        $lure1 = \Fishinglog\Models\Lure::factory()->create([
            'name' => 'Tube Jig',
            'category' => 'Soft Plastics',
            'color' => 'Green Pumpkin',
        ]);
        $lure2 = \Fishinglog\Models\Lure::factory()->create([
            'name' => 'X-Rap Jerkbait',
            'category' => 'Jerkbaits',
            'color' => 'Silver Blue',
        ]);

        // Catches with trophy sizes
        \Fishinglog\Models\Record::factory()->create([
            'fish_breeds_id' => $smallmouth->id,
            'anglers_id' => $angler1->id,
            'lakes_id' => $lake1->id,
            'lures_id' => $lure1->id,
            'length' => 21.25,
            'weight' => 5.40,
            'released' => true,
            'caught' => '2026-07-10',
        ]);

        \Fishinglog\Models\Record::factory()->create([
            'fish_breeds_id' => $smallmouth->id,
            'anglers_id' => $angler1->id,
            'lakes_id' => $lake1->id,
            'lures_id' => $lure1->id,
            'length' => 19.50,
            'weight' => 4.20,
            'released' => true,
            'caught' => '2026-07-12',
        ]);

        \Fishinglog\Models\Record::factory()->create([
            'fish_breeds_id' => $smallmouth->id,
            'anglers_id' => $angler2->id,
            'lakes_id' => $lake2->id,
            'lures_id' => $lure2->id,
            'length' => 20.50,
            'weight' => 4.90,
            'released' => true,
            'caught' => '2026-08-05',
        ]);

        $response = $this->get('/fish/' . $smallmouth->id);
        $response->assertStatus(200);

        // Verify Hero & Dossier
        $response->assertSee('Smallmouth Bass');
        $response->assertSee('Centrarchidae');
        $response->assertSee('Species Intelligence Dossier', false);

        // Verify Trophy & Master Angler Hall of Fame
        $response->assertSee('Trophy Records & Benchmark Hall of Fame', false);
        $response->assertSee('Ontario Master Angler: 20 in.', false);
        $response->assertSee('Length Champion', false);
        $response->assertSee('21.3 inches', false);
        $response->assertSee('Bob Angler');
        $response->assertSee('5.4 lbs');

        // Verify Tactical Lure & Tackle Matrix
        $response->assertSee('Productive Tackle & Lures', false);
        $response->assertSee('Soft Plastics', false);
        $response->assertSee('Green Pumpkin', false);
        $response->assertSee('Tube Jig', false);

        // Verify Waterbody Hotspots & Lake Records
        $response->assertSee('Waterbody Hotspot Rankings', false);
        $response->assertSee('Lake Nipissing', false);
        $response->assertSee('French River', false);

        // Verify Species Angler Spotlight Crown
        $response->assertSee('Species Angler Hall of Fame', false);
        $response->assertSee('Top Species Angler', false);
        $response->assertSee('Bob Angler');
    }
}
