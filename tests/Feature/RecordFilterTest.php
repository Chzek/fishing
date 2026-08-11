<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_records_by_species_name_and_length()
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create(['name' => 'Wawa Lake']);
        $walleye = FishBreed::factory()->create(['name' => 'Walleye']);
        $pike = FishBreed::factory()->create(['name' => 'Northern Pike']);

        // Create 2 records
        $record1 = Record::create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $walleye->id,
            'length' => 24.5,
            'weight' => 4.5,
            'caught' => '2026-07-10',
        ]);

        $record2 = Record::create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $pike->id,
            'length' => 15.0,
            'weight' => 2.0,
            'caught' => '2026-07-11',
        ]);

        // Redirect test for search on /record
        $redirectResponse = $this->actingAs($user)->get('/record?search=Walleye&length=20&length_operator=>');
        $redirectResponse->assertRedirect('/record/directory?search=Walleye&length=20&length_operator=%3E');

        // Search directly on /record/directory
        $response = $this->actingAs($user)->get('/record/directory?search=Walleye&length=20&length_operator=>');

        $response->assertStatus(200);
        $response->assertSee('Walleye');
        $response->assertDontSee('Northern Pike');
    }
}
