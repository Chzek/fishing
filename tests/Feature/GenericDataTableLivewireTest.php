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
    public function generic_data_table_header_click_sorts_records_dynamically()
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
        ])
        ->set('sortBy', 'name')
        ->set('sortOrder', 'asc')
        ->assertSeeInOrder(['Alpha Lake', 'Zeta Lake'])
        ->call('sortByColumn', 'name')
        ->assertSet('sortOrder', 'desc')
        ->assertSeeInOrder(['Zeta Lake', 'Alpha Lake']);
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
