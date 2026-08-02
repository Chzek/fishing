<?php

namespace Tests\Feature;

use Fishinglog\Models\Lake;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class OfflineMapTest extends TestCase
{
    use DatabaseMigrations;

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
            'latitude' => 47.9942,
            'longitude' => -84.7612,
            'structure' => 'Rock/Granite, Drop-off',
            'max_depth' => 105,
        ]);
    }
}
