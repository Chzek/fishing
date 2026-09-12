<?php

namespace Tests\Unit;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Crew;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\Record;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CrewTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function crew_belongs_to_an_expedition()
    {
        $expedition = Expedition::create([
            'description' => 'Wawa 2026',
            'start' => '2026-08-01',
            'finish' => '2026-08-08',
        ]);
        $angler = Angler::factory()->create();

        $crew = Crew::create([
            'expeditions_id' => $expedition->id,
            'anglers_id' => $angler->id,
        ]);

        $this->assertInstanceOf(Expedition::class, $crew->expedition);
        $this->assertSame($expedition->id, $crew->expedition->id);
    }

    #[Test]
    public function crew_has_one_angler()
    {
        $expedition = Expedition::create([
            'description' => 'Wawa 2026',
            'start' => '2026-08-01',
            'finish' => '2026-08-08',
        ]);
        $angler = Angler::factory()->create();

        $crew = Crew::create([
            'expeditions_id' => $expedition->id,
            'anglers_id' => $angler->id,
        ]);

        $this->assertInstanceOf(Angler::class, $crew->angler);
        $this->assertSame($angler->id, $crew->angler->id);
    }

    #[Test]
    public function crew_has_many_records_for_assigned_angler()
    {
        $expedition = Expedition::create([
            'description' => 'Wawa 2026',
            'start' => '2026-08-01',
            'finish' => '2026-08-08',
        ]);
        $angler = Angler::factory()->create();

        $crew = Crew::create([
            'expeditions_id' => $expedition->id,
            'anglers_id' => $angler->id,
        ]);

        $record = Record::factory()->create(['anglers_id' => $angler->id]);

        $this->assertCount(1, $crew->records);
        $this->assertTrue($crew->records->contains($record));
    }
}
