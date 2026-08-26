<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminUserAnglerLinkTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_view_users_list_and_link_angler()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $user = User::factory()->create(['type' => User::DEFAULT_TYPE]);
        $angler = Angler::factory()->create();

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSeeText('User Account');
        $response->assertSeeText('Angler Profile Management');

        $linkResponse = $this->actingAs($admin)->post('/admin/users/link', [
            'user_id' => $user->id,
            'angler_id' => $angler->id,
        ]);

        $linkResponse->assertRedirect(route('admin.users'));

        $this->assertEquals($user->id, $angler->fresh()->user_id);
    }

    public function test_admin_can_toggle_admin_privileges()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $user = User::factory()->create(['type' => User::DEFAULT_TYPE]);

        $response = $this->actingAs($admin)->post("/admin/users/{$user->id}/toggle-admin");

        $response->assertRedirect(route('admin.users'));
        $this->assertTrue($user->fresh()->isAdmin());
    }

    public function test_non_admin_cannot_access_admin_user_management()
    {
        $user = User::factory()->create(['type' => User::DEFAULT_TYPE]);

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertStatus(302);
    }

    public function test_admin_can_delete_user_account()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $user = User::factory()->create(['type' => User::DEFAULT_TYPE]);
        $angler = Angler::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)->delete("/admin/users/{$user->id}");

        $response->assertRedirect(route('admin.users'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertNull($angler->fresh()->user_id);
    }

    public function test_admin_can_manually_verify_user_email()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $unverifiedUser = User::factory()->create([
            'type' => User::DEFAULT_TYPE,
            'email_verified_at' => null,
        ]);

        $this->assertFalse($unverifiedUser->isRegistered());

        $response = $this->actingAs($admin)->post("/admin/users/{$unverifiedUser->id}/verify");

        $response->assertRedirect(route('admin.users'));
        $this->assertTrue($unverifiedUser->fresh()->isRegistered());
    }

    public function test_admin_sees_notification_and_linking_marks_it_read()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $user = User::factory()->create(['type' => User::DEFAULT_TYPE, 'name' => 'Stanley']);
        $angler = Angler::factory()->create();

        // Send registration notification to admin
        $admin->notify(new \Fishinglog\Notifications\InvitedUserRegistered($user));

        $this->assertEquals(1, $admin->fresh()->unreadNotifications->count());

        // Admin views overview page and sees notification alert
        $overviewResponse = $this->actingAs($admin)->get('/admin');
        $overviewResponse->assertStatus(200);
        $overviewResponse->assertSee('New User Registration Alert');
        $overviewResponse->assertSee('Stanley');

        // Admin links angler to user
        $linkResponse = $this->actingAs($admin)->post('/admin/users/link', [
            'user_id' => $user->id,
            'angler_id' => $angler->id,
        ]);

        $linkResponse->assertRedirect(route('admin.users'));

        // Notification should be automatically marked as read
        $this->assertEquals(0, $admin->fresh()->unreadNotifications->count());
    }

    public function test_admin_can_dismiss_notifications()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $user = User::factory()->create(['type' => User::DEFAULT_TYPE, 'name' => 'Stanley']);

        $admin->notify(new \Fishinglog\Notifications\InvitedUserRegistered($user));
        $this->assertEquals(1, $admin->fresh()->unreadNotifications->count());

        $dismissResponse = $this->actingAs($admin)->post('/admin/notifications/mark-all-read');
        $dismissResponse->assertRedirect();

        $this->assertEquals(0, $admin->fresh()->unreadNotifications->count());
    }
}
