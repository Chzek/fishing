<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Fishinglog\Services\NasSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminNasSyncConsoleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_view_nas_sync_console_with_per_model_breakdown()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $admin->markSynced();

        Lake::create(['name' => 'Pending Lake 1', 'latitude' => 45.0, 'longitude' => -78.0]);
        Angler::create(['firstName' => 'Sam', 'middleName' => 'F', 'lastName' => 'Fisher', 'user_id' => $admin->id]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Synology NAS Two-Way Sync Engine');
        $response->assertSee('Pending Outbox:');
        $response->assertSee('2 item(s)');
        $response->assertSee('Outbox by Model');
        $response->assertSee('Lakes &amp; Waters', false);
        $response->assertSee('Anglers');
        $response->assertSee('Pending push: 1 Anglers, 1 Lakes &amp; Waters', false);
    }

    #[Test]
    public function admin_trigger_sync_redirects_with_detailed_breakdown_status()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        $this->mock(NasSyncService::class, function ($mock) {
            $mock->shouldReceive('sync')->once()->andReturn([
                'pushed' => 2,
                'pulled' => 1,
                'pushed_breakdown' => ['Catches' => 2],
                'pulled_breakdown' => ['Lakes & Waters' => 1],
                'last_synced_at' => now()->toIso8601String(),
            ]);
        });

        $response = $this->actingAs($admin)->post('/admin/sync/trigger');

        $response->assertRedirect(route('admin'));
        $response->assertSessionHas('status', 'NAS Sync completed! Pushed 2 items (2 Catches), pulled 1 items (1 Lakes & Waters).');
    }

    #[Test]
    public function admin_trigger_baseline_sync_redirects_with_baseline_status()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        $this->mock(NasSyncService::class, function ($mock) {
            $mock->shouldReceive('sync')->once()->with(true)->andReturn([
                'pushed' => 0,
                'pulled' => 34,
                'pushed_breakdown' => [],
                'pulled_breakdown' => ['Catches' => 34],
                'last_synced_at' => now()->toIso8601String(),
                'is_baseline' => true,
            ]);
        });

        $response = $this->actingAs($admin)->post('/admin/sync/baseline');

        $response->assertRedirect(route('admin'));
        $response->assertSessionHas('status', 'Full Baseline NAS Sync completed! Pushed 0 items, pulled 34 items (34 Catches).');
    }

    #[Test]
    public function admin_trigger_mark_all_synced_redirects_with_status()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        $this->mock(NasSyncService::class, function ($mock) {
            $mock->shouldReceive('markAllSynced')->once()->andReturn(12);
        });

        $response = $this->actingAs($admin)->post('/admin/sync/mark-synced');

        $response->assertRedirect(route('admin'));
        $response->assertSessionHas('status', 'Successfully marked 12 local record(s) as synced.');
    }
}
