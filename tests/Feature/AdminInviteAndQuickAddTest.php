<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminInviteAndQuickAddTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function public_register_route_is_disabled()
    {
        $response = $this->get('/register');
        $response->assertStatus(404);
    }

    #[Test]
    public function admin_can_generate_signed_invite_link_and_invited_user_can_register()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        // 1. Admin generates invite
        $inviteResponse = $this->actingAs($admin)->post('/admin/users/invite', [
            'name' => 'Invited Angler',
            'email' => 'invited@example.com',
        ]);

        $inviteResponse->assertRedirect(route('admin.users'));
        $inviteResponse->assertSessionHas('status');

        // 2. Generate valid signed URL
        $signedUrl = URL::temporarySignedRoute(
            'register.invited',
            now()->addDays(7),
            ['email' => 'invited@example.com', 'name' => 'Invited Angler']
        );

        $getFormResponse = $this->get($signedUrl);
        $getFormResponse->assertStatus(200);
        $getFormResponse->assertSee('Complete Account Setup');

        // 3. Complete invited registration
        $postResponse = $this->post($signedUrl, [
            'name' => 'Invited Angler',
            'email' => 'invited@example.com',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
        ]);

        $postResponse->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', ['email' => 'invited@example.com']);
        $this->assertDatabaseHas('anglers', ['firstName' => 'Invited Angler']);
    }

    #[Test]
    public function admin_can_create_angler_account_via_canada_offline_quick_add()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        $response = $this->actingAs($admin)->post('/admin/users/quick-add', [
            'name' => 'Canada Angler',
            'email' => 'canada@example.com',
            'password' => 'fieldpass123',
        ]);

        $response->assertRedirect(route('admin.users'));
        $response->assertSessionHas('status');

        $user = User::where('email', 'canada@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('pending_upstream', $user->sync_status);
        $this->assertNotEmpty($user->id);

        $angler = Angler::where('user_id', $user->id)->first();
        $this->assertNotNull($angler);
        $this->assertEquals('Canada', $angler->firstName);
        $this->assertEquals('pending_upstream', $angler->sync_status);
    }
}
