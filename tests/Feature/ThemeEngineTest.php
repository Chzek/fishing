<?php

namespace Tests\Feature;

use Fishinglog\Livewire\Ui\ThemeSwitcher;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ThemeEngineTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function users_have_default_system_theme_preference(): void
    {
        $newUser = User::factory()->create();
        $this->assertSame('system', $newUser->theme_preference);
    }

    #[Test]
    public function theme_switcher_renders_options_and_sets_theme_for_authenticated_user(): void
    {
        Livewire::actingAs($this->user)
            ->test(ThemeSwitcher::class)
            ->assertSee('System')
            ->assertSee('Light')
            ->assertSee('Dark')
            ->call('setTheme', 'dark')
            ->assertSet('theme', 'dark')
            ->assertDispatched('theme-changed', theme: 'dark');

        $this->user->refresh();
        $this->assertSame('dark', $this->user->theme_preference);
    }

    #[Test]
    public function theme_switcher_rejects_invalid_theme_names(): void
    {
        Livewire::actingAs($this->user)
            ->test(ThemeSwitcher::class)
            ->call('setTheme', 'neon-cyberpunk')
            ->assertSet('theme', 'system');

        $this->user->refresh();
        $this->assertSame('system', $this->user->theme_preference);
    }

    #[Test]
    public function profile_update_persists_theme_preference(): void
    {
        $response = $this->actingAs($this->user)->put('/profile', [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'theme_preference' => 'light',
        ]);

        $response->assertRedirect('/profile/edit');
        $response->assertSessionHas('status');

        $this->user->refresh();
        $this->assertSame('light', $this->user->theme_preference);
    }

    #[Test]
    public function layout_contains_zero_fouc_script_and_theme_switcher(): void
    {
        $response = $this->actingAs($this->user)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Zero-FOUC Theme Engine Initializer');
        $response->assertSee('Theme Preference');
    }
}
