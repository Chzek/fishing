<?php

namespace Tests\Feature;

use Fishinglog\Livewire\Components\GenericDataTable;
use Fishinglog\Models\Angler;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GenericDataTableLivewireTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function generic_data_table_renders_successfully_for_lakes()
    {
        $user = User::factory()->create();
        $this->be($user);

        $lake1 = Lake::factory()->create(['name' => 'Wawa Lake', 'latitude' => 47.9942, 'longitude' => -84.7732]);
        $lake2 = Lake::factory()->create(['name' => 'Davies Lake', 'latitude' => null, 'longitude' => null]);

        Livewire::test(GenericDataTable::class, [
            'modelClass' => Lake::class,
            'columns' => [
                ['key' => 'name', 'label' => 'Lake Name', 'type' => 'lake_name', 'searchable' => true],
            ],
            'itemName' => 'lakes',
        ])
        ->assertStatus(200)
        ->assertSee('Wawa Lake')
        ->assertSee('Davies Lake')
        ->assertSee('text-emerald-600')
        ->assertSee('map-pin-off');
    }

    #[Test]
    public function generic_data_table_search_filters_records_dynamically()
    {
        $user = User::factory()->create();
        $this->be($user);

        $lake1 = Lake::factory()->create(['name' => 'Superior Waters']);
        $lake2 = Lake::factory()->create(['name' => 'Catfish Lagoon']);

        Livewire::test(GenericDataTable::class, [
            'modelClass' => Lake::class,
            'columns' => [
                ['key' => 'name', 'label' => 'Lake Name', 'searchable' => true],
            ],
            'itemName' => 'lakes',
        ])
        ->set('search', 'Superior')
        ->assertSee('Superior Waters')
        ->assertDontSee('Catfish Lagoon');
    }

    #[Test]
    public function generic_data_table_header_click_sorts_records_dynamically_in_tristate_cycle()
    {
        $user = User::factory()->create();
        $this->be($user);

        $lakeA = Lake::factory()->create(['name' => 'Alpha Lake']);
        $lakeZ = Lake::factory()->create(['name' => 'Zeta Lake']);

        Livewire::test(GenericDataTable::class, [
            'modelClass' => Lake::class,
            'columns' => [
                ['key' => 'name', 'label' => 'Lake Name', 'sortable' => true],
            ],
            'itemName' => 'lakes',
            'defaultSortBy' => 'id',
            'defaultSortOrder' => 'asc',
        ])
        // 1. Initial State: Default sort (id asc)
        ->assertSet('sortBy', 'id')
        // 2. First Click on 'name': Ascending
        ->call('sortByColumn', 'name', false)
        ->assertSet('sortBy', 'name')
        ->assertSet('sortOrder', 'asc')
        ->assertSeeInOrder(['Alpha Lake', 'Zeta Lake'])
        // 3. Second Click on 'name': Descending
        ->call('sortByColumn', 'name', false)
        ->assertSet('sortBy', 'name')
        ->assertSet('sortOrder', 'desc')
        ->assertSeeInOrder(['Zeta Lake', 'Alpha Lake'])
        // 4. Third Click on 'name': Tri-State Reset to Default (id asc)
        ->call('sortByColumn', 'name', false)
        ->assertSet('sortBy', 'id')
        ->assertSet('sortOrder', 'asc');
    }

    #[Test]
    public function generic_data_table_supports_shift_click_multi_column_sorting()
    {
        $user = User::factory()->create();
        $this->be($user);

        Livewire::test(GenericDataTable::class, [
            'modelClass' => Lake::class,
            'columns' => [
                ['key' => 'name', 'label' => 'Lake Name', 'sortable' => true],
                ['key' => 'records_count', 'label' => 'Total Catches', 'type' => 'count', 'sortable' => true],
            ],
            'itemName' => 'lakes',
            'defaultSortBy' => 'id',
            'defaultSortOrder' => 'asc',
        ])
        // Single Click on 'name'
        ->call('sortByColumn', 'name', false)
        ->assertSet('sorts', [['column' => 'name', 'direction' => 'asc']])
        // Shift + Click on 'records_count'
        ->call('sortByColumn', 'records_count', true)
        ->assertSet('sorts', [
            ['column' => 'name', 'direction' => 'asc'],
            ['column' => 'records_count', 'direction' => 'asc']
        ])
        // Shift + Click again on 'records_count' toggles to desc
        ->call('sortByColumn', 'records_count', true)
        ->assertSet('sorts', [
            ['column' => 'name', 'direction' => 'asc'],
            ['column' => 'records_count', 'direction' => 'desc']
        ])
        // Single Click on 'records_count' without Shift resets stack to single sort
        ->call('sortByColumn', 'records_count', false)
        ->assertSet('sorts', [
            ['column' => 'records_count', 'direction' => 'asc']
        ]);
    }

    #[Test]
    public function generic_data_table_sorts_by_calculated_relation_counts()
    {
        $user = User::factory()->create();
        $this->be($user);

        $lake1 = Lake::factory()->create(['name' => 'Empty Waters']);
        $lake2 = Lake::factory()->create(['name' => 'Heavy Catches Lake']);

        Record::factory()->create(['lakes_id' => $lake2->id]);
        Record::factory()->create(['lakes_id' => $lake2->id]);

        Livewire::test(GenericDataTable::class, [
            'modelClass' => Lake::class,
            'columns' => [
                ['key' => 'name', 'label' => 'Lake Name', 'sortable' => true],
                ['key' => 'records_count', 'label' => 'Total Catches', 'type' => 'count', 'sortable' => true],
            ],
            'itemName' => 'lakes',
        ])
        ->set('sortBy', 'records_count')
        ->set('sortOrder', 'desc')
        ->assertStatus(200)
        ->assertSeeInOrder(['Heavy Catches Lake', 'Empty Waters']);
    }

    #[Test]
    public function generic_data_table_renders_successfully_for_anglers()
    {
        $user = User::factory()->create();
        $this->be($user);

        $angler = Angler::factory()->create(['firstName' => 'Samantha', 'lastName' => 'Walker']);

        Livewire::test(GenericDataTable::class, [
            'modelClass' => Angler::class,
            'columns' => [
                ['key' => 'full_name', 'label' => 'Angler Name', 'searchable' => true],
            ],
            'itemName' => 'anglers',
        ])
        ->assertStatus(200)
        ->assertSee('Walker');
    }

    #[Test]
    public function generic_data_table_renders_successfully_for_expeditions()
    {
        $user = User::factory()->create();
        $this->be($user);

        $expedition = \Fishinglog\Models\Expedition::create([
            'description' => 'Wilderness Fly Fishing Trip 2026',
            'start' => '2026-06-01',
            'finish' => '2026-06-07',
        ]);

        Livewire::test(GenericDataTable::class, [
            'modelClass' => \Fishinglog\Models\Expedition::class,
            'columns' => [
                ['key' => 'description', 'label' => 'Trip Description', 'searchable' => true],
            ],
            'itemName' => 'expeditions',
        ])
        ->assertStatus(200)
        ->assertSee('Wilderness Fly Fishing Trip 2026');
    }

    #[Test]
    public function generic_data_table_supports_family_prefilter_for_species()
    {
        $user = User::factory()->create();
        $this->be($user);

        $family1 = \Fishinglog\Models\FishFamily::factory()->create(['name' => 'Salmonidae']);
        $family2 = \Fishinglog\Models\FishFamily::factory()->create(['name' => 'Centrarchidae']);

        $fish1 = \Fishinglog\Models\FishBreed::create(['name' => 'Brook Trout', 'fish_families_id' => $family1->id]);
        $fish2 = \Fishinglog\Models\FishBreed::create(['name' => 'Smallmouth Bass', 'fish_families_id' => $family2->id]);

        $response = $this->get('/fish?family=' . $family1->id);

        $response->assertStatus(200);
        $response->assertSee('Brook Trout');
        $response->assertDontSee('Smallmouth Bass');
    }

    #[Test]
    public function generic_data_table_supports_only_trashed_records()
    {
        $user = User::factory()->create();
        $this->be($user);

        $lake = Lake::factory()->create(['name' => 'Ghost Lake']);
        $lake->delete(); // Soft delete

        Livewire::test(GenericDataTable::class, [
            'modelClass' => Lake::class,
            'onlyTrashed' => true,
            'columns' => [
                ['key' => 'name', 'label' => 'Lake Name', 'searchable' => true],
            ],
            'itemName' => 'lakes',
        ])
        ->assertStatus(200)
        ->assertSee('Ghost Lake')
        ->assertSee('Restore')
        ->assertSee('Purge');
    }
}
