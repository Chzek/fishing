<?php

namespace Tests\Feature;

use Fishinglog\Livewire\Components\GenericDataTable;
use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CatchDirectoryLivewireTest extends TestCase
{
    use DatabaseTransactions;

    private function getDirectoryConfig(): array
    {
        return [
            'modelClass' => Record::class,
            'with' => ['angler', 'lake', 'fishBreed', 'lure'],
            'columns' => [
                ['key' => 'caught', 'label' => 'Date', 'type' => 'date', 'sortable' => true],
                ['key' => 'angler.lastName', 'label' => 'Angler', 'type' => 'angler_name', 'sortable' => true],
                ['key' => 'lake.name', 'label' => 'Lake', 'type' => 'lake_link', 'sortable' => true],
                ['key' => 'fishBreed.name', 'label' => 'Species', 'type' => 'species_name', 'sortable' => true],
                ['key' => 'weight', 'label' => 'Weight', 'type' => 'heavy_record', 'align' => 'center', 'sortable' => true],
                ['key' => 'length', 'label' => 'Length', 'type' => 'lunker_record', 'align' => 'center', 'sortable' => true],
            ],
            'filters' => [
                [
                    'key' => 'species',
                    'type' => 'select',
                    'label' => 'All Species',
                    'column' => 'fish_breeds_id',
                ],
                [
                    'key' => 'length',
                    'type' => 'operator_number',
                    'label' => 'Length',
                    'column' => 'length',
                    'operatorKey' => 'lengthOperator',
                    'defaultOperator' => '>',
                ],
                [
                    'key' => 'caught',
                    'type' => 'date_range',
                    'label' => 'Date Range',
                    'column' => 'caught',
                    'startKey' => 'startDate',
                    'endKey' => 'endDate',
                ],
            ],
            'searchPlaceholder' => 'Search species, lake, angler, lure...',
            'itemName' => 'catches',
            'perPage' => 15,
        ];
    }

    #[Test]
    public function catch_directory_livewire_component_renders_successfully()
    {
        $user = User::factory()->create();
        $this->be($user);

        Livewire::test(GenericDataTable::class, $this->getDirectoryConfig())
            ->assertStatus(200)
            ->assertSee('All Species');
    }

    #[Test]
    public function search_filter_filters_records_dynamically()
    {
        $user = User::factory()->create();
        $this->be($user);

        $walleye = FishBreed::factory()->create(['name' => 'Walleye Special']);
        $bass = FishBreed::factory()->create(['name' => 'Smallmouth Bass']);
        $lake = Lake::factory()->create();
        $angler = Angler::factory()->create();

        Record::factory()->create([
            'fish_breeds_id' => $walleye->id,
            'lakes_id' => $lake->id,
            'anglers_id' => $angler->id,
            'length' => 24.5,
        ]);

        Record::factory()->create([
            'fish_breeds_id' => $bass->id,
            'lakes_id' => $lake->id,
            'anglers_id' => $angler->id,
            'length' => 15.0,
        ]);

        Livewire::test(GenericDataTable::class, $this->getDirectoryConfig())
            ->set('search', 'Walleye Special')
            ->assertSee('Walleye Special')
            ->assertViewHas('records', function ($records) use ($walleye) {
                return $records->count() === 1 && $records->first()->fish_breeds_id === $walleye->id;
            });
    }

    #[Test]
    public function species_dropdown_filters_records_by_uuid()
    {
        $user = User::factory()->create();
        $this->be($user);

        $pike = FishBreed::factory()->create(['name' => 'Northern Pike']);
        $walleye = FishBreed::factory()->create(['name' => 'Walleye']);
        $lake = Lake::factory()->create();
        $angler = Angler::factory()->create();

        $recordPike = Record::factory()->create([
            'fish_breeds_id' => $pike->id,
            'lakes_id' => $lake->id,
            'anglers_id' => $angler->id,
            'length' => 28.0,
        ]);

        $recordWalleye = Record::factory()->create([
            'fish_breeds_id' => $walleye->id,
            'lakes_id' => $lake->id,
            'anglers_id' => $angler->id,
            'length' => 18.0,
        ]);

        Livewire::test(GenericDataTable::class, $this->getDirectoryConfig())
            ->set('filterState.species', $pike->id)
            ->assertViewHas('records', function ($records) use ($pike) {
                return $records->count() === 1 && $records->first()->fish_breeds_id === $pike->id;
            });
    }

    #[Test]
    public function length_and_operator_filters_records_dynamically()
    {
        $user = User::factory()->create();
        $this->be($user);

        $pike = FishBreed::factory()->create(['name' => 'Northern Pike']);
        $lake = Lake::factory()->create();
        $angler = Angler::factory()->create();

        Record::factory()->create([
            'fish_breeds_id' => $pike->id,
            'lakes_id' => $lake->id,
            'anglers_id' => $angler->id,
            'length' => 32.0,
        ]);

        Record::factory()->create([
            'fish_breeds_id' => $pike->id,
            'lakes_id' => $lake->id,
            'anglers_id' => $angler->id,
            'length' => 14.0,
        ]);

        Livewire::test(GenericDataTable::class, $this->getDirectoryConfig())
            ->set('filterState.lengthOperator', '>')
            ->set('filterState.length', '20')
            ->assertViewHas('records', function ($records) {
                return $records->count() === 1 && (float)$records->first()->length === 32.0;
            });
    }

    #[Test]
    public function date_range_filter_filters_records_between_dates()
    {
        $user = User::factory()->create();
        $this->be($user);

        $lake = Lake::factory()->create();
        $angler = Angler::factory()->create();
        $breed = FishBreed::factory()->create();

        Record::factory()->create([
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'anglers_id' => $angler->id,
            'caught' => '2026-05-15',
            'length' => 22.0,
        ]);

        Record::factory()->create([
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'anglers_id' => $angler->id,
            'caught' => '2026-08-20',
            'length' => 18.0,
        ]);

        Livewire::test(GenericDataTable::class, $this->getDirectoryConfig())
            ->set('filterState.startDate', '2026-05-01')
            ->set('filterState.endDate', '2026-06-01')
            ->assertViewHas('records', function ($records) {
                return $records->count() === 1 && (float)$records->first()->length === 22.0;
            });
    }

    #[Test]
    public function column_click_sorting_sorts_records_dynamically()
    {
        $user = User::factory()->create();
        $this->be($user);

        $pike = FishBreed::factory()->create(['name' => 'Northern Pike']);
        $lake = Lake::factory()->create();
        $angler = Angler::factory()->create();

        Record::factory()->create([
            'fish_breeds_id' => $pike->id,
            'lakes_id' => $lake->id,
            'anglers_id' => $angler->id,
            'length' => 10.0,
        ]);

        Record::factory()->create([
            'fish_breeds_id' => $pike->id,
            'lakes_id' => $lake->id,
            'anglers_id' => $angler->id,
            'length' => 40.0,
        ]);

        // 1. Sort by length asc
        Livewire::test(GenericDataTable::class, $this->getDirectoryConfig())
            ->call('sortByColumn', 'length')
            ->assertSet('sortBy', 'length')
            ->assertSet('sortOrder', 'asc')
            ->assertViewHas('records', function ($records) {
                return (float)$records->first()->length === 10.0;
            });
    }
}
