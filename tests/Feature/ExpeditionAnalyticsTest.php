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

    public function test_expedition_analytics_service_calculates_distinct_anglers_correctly()
    {
        $angler1 = Angler::factory()->create(['firstName' => 'John', 'lastName' => 'Doe']);
        $angler2 = Angler::factory()->create(['firstName' => 'Jane', 'lastName' => 'Smith']);
        $angler3 = Angler::factory()->create(['firstName' => 'Guest', 'lastName' => 'Fisher']);
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create();

        $expedition = Expedition::create([
            'description' => 'Boundary Waters 2026',
            'start' => '2026-06-01',
            'finish' => '2026-06-05',
        ]);

        // Crew: Angler 1 and Angler 2
        \Fishinglog\Models\Crew::create([
            'expeditions_id' => $expedition->id,
            'anglers_id' => $angler1->id,
        ]);
        \Fishinglog\Models\Crew::create([
            'expeditions_id' => $expedition->id,
            'anglers_id' => $angler2->id,
        ]);

        // Catches: Angler 1 (roster) and Angler 3 (catching during dates)
        Record::create([
            'anglers_id' => $angler1->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'length' => 20.0,
            'caught' => '2026-06-02',
        ]);
        Record::create([
            'anglers_id' => $angler3->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'length' => 24.5,
            'caught' => '2026-06-03',
        ]);

        $service = app(\Fishinglog\Services\ExpeditionAnalyticsService::class);
        $analytics = $service->getAnalytics($expedition);

        $this->assertEquals(3, $analytics['totalAnglersCount']);
        $this->assertEquals(3, $analytics['totalUniqueAnglersCount']);

        $leaderboard = $analytics['crewLeaderboard'];
        $this->assertCount(3, $leaderboard);

        // Angler 1: In roster + Has Catches
        $entry1 = $leaderboard->firstWhere('anglers_id', $angler1->id);
        $this->assertNotNull($entry1);
        $this->assertEquals(1, $entry1->total_catches);

        // Angler 3: Has Catches
        $entry3 = $leaderboard->firstWhere('anglers_id', $angler3->id);
        $this->assertNotNull($entry3);
        $this->assertEquals(1, $entry3->total_catches);

        // Angler 2: In roster, 0 catches
        $entry2 = $leaderboard->firstWhere('anglers_id', $angler2->id);
        $this->assertNotNull($entry2);
        $this->assertEquals(0, $entry2->total_catches);

        // Model accessors
        $this->assertEquals(3, $expedition->anglers_count);
        $this->assertCount(3, $expedition->anglers);
        $this->assertTrue($expedition->angler_ids->contains($angler1->id));
        $this->assertTrue($expedition->angler_ids->contains($angler2->id));
        $this->assertTrue($expedition->angler_ids->contains($angler3->id));
    }

    public function test_expedition_show_renders_distinct_anglers_count()
    {
        $user = User::factory()->create();
        $anglerRoster = Angler::factory()->create(['firstName' => 'Captain', 'lastName' => 'Morgan']);
        $anglerGuest = Angler::factory()->create(['firstName' => 'Guest', 'lastName' => 'Angler']);
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create();

        $expedition = Expedition::create([
            'description' => 'Lake Nipissing 2026',
            'start' => '2026-07-10',
            'finish' => '2026-07-12',
        ]);

        \Fishinglog\Models\Crew::create([
            'expeditions_id' => $expedition->id,
            'anglers_id' => $anglerRoster->id,
        ]);

        Record::create([
            'anglers_id' => $anglerGuest->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'length' => 18.0,
            'caught' => '2026-07-11',
        ]);

        $response = $this->actingAs($user)->get("/expedition/{$expedition->id}");
        $response->assertStatus(200);
        $response->assertSee('2 Anglers');
        $response->assertSee('Captain Morgan');
        $response->assertSee('Guest Angler');
    }
}
