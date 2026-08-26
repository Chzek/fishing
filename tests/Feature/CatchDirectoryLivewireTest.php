<?php

namespace Tests\Feature;

use Fishinglog\Livewire\Directory\CatchDirectory;
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

    #[Test]
    public function catch_directory_livewire_component_renders_successfully()
    {
        $user = User::factory()->create();
        $this->be($user);

        Livewire::test(CatchDirectory::class)
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

        Livewire::test(CatchDirectory::class)
            ->set('search', 'Walleye Special')
            ->assertSee('Walleye Special')
            ->assertViewHas('records', function ($records) use ($walleye) {
                return $records->count() === 1 && $records->first()->fish_breeds_id === $walleye->id;
            });
    }

    #[Test]
    public function species_dropdown_filters_records_by_uuid_or_name()
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

        // 1. Filter by species UUID
        Livewire::test(CatchDirectory::class)
            ->set('speciesId', $pike->id)
            ->assertViewHas('records', function ($records) use ($pike) {
                return $records->count() === 1 && $records->first()->fish_breeds_id === $pike->id;
            });

        // 2. Filter by species name string
        Livewire::test(CatchDirectory::class)
            ->set('speciesId', 'Northern Pike')
            ->assertViewHas('records', function ($records) use ($pike) {
                return $records->count() === 1 && $records->first()->fish_breeds_id === $pike->id;
            });
    }
}
