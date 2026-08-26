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

class ExpeditionAnalyticsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_expedition_show_displays_trip_brag_board_and_analytics()
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create(['name' => 'Walleye']);

        $expedition = Expedition::create([
            'description' => 'Annual Wilderness Voyage 2026',
            'start' => '2026-08-01',
            'finish' => '2026-08-07',
        ]);

        Record::create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'length' => 28.5,
            'weight' => 7.5,
            'released' => 1,
            'caught' => '2026-08-03',
            'latitude' => 48.1234,
            'longitude' => -84.5678,
        ]);

        $response = $this->actingAs($user)->get("/expedition/{$expedition->id}");

        $response->assertStatus(200);
        $response->assertSeeText('Annual Wilderness Voyage 2026');
        $response->assertSeeText('Lunker Legend');
        $response->assertSeeText('Heavyweight Champ');
        $response->assertSeeText('Top Rod MVP');
        $response->assertSeeText('Daily Catch Cadence');
        $response->assertSeeText('Species Breakdown');
        $response->assertSeeText('Trip Crew Leaderboard');
        $response->assertSeeText('Walleye');
    }
}
