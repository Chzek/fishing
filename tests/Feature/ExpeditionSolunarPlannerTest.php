<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExpeditionSolunarPlannerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_expedition_show_renders_solunar_trip_planner_and_feeding_outlook(): void
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create([
            'name' => 'Wawa Wilderness Lake',
            'latitude' => 47.9944,
            'longitude' => -84.7619,
        ]);
        $breed = FishBreed::factory()->create(['name' => 'Northern Pike']);

        $expedition = Expedition::create([
            'description' => 'Great Northern Expedition 2026',
            'start' => '2026-07-10',
            'finish' => '2026-07-16',
        ]);

        Record::create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'length' => 38.0,
            'weight' => 14.5,
            'released' => 1,
            'caught' => '2026-07-12',
            'latitude' => 47.9944,
            'longitude' => -84.7619,
        ]);

        $response = $this->actingAs($user)->get("/expedition/{$expedition->id}");

        $response->assertStatus(200);
        $response->assertSee('Moon Phase');
        $response->assertSee('Trip Feeding Outlook');
        $response->assertSee('Major Feed');
        $response->assertSee('Minor Feed');
        $response->assertSee('Wawa Wilderness Lake');
    }
}
