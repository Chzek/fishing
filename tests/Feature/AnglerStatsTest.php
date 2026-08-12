<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnglerStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_angler_stats_page()
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create(['firstName' => 'John', 'lastName' => 'Doe']);
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create();

        Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'length' => 24.5,
            'weight' => 5.2,
            'released' => 1,
        ]);

        $response = $this->actingAs($user)->get('/angler/stats');

        $response->assertStatus(200);
        $response->assertSee('Angler Telemetry');
        $response->assertSee('John Doe');
        $response->assertSee('Conservation Rate');
    }

    public function test_unauthenticated_user_cannot_access_angler_stats()
    {
        $response = $this->get('/angler/stats');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}
