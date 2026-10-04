<?php

namespace Tests\Feature;

use Fishinglog\Events\CatchLoggedEvent;
use Fishinglog\Listeners\CheckTrophyMilestoneListener;
use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
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

        $angler1 = Angler::factory()->create();
        $angler2 = Angler::factory()->create();
        $breed = FishBreed::factory()->create(['name' => 'Smallmouth Bass']);
        $lake = Lake::factory()->create(['name' => 'Indian Lake']);

        // Another angler has a larger catch on this lake (22") so angler1's 19.5" is neither lake nor all-time record
        Record::create([
            'anglers_id' => $angler2->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'length' => 22.00,
            'caught' => now()->subDays(10),
        ]);

        // Angler1's initial catch (16")
        Record::create([
            'anglers_id' => $angler1->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'length' => 16.00,
            'caught' => now()->subDay(),
        ]);

        // Angler1's new PB catch (19.5")
        $newPbRecord = Record::create([
            'anglers_id' => $angler1->id,
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

    #[Test]
    public function all_time_fishery_record_triggers_highest_priority_milestone()
    {
        $angler1 = Angler::factory()->create();
        $angler2 = Angler::factory()->create();
        $breed = FishBreed::factory()->create(['name' => 'Muskie']);
        $lake1 = Lake::factory()->create(['name' => 'Lake of the Woods']);
        $lake2 = Lake::factory()->create(['name' => 'Eagle Lake']);

        // Existing catches in the entire logbook
        Record::create([
            'anglers_id' => $angler1->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake1->id,
            'length' => 45.00,
            'caught' => now()->subMonths(2),
        ]);

        Record::create([
            'anglers_id' => $angler2->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake2->id,
            'length' => 50.00,
            'caught' => now()->subMonth(),
        ]);

        // New monster catch beating all records across all lakes (54.5")
        $allTimeRecord = Record::create([
            'anglers_id' => $angler1->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake1->id,
            'length' => 54.50,
            'caught' => now(),
        ]);

        $milestone = $allTimeRecord->checkTrophyMilestone();

        $this->assertNotNull($milestone);
        $this->assertEquals('all_time_record', $milestone['type']);
        $this->assertEquals(50.00, $milestone['previous_length']);
        $this->assertStringContainsString('All-Time Logbook Record', $milestone['title']);
    }

    #[Test]
    public function lake_record_triggers_when_beating_lake_max_but_not_global_max()
    {
        $angler1 = Angler::factory()->create();
        $angler2 = Angler::factory()->create();
        $breed = FishBreed::factory()->create(['name' => 'Northern Pike']);
        $lakeA = Lake::factory()->create(['name' => 'Hawk Lake']);
        $lakeB = Lake::factory()->create(['name' => 'Lac Seul']);

        // Lake B has a global record of 42"
        Record::create([
            'anglers_id' => $angler2->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lakeB->id,
            'length' => 42.00,
            'caught' => now()->subMonths(3),
        ]);

        // Lake A's previous max was 32"
        Record::create([
            'anglers_id' => $angler2->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lakeA->id,
            'length' => 32.00,
            'caught' => now()->subMonths(2),
        ]);

        // Angler 1 catches 38" on Lake A (New Lake Record for Lake A, but not all-time global 42")
        $lakeRecord = Record::create([
            'anglers_id' => $angler1->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lakeA->id,
            'length' => 38.00,
            'caught' => now(),
        ]);

        $milestone = $lakeRecord->checkTrophyMilestone();

        $this->assertNotNull($milestone);
        $this->assertEquals('lake_record', $milestone['type']);
        $this->assertEquals(32.00, $milestone['previous_length']);
        $this->assertStringContainsString('Lake Record', $milestone['title']);
        $this->assertStringContainsString('Hawk Lake', $milestone['title']);
    }

    #[Test]
    public function first_species_catch_triggers_milestone_when_no_prior_catches_exist()
    {
        $angler = Angler::factory()->create();
        $breed = FishBreed::factory()->create(['name' => 'Brook Trout']);
        $lake = Lake::factory()->create(['name' => 'Nipigon River']);

        // First catch of this species for this angler
        $firstCatch = Record::create([
            'anglers_id' => $angler->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'length' => 14.50,
            'caught' => now(),
        ]);

        $milestone = $firstCatch->checkTrophyMilestone();

        $this->assertNotNull($milestone);
        $this->assertEquals('first_species_catch', $milestone['type']);
        $this->assertNull($milestone['previous_length']);
        $this->assertStringContainsString('First Logged', $milestone['title']);
    }

    #[Test]
    public function trophy_notification_generates_personalized_messages()
    {
        $user = User::factory()->create(['name' => 'Greg']);
        $admin = User::factory()->create(['name' => 'Admin User', 'type' => User::ADMIN_TYPE]);
        $angler = Angler::factory()->create(['user_id' => $user->id, 'firstName' => 'Greg', 'lastName' => 'Mroczek']);
        $lake = Lake::factory()->create(['name' => 'Lake Simcoe']);
        $breed = FishBreed::factory()->create(['name' => 'Yellow Perch']);

        $record = Record::create([
            'anglers_id' => $angler->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'length' => 14.25,
            'caught' => now(),
        ]);

        $milestone = [
            'type' => 'all_time_record',
            'title' => 'New All-Time Logbook Record!',
            'previous_length' => 13.50,
        ];

        $notification = new TrophyCatchLogged($record, $milestone);

        $userPayload = $notification->toDatabase($user);
        $adminPayload = $notification->toDatabase($admin);

        // Angler sees personalized "You caught a new All-Time Logbook Record"
        $this->assertStringContainsString('You caught a new', $userPayload['message']);
        $this->assertStringContainsString('14.25"', $userPayload['message']);

        // Admin sees "Greg F. Mroczek caught a new All-Time Logbook Record"
        $this->assertStringContainsString('caught a new', $adminPayload['message']);
        $this->assertStringContainsString('14.25"', $adminPayload['message']);
        $this->assertEquals('trophy_catch', $adminPayload['type']);
        $this->assertEquals('all_time_record', $adminPayload['milestone_type']);
    }

    #[Test]
    public function trophy_listener_notifies_both_catching_angler_and_system_administrators()
    {
        Notification::fake();

        $admin1 = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $admin2 = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $otherUser = User::factory()->create(['type' => User::DEFAULT_TYPE]);

        $anglerUser = User::factory()->create(['type' => User::DEFAULT_TYPE]);
        $angler = Angler::factory()->create(['user_id' => $anglerUser->id, 'firstName' => 'John', 'lastName' => 'Doe']);
        $lake = Lake::factory()->create(['name' => 'Lake Huron']);
        $breed = FishBreed::factory()->create(['name' => 'Chinook Salmon']);

        // Previous baseline catch (30.0")
        Record::create([
            'anglers_id' => $angler->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'length' => 30.00,
            'caught' => now()->subDays(5),
        ]);

        // Monster new catch breaking all-time logbook record (36.0")
        $record = Record::create([
            'anglers_id' => $angler->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'length' => 36.00,
            'caught' => now(),
        ]);

        $event = new CatchLoggedEvent($record);
        $listener = new CheckTrophyMilestoneListener();
        $listener->handle($event);

        // Angler user should receive notification
        Notification::assertSentTo($anglerUser, TrophyCatchLogged::class);

        // Admin users should receive notification
        Notification::assertSentTo($admin1, TrophyCatchLogged::class);
        Notification::assertSentTo($admin2, TrophyCatchLogged::class);

        // Unrelated non-admin users should not receive notification
        Notification::assertNotSentTo($otherUser, TrophyCatchLogged::class);
    }
}
