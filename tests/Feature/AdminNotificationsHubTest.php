<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Fishinglog\Notifications\InvitedUserRegistered;
use Fishinglog\Notifications\TrophyCatchLogged;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminNotificationsHubTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function admin_hub_renders_polymorphic_notifications_for_registrations_and_milestones()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $newUser = User::factory()->create(['name' => 'Charlie Angler', 'type' => User::DEFAULT_TYPE]);

        $angler = Angler::factory()->create(['user_id' => $admin->id, 'firstName' => 'Admin', 'lastName' => 'Boss']);
        $lake = Lake::factory()->create(['name' => 'Lac des Mille Lacs']);
        $breed = FishBreed::factory()->create(['name' => 'Lake Trout']);

        $record = Record::create([
            'anglers_id' => $angler->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'length' => 39.5,
            'caught' => now(),
        ]);

        $trophyNotification = new TrophyCatchLogged($record, [
            'type' => 'all_time_record',
            'title' => '🌟 All-Time Logbook Record Lake Trout!',
            'previous_length' => 36.0,
        ]);

        // Send both registration and trophy notifications
        $admin->notify(new InvitedUserRegistered($newUser));
        $admin->notify($trophyNotification);

        $response = $this->actingAs($admin->fresh())->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Notifications & Activity Alerts');
        $response->assertSee('All-Time Record');
        $response->assertSee('Registration');
        $response->assertSee('Charlie Angler');
        $response->assertSee('Lac des Mille Lacs');
        $response->assertSee('View Catch →');
        $response->assertSee('Pair Profile →');
        $response->assertSee('Dismiss All');
    }

    #[Test]
    public function admin_can_dismiss_single_notification()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $newUser = User::factory()->create(['name' => 'Dan Hunter', 'type' => User::DEFAULT_TYPE]);

        $admin->notify(new InvitedUserRegistered($newUser));
        $notification = $admin->fresh()->unreadNotifications->first();
        $this->assertNotNull($notification);

        $response = $this->actingAs($admin)->post("/admin/notifications/{$notification->id}/mark-read");
        $response->assertRedirect();

        $this->assertEquals(0, $admin->fresh()->unreadNotifications->count());
    }

    #[Test]
    public function admin_can_dismiss_all_notifications()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $user1 = User::factory()->create(['name' => 'Angler One', 'type' => User::DEFAULT_TYPE]);
        $user2 = User::factory()->create(['name' => 'Angler Two', 'type' => User::DEFAULT_TYPE]);

        $admin->notify(new InvitedUserRegistered($user1));
        $admin->notify(new InvitedUserRegistered($user2));
        $this->assertEquals(2, $admin->fresh()->unreadNotifications->count());

        $response = $this->actingAs($admin)->post('/admin/notifications/mark-all-read');
        $response->assertRedirect();

        $this->assertEquals(0, $admin->fresh()->unreadNotifications->count());
    }
}
