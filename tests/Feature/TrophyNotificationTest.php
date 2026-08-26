<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Notifications\TrophyCatchLogged;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TrophyNotificationTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function standard_catch_without_record_does_not_trigger_trophy_notification()
    {
        Notification::fake();

        $angler = Angler::factory()->create();
        $user = $angler->user;
        $breed = FishBreed::factory()->create(['name' => 'Walleye']);
        $lake = Lake::factory()->create(['name' => 'Black Lake']);

        // First catch (15")
        Record::create([
            'anglers_id' => $angler->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'length' => 15.00,
            'caught' => now(),
        ]);

        // Second catch shorter than PB (12")
        $record = new Record([
            'anglers_id' => $angler->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'length' => 12.00,
            'caught' => now(),
        ]);

        $milestone = $record->checkTrophyMilestone();
        $this->assertNull($milestone);
    }

    #[Test]
    public function breaking_personal_best_triggers_trophy_milestone()
    {
        Notification::fake();

        $angler = Angler::factory()->create();
        $breed = FishBreed::factory()->create(['name' => 'Smallmouth Bass']);
        $lake = Lake::factory()->create(['name' => 'Indian Lake']);

        // Initial PB (16")
        Record::create([
            'anglers_id' => $angler->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'length' => 16.00,
            'caught' => now()->subDay(),
        ]);

        // New PB catch (19.5")
        $newPbRecord = Record::create([
            'anglers_id' => $angler->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'length' => 19.50,
            'caught' => now(),
        ]);

        $milestone = $newPbRecord->checkTrophyMilestone();

        $this->assertNotNull($milestone);
        $this->assertEquals('species_pb', $milestone['type']);
        $this->assertEquals(16.00, $milestone['previous_length']);
    }
}
