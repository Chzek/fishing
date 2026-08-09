<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Fishinglog\Models\FishingZone;
use Fishinglog\Models\Lake;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FishingZoneTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $zone;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->zone = FishingZone::create([
            'code' => 'FMZ 7',
            'name' => 'FMZ 7 - Wawa & Algoma District',
            'province_state' => 'Ontario',
            'country' => 'Canada',
            'description' => 'Highway 17 corridor and Wawa region lakes.',
            'regulations_url' => 'https://www.ontario.ca/page/fisheries-management-zone-7-fmz-7',
        ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_fishing_zones()
    {
        $response = $this->get('/fishing-zone');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function authenticated_user_can_view_fishing_zones_index()
    {
        $response = $this->actingAs($this->user)->get('/fishing-zone');

        $response->assertStatus(200);
        $response->assertSee('Fisheries Management Zones');
        $response->assertSee('FMZ 7');
    }

    #[Test]
    public function authenticated_user_can_view_fishing_zone_details()
    {
        $lake = Lake::factory()->create([
            'name' => 'Wawa Lake',
            'fishing_zone_id' => $this->zone->id,
        ]);

        $response = $this->actingAs($this->user)->get('/fishing-zone/' . $this->zone->id);

        $response->assertStatus(200);
        $response->assertSee($this->zone->name);
        $response->assertSee('Wawa Lake');
    }

    #[Test]
    public function user_can_assign_fishing_zone_to_lake()
    {
        $response = $this->actingAs($this->user)->post('/lake', [
            'name' => 'Hawk Lake',
            'latitude' => 48.01,
            'longitude' => -84.75,
            'fishing_zone_id' => $this->zone->id,
        ]);

        $response->assertRedirect('/lake');
        $this->assertDatabaseHas('lakes', [
            'name' => 'Hawk Lake',
            'fishing_zone_id' => $this->zone->id,
        ]);
    }

    #[Test]
    public function it_auto_detects_fishing_zone_for_lake_coordinates()
    {
        $response = $this->actingAs($this->user)->post('/lake', [
            'name' => 'Auto Zone Lake',
            'latitude' => 48.0042,
            'longitude' => -84.7712,
        ]);

        $response->assertRedirect('/lake');
        $lake = Lake::where('name', 'Auto Zone Lake')->first();
        $this->assertNotNull($lake);
        $this->assertEquals($this->zone->id, $lake->fishing_zone_id);
    }

    #[Test]
    public function it_runs_lakes_sync_zones_artisan_command()
    {
        $lake = Lake::factory()->create([
            'name' => 'Command Sync Lake',
            'latitude' => 48.08,
            'longitude' => -84.74,
            'fishing_zone_id' => null,
        ]);

        $this->artisan('lakes:sync-zones --force')
            ->assertExitCode(0);

        $this->assertDatabaseHas('lakes', [
            'id' => $lake->id,
            'fishing_zone_id' => $this->zone->id,
        ]);
    }
}
