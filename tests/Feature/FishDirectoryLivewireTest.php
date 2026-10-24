<?php

namespace Tests\Feature;

use Fishinglog\Livewire\Directory\FishDirectory;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FishDirectoryLivewireTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_renders_fish_directory_on_fish_index_route(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $family = FishFamily::factory()->create(['name' => 'Salmonidae']);
        $fish = FishBreed::factory()->create([
            'name' => 'Atlantic Salmon',
            'fish_families_id' => $family->id,
        ]);

        $response = $this->get('/fish');

        $response->assertStatus(200);
        $response->assertSeeLivewire(FishDirectory::class);
        $response->assertSee('Atlantic Salmon');
        $response->assertSee('Salmonidae');
        $response->assertSee('Grid View');
        $response->assertSee('Table View');
    }

    #[Test]
    public function it_supports_switching_between_grid_and_table_views(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $family = FishFamily::factory()->create(['name' => 'Esocidae']);
        $fish = FishBreed::factory()->create([
            'name' => 'Muskellunge',
            'fish_families_id' => $family->id,
        ]);

        Livewire::test(FishDirectory::class)
            ->assertSet('viewMode', 'grid')
            ->assertSee('Muskellunge')
            ->assertSee('View Dossier')
            ->call('setViewMode', 'table')
            ->assertSet('viewMode', 'table')
            ->assertSee('Species & Taxonomy', false)
            ->assertSee('Total Logged')
            ->assertSee('Longest Record')
            ->assertSee('Muskellunge');
    }

    #[Test]
    public function it_filters_species_by_search_query(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $family1 = FishFamily::factory()->create(['name' => 'Percidae']);
        $family2 = FishFamily::factory()->create(['name' => 'Centrarchidae']);

        $walleye = FishBreed::factory()->create([
            'name' => 'Yellow Walleye',
            'fish_families_id' => $family1->id,
        ]);
        $bass = FishBreed::factory()->create([
            'name' => 'Smallmouth Bass',
            'fish_families_id' => $family2->id,
        ]);

        Livewire::test(FishDirectory::class)
            ->set('search', 'Walleye')
            ->assertSee('Yellow Walleye')
            ->assertDontSee('Smallmouth Bass')
            ->set('search', 'Centrarchidae')
            ->assertSee('Smallmouth Bass')
            ->assertDontSee('Yellow Walleye');
    }

    #[Test]
    public function it_filters_species_by_family_pill(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pikeFamily = FishFamily::factory()->create(['name' => 'Esocidae']);
        $bassFamily = FishFamily::factory()->create(['name' => 'Centrarchidae']);

        $pike = FishBreed::factory()->create([
            'name' => 'Northern Pike',
            'fish_families_id' => $pikeFamily->id,
        ]);
        $bass = FishBreed::factory()->create([
            'name' => 'Largemouth Bass',
            'fish_families_id' => $bassFamily->id,
        ]);

        Livewire::test(FishDirectory::class)
            ->call('selectFamily', (string) $pikeFamily->id)
            ->assertSet('family', (string) $pikeFamily->id)
            ->assertSee('Northern Pike')
            ->assertDontSee('Largemouth Bass')
            // Toggle off when clicking the same family pill
            ->call('selectFamily', (string) $pikeFamily->id)
            ->assertSet('family', '')
            ->assertSee('Northern Pike')
            ->assertSee('Largemouth Bass');
    }

    #[Test]
    public function it_sorts_species_by_columns_in_table_view(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $fishA = FishBreed::factory()->create(['name' => 'Alpha Fish']);
        $fishZ = FishBreed::factory()->create(['name' => 'Zulu Fish']);

        Livewire::test(FishDirectory::class)
            ->call('setViewMode', 'table')
            ->call('sortByColumn', 'name')
            ->assertSet('sortBy', 'name')
            ->assertSet('sortOrder', 'desc')
            ->call('sortByColumn', 'name')
            ->assertSet('sortOrder', 'asc');
    }

    #[Test]
    public function it_clears_active_filters(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(FishDirectory::class)
            ->set('search', 'Trout')
            ->set('family', '123')
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('family', '');
    }

    #[Test]
    public function it_renders_telemetry_metrics_for_longest_and_heaviest_catches(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $breed = FishBreed::factory()->create(['name' => 'Trophy Muskie']);
        Record::factory()->create([
            'fish_breeds_id' => $breed->id,
            'length' => 54.5,
            'weight' => 42.0,
        ]);

        Livewire::test(FishDirectory::class)
            ->set('search', 'Trophy Muskie')
            ->assertSee('54.5″')
            ->assertSee('42 lbs.');
    }
}
