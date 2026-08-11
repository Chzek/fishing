<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordSummaryDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function unauthenticated_user_cannot_access_catches_summary()
    {
        $response = $this->get('/record');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function authenticated_user_can_view_catches_summary_dashboard_with_telemetry()
    {
        $angler = Angler::factory()->create(['firstName' => 'John', 'lastName' => 'Doe']);
        $lake = Lake::factory()->create(['name' => 'Wawa Lake']);
        $breed = FishBreed::factory()->create(['name' => 'Walleye']);

        Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'length' => 24.5,
            'weight' => 5.2,
            'released' => 1,
            'caught' => '2026-06-15',
        ]);

        $response = $this->actingAs($this->user)->get('/record');

        $response->assertStatus(200);
        $response->assertSee('Catches Logbook');
        $response->assertSee('Lifetime Production');
        $response->assertSee('Top 5 Angler Producers');
        $response->assertSee('Top 5 Waterbodies');
        $response->assertSee('Macro Species Target Shifts');
        $response->assertSee('Weather Condition Distribution & Top Producing Lakes', false);
        $response->assertSee('Wawa Lake');
        $response->assertSee('Walleye');
        $response->assertSee('Open Logbook Directory');
    }
}
