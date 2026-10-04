<?php

namespace Tests\Unit;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Services\TrophyScoringService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TrophyScoringServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected TrophyScoringService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new TrophyScoringService();
    }

    #[Test]
    public function it_calculates_normalized_trophy_scores_accurately()
    {
        $smallmouth = FishBreed::factory()->create([
            'name' => 'Smallmouth Bass',
            'trophy_length_bench' => 20.0,
            'trophy_weight_bench' => 4.5,
        ]);

        $walleye = FishBreed::factory()->create([
            'name' => 'Walleye',
            'trophy_length_bench' => 28.0,
            'trophy_weight_bench' => 8.0,
        ]);

        $pike = FishBreed::factory()->create([
            'name' => 'Northern Pike',
            'trophy_length_bench' => 36.0,
            'trophy_weight_bench' => 15.0,
        ]);

        // 21.0" Smallmouth vs 20.0" benchmark = 105.0%
        $bassScore = $this->service->calculateScore(21.0, $smallmouth);
        $this->assertEquals(105.0, $bassScore);

        // 29.4" Walleye vs 28.0" benchmark = 105.0%
        $walleyeScore = $this->service->calculateScore(29.4, $walleye);
        $this->assertEquals(105.0, $walleyeScore);

        // 33.0" Pike vs 36.0" benchmark = 91.7%
        $pikeScore = $this->service->calculateScore(33.0, $pike);
        $this->assertEquals(91.7, $pikeScore);

        // Zero / negative length
        $this->assertEquals(0.0, $this->service->calculateScore(0.0, $smallmouth));
        $this->assertEquals(0.0, $this->service->calculateScore(-5.0, $smallmouth));
    }

    #[Test]
    public function it_uses_fallback_benchmarks_when_database_values_are_empty()
    {
        $unknownFish = FishBreed::factory()->create([
            'name' => 'Yellow Perch',
            'trophy_length_bench' => null,
            'trophy_weight_bench' => null,
        ]);

        // Fallback for Yellow Perch is 12.0"
        $perchScore = $this->service->calculateScore(12.6, $unknownFish);
        $this->assertEquals(105.0, $perchScore);
    }

    #[Test]
    public function it_evaluates_trophy_rating_tiers_correctly()
    {
        $master = $this->service->getTrophyTier(105.0);
        $this->assertEquals('master', $master['key']);
        $this->assertEquals('Master Angler', $master['label']);
        $this->assertTrue($master['is_trophy']);
        $this->assertEquals('amber', $master['badge_variant']);

        $gold = $this->service->getTrophyTier(95.4);
        $this->assertEquals('gold', $gold['key']);
        $this->assertEquals('Gold Class', $gold['label']);
        $this->assertFalse($gold['is_trophy']);
        $this->assertEquals('emerald', $gold['badge_variant']);

        $silver = $this->service->getTrophyTier(84.0);
        $this->assertEquals('silver', $silver['key']);
        $this->assertEquals('Silver Class', $silver['label']);
        $this->assertFalse($silver['is_trophy']);
        $this->assertEquals('teal', $silver['badge_variant']);

        $standard = $this->service->getTrophyTier(72.5);
        $this->assertEquals('standard', $standard['key']);
        $this->assertEquals('Standard Catch', $standard['label']);
        $this->assertFalse($standard['is_trophy']);
        $this->assertEquals('slate', $standard['badge_variant']);
    }

    #[Test]
    public function it_ranks_top_normalized_catches_with_weight_tie_breakers()
    {
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create();

        $smallmouth = FishBreed::factory()->create(['name' => 'Smallmouth Bass', 'trophy_length_bench' => 20.0]);
        $walleye = FishBreed::factory()->create(['name' => 'Walleye', 'trophy_length_bench' => 28.0]);
        $pike = FishBreed::factory()->create(['name' => 'Northern Pike', 'trophy_length_bench' => 36.0]);

        // Catches:
        // 1. Pike: 34" (34 / 36 = 94.4 pts)
        $c1 = Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $pike->id,
            'length' => 34.0,
            'weight' => 11.5,
            'caught' => '2026-07-01',
        ]);

        // 2. Bass: 21" (21 / 20 = 105.0 pts) with weight 5.2 lbs
        $c2 = Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $smallmouth->id,
            'length' => 21.0,
            'weight' => 5.2,
            'caught' => '2026-07-02',
        ]);

        // 3. Walleye: 29.4" (29.4 / 28 = 105.0 pts) with weight 9.8 lbs (Wins tie-breaker over Bass)
        $c3 = Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $walleye->id,
            'length' => 29.4,
            'weight' => 9.8,
            'caught' => '2026-07-03',
        ]);

        // 4. Bass 2: 20" (20 / 20 = 100.0 pts)
        $c4 = Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $smallmouth->id,
            'length' => 20.0,
            'weight' => 4.6,
            'caught' => '2026-07-04',
        ]);

        $top = $this->service->getTopNormalizedCatches(Record::where('lakes_id', $lake->id), 4);

        $this->assertCount(4, $top);

        // Expected order:
        // Rank #1: Walleye (105.0 pts, 9.8 lbs)
        // Rank #2: Bass (105.0 pts, 5.2 lbs)
        // Rank #3: Bass 2 (100.0 pts, 4.6 lbs)
        // Rank #4: Pike (94.4 pts, 11.5 lbs)
        $this->assertEquals($c3->id, $top[0]->id);
        $this->assertEquals(105.0, $top[0]->trophy_score);

        $this->assertEquals($c2->id, $top[1]->id);
        $this->assertEquals(105.0, $top[1]->trophy_score);

        $this->assertEquals($c4->id, $top[2]->id);
        $this->assertEquals(100.0, $top[2]->trophy_score);

        $this->assertEquals($c1->id, $top[3]->id);
        $this->assertEquals(94.4, $top[3]->trophy_score);
    }

    #[Test]
    public function it_estimates_biological_weight_from_length()
    {
        $smallmouth = FishBreed::factory()->create(['name' => 'Smallmouth Bass']);
        $walleye = FishBreed::factory()->create(['name' => 'Walleye']);

        // 20" Smallmouth ~ 5.3 lbs
        $bassEst = $this->service->estimateWeight(20.0, $smallmouth);
        $this->assertNotNull($bassEst);
        $this->assertGreaterThan(4.5, $bassEst);
        $this->assertLessThan(6.0, $bassEst);

        // 28" Walleye ~ 8.0 lbs
        $walleyeEst = $this->service->estimateWeight(28.0, $walleye);
        $this->assertNotNull($walleyeEst);
        $this->assertGreaterThan(7.0, $walleyeEst);
        $this->assertLessThan(9.5, $walleyeEst);

        $this->assertNull($this->service->estimateWeight(0.0, $smallmouth));
    }
}
