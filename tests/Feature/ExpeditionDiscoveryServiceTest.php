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
    public function it_recommends_missing_expeditions_from_unlinked_journal_entries(): void
    {
        $angler = Angler::factory()->create(['firstName' => 'Nicholas', 'lastName' => 'Mroczek']);
        $lake = Lake::factory()->create(['name' => 'Catfish Creek']);

        $entry1 = JournalEntry::factory()->create([
            'expeditions_id' => null,
            'entry_date' => '2007-06-28',
            'start_date' => '2007-06-28',
            'end_date' => '2007-06-29',
        ]);
        $entry1->anglers()->attach($angler->id);
        $entry1->lakes()->attach($lake->id);

        $entry2 = JournalEntry::factory()->create([
            'expeditions_id' => null,
            'entry_date' => '2007-06-30',
            'start_date' => '2007-06-30',
            'end_date' => '2007-07-01',
        ]);

        $service = app(ExpeditionDiscoveryService::class);
        $recommendations = $service->getRecommendedExpeditions();

        $this->assertNotEmpty($recommendations);
        $firstRec = $recommendations->first();

        $this->assertEquals('2007-06-28', $firstRec['start_date']);
        $this->assertEquals('2007-06-30', $firstRec['finish_date']);
        $this->assertEquals(2, $firstRec['entries_count']);
        $this->assertTrue($firstRec['matched_anglers']->contains('id', $angler->id));
        $this->assertTrue($firstRec['matched_lakes']->contains('id', $lake->id));
    }

    #[Test]
    public function it_creates_expedition_and_links_crew_and_entries_from_recommendation(): void
    {
        $angler = Angler::factory()->create();
        $entry = JournalEntry::factory()->create([
            'expeditions_id' => null,
            'entry_date' => '2007-06-29',
        ]);

        $service = app(ExpeditionDiscoveryService::class);

        $recPayload = [
            'suggested_title' => 'Canada Spring Expedition 2007',
            'start_date' => '2007-06-28',
            'finish_date' => '2007-07-02',
            'entry_ids' => [$entry->id],
            'matched_anglers' => [$angler],
        ];

        $expedition = $service->createFromRecommendation($recPayload);

        $this->assertDatabaseHas('expeditions', [
            'id' => $expedition->id,
            'description' => 'Canada Spring Expedition 2007',
        ]);

        $this->assertDatabaseHas('crews', [
            'expeditions_id' => $expedition->id,
            'anglers_id' => $angler->id,
        ]);

        $entry->refresh();
        $this->assertEquals($expedition->id, $entry->expeditions_id);
    }
}
