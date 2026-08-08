<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserAnglerLinkTest extends TestCase
{
    use RefreshDatabase;

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
}
