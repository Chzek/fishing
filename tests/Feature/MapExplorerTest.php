<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MapExplorerTest extends TestCase
{
    use DatabaseMigrations;

    public function test_authenticated_user_can_access_map_explorer_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/map/explorer');

        $response->assertStatus(200);
        $response->assertSee('Lake Explorer');
    }

    public function test_explorer_lakes_api_returns_lakes_in_bounding_box_with_filters(): void
    {
        $family = FishFamily::create(['name' => 'Percidae']);
        $walleye = FishBreed::create(['name' => 'Walleye', 'fish_families_id' => $family->id]);
        $angler = Angler::create(['firstName' => 'Geren', 'middleName' => '', 'lastName' => 'Mroczek']);

        $wawaLake = Lake::create([
            'name' => 'Wawa Lake',
            'latitude' => 48.0000,
            'longitude' => -84.7600,
        ]);

        Record::create([
            'anglers_id' => $angler->id,
            'lakes_id' => $wawaLake->id,
            'fish_breeds_id' => $walleye->id,
            'length' => 22.5,
            'weight' => 4.5,
            'caught' => '2026-06-15',
        ]);

        $response = $this->getJson('/api/v1/explorer/lakes?min_lat=47.5&max_lat=48.5&min_lng=-85.0&max_lng=-84.0&fish_breed_id=' . $walleye->id);

        $response->assertStatus(200);
        $response->assertJsonPath('count', 1);
        $response->assertJsonPath('data.0.name', 'Wawa Lake');
    }

    public function test_explorer_lake_detail_api_returns_catch_analytics(): void
    {
        $family = FishFamily::create(['name' => 'Esocidae']);
        $pike = FishBreed::create(['name' => 'Northern Pike', 'fish_families_id' => $family->id]);
        $angler = Angler::create(['firstName' => 'Geren', 'middleName' => '', 'lastName' => 'Mroczek']);
        $lure = Lure::create(['name' => 'Rapala Jointed Shad', 'color' => 'Firetiger', 'size' => '7cm']);

        $hawkLake = Lake::create([
            'name' => 'Hawk Lake',
            'latitude' => 48.1500,
            'longitude' => -84.8500,
        ]);

        Record::create([
            'anglers_id' => $angler->id,
            'lakes_id' => $hawkLake->id,
            'fish_breeds_id' => $pike->id,
            'lures_id' => $lure->id,
            'length' => 36.0,
            'weight' => 12.5,
            'caught' => '2026-07-04',
        ]);

        $response = $this->getJson('/api/v1/explorer/lake/' . $hawkLake->id);

        $response->assertStatus(200);
        $response->assertJsonPath('total_catches', 1);
        $this->assertEquals(36.0, (float)$response->json('longest_catch.length'));
        $this->assertEquals(12.5, (float)$response->json('fattest_catch.weight'));
        $response->assertJsonPath('species_breakdown.0.fish_breed.name', 'Northern Pike');
        $response->assertJsonPath('top_lures.0.lure.name', 'Rapala Jointed Shad');
    }
}
