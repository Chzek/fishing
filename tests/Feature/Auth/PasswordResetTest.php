<?php

namespace Tests\Feature\Auth;

use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/password/reset');

        $response->assertStatus(200);
        $response->assertSee('Reset Password');
        $response->assertSee('Send Reset Link');
    }

    #[Test]
    public function reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'angler@example.com',
        ]);

        $response = $this->post('/password/email', [
            'email' => $user->email,
        ]);

        $response->assertSessionHas('status');
        $response->assertRedirect();
    }

    #[Test]
    public function reset_password_screen_can_be_rendered_with_token(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->get("/password/reset/{$token}?email=" . urlencode($user->email));

        $response->assertStatus(200);
        $response->assertSee('Set New Password');
        $response->assertSee($user->email);
    }

    #[Test]
    public function password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-secret-password'),
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-secret-password123',
            'password_confirmation' => 'new-secret-password123',
        ]);

        $response->assertRedirect('/profile');
        $this->assertTrue(Hash::check('new-secret-password123', $user->fresh()->password));
    }

    #[Test]
    public function password_cannot_be_reset_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-secret-password'),
        ]);

        $response = $this->from('/password/reset/invalid-token')->post('/password/reset', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'new-secret-password123',
            'password_confirmation' => 'new-secret-password123',
        ]);

        $response->assertRedirect('/password/reset/invalid-token');
        $this->assertTrue(Hash::check('old-secret-password', $user->fresh()->password));
    }
}
