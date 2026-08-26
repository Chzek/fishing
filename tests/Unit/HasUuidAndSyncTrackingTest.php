<?php

namespace Tests\Unit;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HasUuidAndSyncTrackingTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_assigns_uuid_and_pending_sync_status_on_create()
    {
        $lake = Lake::create([
            'name' => 'Test Sync Lake',
            'latitude' => 45.0,
            'longitude' => -78.0,
        ]);

        $this->assertNotEmpty($lake->id);
        $this->assertEquals(36, strlen($lake->id));
        $this->assertEquals('pending_upstream', $lake->sync_status);
        $this->assertNull($lake->synced_at);
    }

    #[Test]
    public function it_marks_model_as_synced()
    {
        $lure = Lure::create([
            'name' => 'Sync Crankbait',
            'color' => 'Red',
            'size' => 'Medium',
        ]);

        $this->assertEquals('pending_upstream', $lure->sync_status);

        $lure->markSynced();

        $this->assertEquals('synced', $lure->fresh()->sync_status);
        $this->assertNotNull($lure->fresh()->synced_at);
    }

    #[Test]
    public function it_resets_sync_status_to_pending_on_update()
    {
        $angler = Angler::create([
            'firstName' => 'Sync',
            'middleName' => '',
            'lastName' => 'Angler',
        ]);

        $angler->markSynced();
        $this->assertEquals('synced', $angler->fresh()->sync_status);

        $angler->update(['lastName' => 'UpdatedName']);
        $this->assertEquals('pending_upstream', $angler->fresh()->sync_status);
    }
}
