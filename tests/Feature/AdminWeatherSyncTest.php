<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AdminWeatherSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_weather_sync_console_with_pending_count()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $lake = Lake::factory()->create();
        $angler = Angler::factory()->create();

        Record::factory()->create([
            'lakes_id' => $lake->id,
            'anglers_id' => $angler->id,
            'caught' => '2026-06-15',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Weather Telemetry Sync Engine');
        $response->assertSee('Pending Weather Sync:');
        $response->assertSee('Issue Weather Sync Now');
    }

    public function test_admin_can_trigger_weather_sync()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        Artisan::shouldReceive('call')
            ->once()
            ->with('weather:sync')
            ->andReturn(0);

        $response = $this->actingAs($admin)->post('/admin/weather/sync');

        $response->assertRedirect('/admin');
        $response->assertSessionHas('status', 'Weather Telemetry Sync triggered successfully!');
    }

    public function test_non_admin_cannot_trigger_weather_sync()
    {
        $user = User::factory()->create(['type' => User::DEFAULT_TYPE]);

        $response = $this->actingAs($user)->post('/admin/weather/sync');

        $response->assertStatus(302);
    }
}
