<?php

namespace Tests\Feature\Auth;

use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sign In');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/profile');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $response = $this->get('/profile');

        $response->assertRedirect('/login');
    }

    public function test_users_can_authenticate_with_remember_me(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
            'remember' => 'on',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/profile');

        $recallerCookie = null;
        foreach ($response->headers->getCookies() as $cookie) {
            if (str_starts_with($cookie->getName(), 'remember_web_')) {
                $recallerCookie = $cookie;
                break;
            }
        }

        $this->assertNotNull($recallerCookie, 'Remember Me cookie was not queued on login response');
        $this->assertNotNull($user->fresh()->remember_token, 'Remember token was not stored in database');
    }

    public function test_remember_me_cookie_persists_authentication_after_session_cleared(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
            'remember' => 'on',
        ]);

        $recallerCookie = null;
        foreach ($response->headers->getCookies() as $cookie) {
            if (str_starts_with($cookie->getName(), 'remember_web_')) {
                $recallerCookie = $cookie;
                break;
            }
        }

        $this->assertNotNull($recallerCookie);

        // Simulate closing the browser: flush in-memory session and flush authenticated user instance
        $this->flushSession();
        $this->app['auth']->forgetGuards();

        // Making a request with only the remember cookie should re-authenticate the user
        $followUp = $this->withUnencryptedCookie($recallerCookie->getName(), $recallerCookie->getValue())
            ->get('/profile');

        $followUp->assertOk();
        $this->assertAuthenticatedAs($user);
    }
}
