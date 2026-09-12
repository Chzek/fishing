<?php

namespace Tests\Feature\Livewire;

use Fishinglog\Livewire\Ui\OfflineSyncIndicator;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OfflineSyncIndicatorTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function offline_sync_indicator_mounts_with_defaults(): void
    {
        Livewire::test(OfflineSyncIndicator::class)
            ->assertSet('compact', false)
            ->assertSet('showLabel', true)
            ->assertSet('syncedCount', 0)
            ->assertSet('lastSyncedAt', null)
            ->assertSeeHtml('x-data="offlineSyncIndicator()"')
            ->assertSeeHtml('/record/offline-review');
    }

    #[Test]
    public function offline_sync_indicator_accepts_compact_parameters(): void
    {
        Livewire::test(OfflineSyncIndicator::class, ['compact' => true, 'showLabel' => false])
            ->assertSet('compact', true)
            ->assertSet('showLabel', false)
            ->assertSeeHtml('x-data="offlineSyncIndicator()"');
    }

    #[Test]
    public function offline_sync_indicator_handles_sync_completed_event(): void
    {
        Livewire::test(OfflineSyncIndicator::class)
            ->dispatch('offline-sync-completed', syncedCount: 3)
            ->assertSet('syncedCount', 3)
            ->assertNotSet('lastSyncedAt', null);
    }

    #[Test]
    public function offline_sync_indicator_handles_queue_updated_event(): void
    {
        Livewire::test(OfflineSyncIndicator::class)
            ->dispatch('offline-queue-updated')
            ->assertStatus(200);
    }

    #[Test]
    public function offline_sync_indicator_renders_in_application_layout_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSeeHtml('offlineSyncIndicator()');
        $response->assertSee('Sync Telemetry');
    }
}
