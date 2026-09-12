<?php

namespace Tests\Feature;

use Fishinglog\Livewire\Admin\SyncDiagnosticConsole;
use Fishinglog\Models\Angler;
use Fishinglog\Models\Lake;
use Fishinglog\Models\User;
use Fishinglog\Services\NasSyncService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminNasSyncConsoleTest extends TestCase
{
    use DatabaseTransactions;

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
    public function non_admin_cannot_access_admin_sync_console()
    {
        $user = User::factory()->create(['type' => User::DEFAULT_TYPE]);

        $response = $this->actingAs($user)->get('/admin/sync');

        $response->assertRedirect('/home');
    }

    #[Test]
    public function admin_can_access_admin_sync_console_page()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        $response = $this->actingAs($admin)->get('/admin/sync');

        $response->assertStatus(200);
        $response->assertSee('Remote Synchronization');
        $response->assertSee('Diagnostic Console');
        $response->assertSee('13-Model Outbox');
    }

    #[Test]
    public function livewire_sync_diagnostic_console_renders_matrix_and_metrics()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        Http::fake([
            'https://nas.example.com/api/v1/sync/pull*' => Http::response(['status' => 'ok'], 200),
        ]);

        config([
            'services.nas.url' => 'https://nas.example.com',
            'services.nas.token' => 'test-token',
        ]);

        Lake::create(['name' => 'Diagnostic Console Lake', 'latitude' => 45.1, 'longitude' => -78.1]);

        Livewire::actingAs($admin)
            ->test(SyncDiagnosticConsole::class)
            ->assertSee('13-Model Outbox')
            ->assertSee('Catches')
            ->assertSee('Fish Species')
            ->assertSee('Media Pipeline')
            ->assertSee('SHA-256')
            ->assertSee('Run Live Ping Probe');
    }

    #[Test]
    public function livewire_sync_diagnostic_console_executes_live_ping_probe()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        Http::fake([
            'https://nas.example.com/api/v1/sync/pull*' => Http::response(['status' => 'ok'], 200),
        ]);

        config([
            'services.nas.url' => 'https://nas.example.com',
            'services.nas.token' => 'test-token',
            'services.nas.target_name' => 'NAS',
        ]);

        Livewire::actingAs($admin)
            ->test(SyncDiagnosticConsole::class)
            ->call('testConnection')
            ->assertSet('isTesting', false)
            ->assertSee('Connection to NAS successful!');
    }

    #[Test]
    public function livewire_sync_diagnostic_console_triggers_sync_jobs()
    {
        Queue::fake();
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        Livewire::actingAs($admin)
            ->test(SyncDiagnosticConsole::class)
            ->call('triggerSync')
            ->assertSee('synchronization job queued');

        Queue::assertPushed(\Fishinglog\Jobs\SyncNasJob::class, function ($job) {
            return $job->forceBaseline === false;
        });

        Livewire::actingAs($admin)
            ->test(SyncDiagnosticConsole::class)
            ->call('triggerBaselineSync')
            ->assertSee('Full Baseline');

        Queue::assertPushed(\Fishinglog\Jobs\SyncNasJob::class, function ($job) {
            return $job->forceBaseline === true;
        });
    }

    #[Test]
    public function livewire_sync_diagnostic_console_can_mark_all_synced()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $lake = Lake::create(['name' => 'Mark Synced Lake', 'latitude' => 45.0, 'longitude' => -78.0]);
        $this->assertEquals('pending_upstream', $lake->sync_status);

        Livewire::actingAs($admin)
            ->test(SyncDiagnosticConsole::class)
            ->call('markAllSynced')
            ->assertSee('Successfully marked');

        $this->assertEquals('synced', $lake->fresh()->sync_status);
    }

    #[Test]
    public function livewire_sync_diagnostic_console_toggles_pending_filter()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        Livewire::actingAs($admin)
            ->test(SyncDiagnosticConsole::class)
            ->assertSet('filterPendingOnly', false)
            ->call('togglePendingFilter')
            ->assertSet('filterPendingOnly', true)
            ->call('togglePendingFilter')
            ->assertSet('filterPendingOnly', false);
    }

    #[Test]
    public function admin_trigger_sync_redirects_with_detailed_breakdown_status()
    {
        Queue::fake();
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        $response = $this->actingAs($admin)->post('/admin/sync/trigger');

        $response->assertRedirect(route('admin'));
        $response->assertSessionHas('status');
        Queue::assertPushed(\Fishinglog\Jobs\SyncNasJob::class, function ($job) {
            return $job->forceBaseline === false;
        });
    }

    #[Test]
    public function admin_trigger_baseline_sync_redirects_with_baseline_status()
    {
        Queue::fake();
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        $response = $this->actingAs($admin)->post('/admin/sync/baseline');

        $response->assertRedirect(route('admin'));
        $response->assertSessionHas('status');
        Queue::assertPushed(\Fishinglog\Jobs\SyncNasJob::class, function ($job) {
            return $job->forceBaseline === true;
        });
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

    #[Test]
    public function admin_can_view_dynamic_sync_labels_when_target_is_laptop()
    {
        config(['services.nas.target_name' => 'Laptop']);

        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $admin->markSynced();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Field Laptop Two-Way Sync Engine');
        $response->assertSee('Synchronize server catches, lakes, and anglers with your field laptop.');
        $response->assertSee('Sync Now with Laptop');
        $response->assertSee('Perform a Full Baseline Pull from Laptop?');
    }
}
