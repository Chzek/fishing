<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\JournalEntry;
use Fishinglog\Models\Lake;
use Fishinglog\Services\ExpeditionDiscoveryService;
use Fishinglog\Services\JournalTranscriptionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExpeditionDiscoveryServiceTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_finds_existing_expedition_covering_a_date(): void
    {
        $expedition = Expedition::factory()->create([
            'start' => '2026-06-20',
            'finish' => '2026-06-27',
        ]);

        $service = app(ExpeditionDiscoveryService::class);

        $found = $service->findExpeditionForDate('2026-06-22');
        $this->assertNotNull($found);
        $this->assertEquals($expedition->id, $found->id);

        $outside = $service->findExpeditionForDate('2026-08-01');
        $this->assertNull($outside);
    }

    #[Test]
    public function it_only_matches_exact_anglers_and_prevents_false_historical_associations(): void
    {
        $andy = Angler::factory()->create(['firstName' => 'Andy', 'lastName' => 'Brauer']);
        $simon = Angler::factory()->create(['firstName' => 'Simon', 'lastName' => 'Brauer']);
        $lake = Lake::factory()->create(['name' => 'Catfish Creek']);

        $entry = JournalEntry::factory()->create([
            'title' => 'Historical Test Entry',
            'entry_date' => '2007-06-29',
        ]);

        $transcriptionService = app(JournalTranscriptionService::class);

        // "Red Brauer" and "Andy Brauer" mentioned. Red Brauer is NOT Simon Brauer.
        $transcriptionService->syncMentionedEntities(
            $entry,
            ['Red Brauer', 'Andy Brauer'],
            ['Catfish Creek']
        );

        $entry->refresh();
        $this->assertTrue($entry->anglers->contains('id', $andy->id));
        $this->assertFalse($entry->anglers->contains('id', $simon->id));
        $this->assertTrue($entry->lakes->contains('id', $lake->id));
    }
}

