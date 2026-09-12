<?php

namespace Tests\Feature\Livewire;

use Fishinglog\Livewire\Widgets\SolunarForecast;
use Fishinglog\Models\Lake;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class SolunarForecastTest extends TestCase
{
    use DatabaseTransactions;

    public function test_solunar_widget_mounts_with_defaults(): void
    {
        Livewire::test(SolunarForecast::class)
            ->assertStatus(200)
            ->assertSee('Solunar')
            ->assertSee('Feeding Forecast')
            ->assertSee('Major Windows')
            ->assertSee('Minor Windows')
            ->assertSee('24-Hour Solunar Feeding Activity Ribbon');
    }

    public function test_solunar_widget_binds_to_specific_lake(): void
    {
        $lake = Lake::create([
            'name' => 'Wawa Lake',
            'latitude' => 47.9944,
            'longitude' => -84.7619,
        ]);

        Livewire::test(SolunarForecast::class, ['lakeId' => $lake->id])
            ->assertStatus(200)
            ->assertSee('Wawa Lake')
            ->assertSet('lakeName', 'Wawa Lake')
            ->assertSet('latitude', 47.9944)
            ->assertSet('longitude', -84.7619);
    }

    public function test_solunar_widget_handles_date_navigation(): void
    {
        $initialDate = '2026-07-15';

        Livewire::test(SolunarForecast::class, ['date' => $initialDate])
            ->assertSet('date', '2026-07-15')
            ->call('nextDay')
            ->assertSet('date', '2026-07-16')
            ->call('previousDay')
            ->assertSet('date', '2026-07-15')
            ->call('setDate', '2026-08-01')
            ->assertSet('date', '2026-08-01');
    }

    public function test_solunar_widget_can_toggle_collapsed_state(): void
    {
        Livewire::test(SolunarForecast::class)
            ->assertSet('collapsed', false)
            ->call('toggleCollapsed')
            ->assertSet('collapsed', true)
            ->call('toggleCollapsed')
            ->assertSet('collapsed', false);
    }

    public function test_lake_show_page_renders_solunar_widget(): void
    {
        $user = User::factory()->create();
        $lake = Lake::create([
            'name' => 'Hawk Lake',
            'latitude' => 48.1500,
            'longitude' => -84.8500,
        ]);

        $response = $this->actingAs($user)->get('/lake/' . $lake->id);

        $response->assertStatus(200);
        $response->assertSee('Solunar');
        $response->assertSee('Feeding Forecast');
        $response->assertSee('Hawk Lake');
    }
}
