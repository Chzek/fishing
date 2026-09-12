<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DatabaseCompositeIndexTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function records_table_has_all_target_composite_indexes()
    {
        $indexes = DB::select("SHOW INDEXES FROM records");
        $indexNames = collect($indexes)->pluck('Key_name')->unique()->values()->all();

        $expectedIndexes = [
            'records_anglers_caught_idx',
            'records_lakes_fish_breeds_idx',
            'records_fish_breeds_length_idx',
            'records_fish_breeds_weight_idx',
            'records_lakes_caught_idx',
            'records_anglers_fish_breeds_idx',
        ];

        foreach ($expectedIndexes as $expected) {
            $this->assertContains($expected, $indexNames, "Failed asserting that records table contains index [{$expected}].");
        }
    }

    #[Test]
    public function composite_indexed_queries_execute_cleanly()
    {
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create();
        $family = FishFamily::factory()->create();
        $breed = FishBreed::factory()->create(['fish_families_id' => $family->id]);

        $record = Record::create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'caught' => now()->subDays(2),
            'length' => 22.5,
            'weight' => 5.75,
        ]);

        // 1. Angler chronological query (uses records_anglers_caught_idx)
        $anglerCatches = Record::where('anglers_id', $angler->id)->orderBy('caught', 'desc')->get();
        $this->assertTrue($anglerCatches->contains($record));

        // 2. Lake species breakdown query (uses records_lakes_fish_breeds_idx)
        $lakeSpeciesCatches = Record::where('lakes_id', $lake->id)->where('fish_breeds_id', $breed->id)->get();
        $this->assertTrue($lakeSpeciesCatches->contains($record));

        // 3. Species PB length query (uses records_fish_breeds_length_idx)
        $topSpeciesByLength = Record::where('fish_breeds_id', $breed->id)->orderBy('length', 'desc')->first();
        $this->assertNotNull($topSpeciesByLength);
        $this->assertSame($record->id, $topSpeciesByLength->id);

        // 4. Species PB weight query (uses records_fish_breeds_weight_idx)
        $topSpeciesByWeight = Record::where('fish_breeds_id', $breed->id)->orderBy('weight', 'desc')->first();
        $this->assertNotNull($topSpeciesByWeight);
        $this->assertSame($record->id, $topSpeciesByWeight->id);

        // 5. Lake chronological query (uses records_lakes_caught_idx)
        $lakeCatches = Record::where('lakes_id', $lake->id)->orderBy('caught', 'desc')->get();
        $this->assertTrue($lakeCatches->contains($record));

        // 6. Angler per-species query (uses records_anglers_fish_breeds_idx)
        $anglerSpeciesCatches = Record::where('anglers_id', $angler->id)->where('fish_breeds_id', $breed->id)->get();
        $this->assertTrue($anglerSpeciesCatches->contains($record));
    }
}
