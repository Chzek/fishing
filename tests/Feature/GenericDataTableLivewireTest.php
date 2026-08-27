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

        $lake1 = Lake::factory()->create(['name' => 'Wawa Lake']);
        $lake2 = Lake::factory()->create(['name' => 'Davies Lake']);

        Livewire::test(GenericDataTable::class, [
            'modelClass' => Lake::class,
            'columns' => [
                ['key' => 'name', 'label' => 'Lake Name', 'searchable' => true],
            ],
            'itemName' => 'lakes',
        ])
        ->assertStatus(200)
        ->assertSee('Wawa Lake')
        ->assertSee('Davies Lake');
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
        ->call('sortByColumn', 'name')
        ->assertSet('sortBy', 'name')
        ->assertSet('sortOrder', 'asc')
        ->assertSeeInOrder(['Alpha Lake', 'Zeta Lake'])
        // 3. Second Click on 'name': Descending
        ->call('sortByColumn', 'name')
        ->assertSet('sortBy', 'name')
        ->assertSet('sortOrder', 'desc')
        ->assertSeeInOrder(['Zeta Lake', 'Alpha Lake'])
        // 4. Third Click on 'name': Tri-State Reset to Default (id asc)
        ->call('sortByColumn', 'name')
        ->assertSet('sortBy', 'id')
        ->assertSet('sortOrder', 'asc');
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
}
