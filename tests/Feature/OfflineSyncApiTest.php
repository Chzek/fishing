<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfflineSyncApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_reference_data_for_offline_cache()
    {
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create();
        $lure = Lure::factory()->create();

        $response = $this->getJson('/api/v1/reference-data');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'anglers',
            'lakes',
            'fish_breeds',
            'lures',
            'expeditions',
        ]);
    }

    #[Test]
    public function it_can_store_catch_via_api_and_prevents_duplicate_client_id()
    {
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create();
        $clientId = 'test-uuid-12345-abcde';

        $payload = [
            'client_id' => $clientId,
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'length' => 21.5,
            'weight' => 4.8,
            'released' => 1,
            'caught' => '2026-08-01',
        ];

        // First submission -> Created
        $response1 = $this->postJson('/api/v1/records', $payload);
        $response1->assertSuccessful();
        $response1->assertJsonPath('status', 'created');

        $this->assertDatabaseHas('records', [
            'client_id' => $clientId,
            'length' => 21.5,
        ]);

        // Duplicate submission with same client_id -> Ignored safely
        $response2 = $this->postJson('/api/v1/records', $payload);
        $response2->assertSuccessful();
        $response2->assertJsonPath('status', 'duplicate_ignored');

        // Verify only 1 record exists in database
        $this->assertEquals(1, Record::where('client_id', $clientId)->count());
    }

    #[Test]
    public function it_can_access_quick_catch_web_route()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/record/quick');

        $response->assertStatus(200);
        $response->assertSee('Boat Quick Catch Log');
    }
}
