<?php

namespace Tests\Feature;

use Fishinglog\Models\Lake;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfflineMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_offline_map_downloader_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/map/offline');

        $response->assertStatus(200);
        $response->assertSee('Offline Map Region Downloader');
        $response->assertSee('Wawa, Hawk Junction & White River Region', false);
    }

    public function test_can_create_lake_with_coordinates_structure_and_max_depth(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/lake', [
            'name' => 'Wawa Lake',
            'latitude' => 47.9942,
            'longitude' => -84.7612,
            'structure' => 'Rock/Granite, Drop-off',
            'max_depth' => 105,
        ]);

        $response->assertRedirect('/lake');

        $this->assertDatabaseHas('lakes', [
            'name' => 'Wawa Lake',
            'structure' => 'Rock/Granite, Drop-off',
            'max_depth' => 105,
        ]);

        $lake = Lake::where('name', 'Wawa Lake')->first();
        $this->assertEquals(47.9942, round($lake->latitude, 4));
        $this->assertEquals(-84.7612, round($lake->longitude, 4));
    }

    public function test_can_find_nearby_lakes_within_two_miles(): void
    {
        $targetLake = Lake::create([
            'name' => 'Wawa Lake',
            'latitude' => 47.9942,
            'longitude' => -84.7612,
        ]);

        $nearbyLake = Lake::create([
            'name' => 'Magpie Pond',
            'latitude' => 48.0010, // ~0.5 miles away
            'longitude' => -84.7650,
        ]);

        $farLake = Lake::create([
            'name' => 'White River Lake',
            'latitude' => 48.5900, // ~41 miles away
            'longitude' => -85.2800,
        ]);

        $nearby = Lake::nearby($targetLake->latitude, $targetLake->longitude, 2.0, $targetLake->id);

        $this->assertCount(1, $nearby);
        $this->assertEquals('Magpie Pond', $nearby->first()->name);
    }

    public function test_nearby_lakes_api_endpoint(): void
    {
        Lake::create([
            'name' => 'Hawk Lake',
            'latitude' => 48.1500,
            'longitude' => -84.8500,
        ]);

        $response = $this->getJson('/api/v1/lakes/nearby?lat=48.1510&lng=-84.8510&radius=2');

        $response->assertStatus(200);
        $response->assertJsonPath('count', 1);
        $response->assertJsonPath('data.0.name', 'Hawk Lake');
    }
}
