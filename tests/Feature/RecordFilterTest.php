<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RecordFilterTest extends TestCase
{
    use DatabaseTransactions;

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
        $response->assertSee('24.5');
    }

    public function test_can_filter_directory_by_species_id_and_species_name()
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create(['name' => 'Davies Lake']);
        $walleye = FishBreed::factory()->create(['name' => 'Walleye']);
        $pike = FishBreed::factory()->create(['name' => 'Northern Pike']);

        Record::create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $walleye->id,
            'length' => 19.5,
            'caught' => '2026-08-01',
        ]);

        Record::create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $pike->id,
            'length' => 27.0,
            'caught' => '2026-08-02',
        ]);

        // Filter by species ID
        $responseById = $this->actingAs($user)->get('/record/directory?species=' . $pike->id);
        $responseById->assertStatus(200);
        $responseById->assertSee('27.0');
        $responseById->assertDontSee('19.5');

        // Filter by species name
        $responseByName = $this->actingAs($user)->get('/record/directory?species=Northern Pike');
        $responseByName->assertStatus(200);
        $responseByName->assertSee('27.0');
        $responseByName->assertDontSee('19.5');
    }

    public function test_can_multi_sort_records_by_lake_and_species()
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create(['name' => 'Davies Lake']);
        $walleye = FishBreed::factory()->create(['name' => 'Walleye']);
        $pike = FishBreed::factory()->create(['name' => 'Northern Pike']);

        // Create records on Davies Lake with different species and catch dates
        Record::create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $walleye->id,
            'length' => 20.0,
            'caught' => '2026-08-01',
        ]);

        Record::create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $pike->id,
            'length' => 25.0,
            'caught' => '2026-08-02',
        ]);

        // Multi-sort by lake asc, species asc
        $response = $this->actingAs($user)->get('/record/directory?sort_by=lake,species&sort_order=asc,asc');
        $response->assertStatus(200);

        // Northern Pike ('N') must come before Walleye ('W') for Davies Lake
        $response->assertSeeInOrder(['Northern Pike', 'Walleye']);
    }

    public function test_can_sort_anglers_by_summary_columns_and_full_name()
    {
        $user = User::factory()->create();

        $angler1 = Angler::factory()->create(['firstName' => 'John', 'lastName' => 'Smith', 'middleName' => 'A']);
        $angler2 = Angler::factory()->create(['firstName' => 'Adam', 'lastName' => 'Brown', 'middleName' => 'B']);

        $response = $this->actingAs($user)->get('/angler?sort_by=angler&sort_order=asc');
        $response->assertStatus(200);

        // Brown comes before Smith
        $response->assertSeeInOrder(['Brown', 'Smith']);

        $responseSummary = $this->actingAs($user)->get('/angler?sort_by=catches&sort_order=desc');
        $responseSummary->assertStatus(200);

        $responseLakes = $this->actingAs($user)->get('/angler?sort_by=lakes&sort_order=desc');
        $responseLakes->assertStatus(200);
    }
}
