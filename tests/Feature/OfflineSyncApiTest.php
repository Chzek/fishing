<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OfflineSyncApiTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_returns_enriched_reference_data_for_offline_cache(): void
    {
        $angler = Angler::factory()->create([
            'firstName' => 'John',
            'lastName' => 'Doe',
        ]);
        $lake = Lake::factory()->create(['name' => 'Wawa Lake']);
        $breed = FishBreed::factory()->create(['name' => 'Walleye']);
        $lure = Lure::factory()->create([
            'brand' => 'Rapala',
            'name' => 'Husky Jerk',
            'category' => 'Hard Baits',
            'color' => 'Silver Blue',
        ]);

        $response = $this->getJson('/api/v1/reference-data');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'anglers' => [
                '*' => ['id', 'firstName', 'middleName', 'lastName', 'full_name'],
            ],
            'lakes' => [
                '*' => ['id', 'name', 'latitude', 'longitude'],
            ],
            'fish_breeds' => [
                '*' => ['id', 'name', 'fish_families_id'],
            ],
            'lures' => [
                '*' => ['id', 'name', 'brand', 'category', 'color', 'size', 'depth_range'],
            ],
            'expeditions',
        ]);

        $this->assertTrue(collect($response->json('anglers'))->contains('full_name', $angler->full_name));
        $this->assertTrue(collect($response->json('lures'))->contains('brand', 'Rapala'));
    }

    #[Test]
    public function it_can_store_catch_via_api_and_prevents_duplicate_client_id(): void
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
        $this->assertSame(1, Record::where('client_id', $clientId)->count());
    }

    #[Test]
    public function it_renders_quick_catch_page_with_offline_components_and_rehydration(): void
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create(['user_id' => $user->id, 'firstName' => 'Mark', 'lastName' => 'Bowen']);
        $lake = Lake::factory()->create(['name' => 'Kabitotikwia Lake']);
        $breed = FishBreed::factory()->create(['name' => 'Northern Pike']);
        $lure = Lure::factory()->create(['name' => 'Red Eye Special']);

        $response = $this->actingAs($user)->get('/record/quick');

        $response->assertStatus(200);
        $response->assertSee('Boat Quick Catch Log');
        $response->assertSee('offlineLureSelector(');
        $response->assertSee('rehydrateOfflineSelects()');
        $response->assertSee('Kabitotikwia Lake');
        $response->assertSee('Northern Pike');
        $response->assertSee('Red Eye Special');
    }
}
