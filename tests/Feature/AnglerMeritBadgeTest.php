<?php

namespace Tests\Feature;

use Database\Seeders\BadgeCatalogSeeder;
use Fishinglog\Events\CatchLoggedEvent;
use Fishinglog\Models\Angler;
use Fishinglog\Models\AnglerBadge;
use Fishinglog\Models\Badge;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Services\BadgeEvaluatorService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AnglerMeritBadgeTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure badge catalog is seeded for the test run
        $this->seed(BadgeCatalogSeeder::class);
    }

    #[Test]
    public function badge_catalog_is_seeded_with_all_categories_and_tiers(): void
    {
        $this->assertGreaterThanOrEqual(33, Badge::count());

        $expectedCategories = [
            'volume',
            'species',
            'diversity',
            'cadence',
            'streak',
            'exploration',
            'weather',
            'nomad',
            'yardage',
            'conservation',
            'heritage',
        ];

        foreach ($expectedCategories as $category) {
            $this->assertTrue(
                Badge::where('category', $category)->exists(),
                "Expected category {$category} to exist in seeded badge catalog."
            );
        }

        $expectedTiers = ['bronze', 'silver', 'gold', 'platinum'];
        foreach ($expectedTiers as $tier) {
            $this->assertTrue(
                Badge::where('tier', $tier)->exists(),
                "Expected tier {$tier} to exist in seeded badge catalog."
            );
        }
    }

    #[Test]
    public function evaluator_awards_volume_badges_with_correct_timestamps_and_summaries(): void
    {
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create(['name' => 'Saganaga Lake']);
        $breed = FishBreed::factory()->create(['name' => 'Walleye']);

        // Create 5 catches on different dates
        $catches = collect();
        for ($i = 1; $i <= 5; $i++) {
            $catches->push(Record::factory()->create([
                'anglers_id' => $angler->id,
                'lakes_id' => $lake->id,
                'fish_breeds_id' => $breed->id,
                'length' => 20.0 + $i,
                'caught' => "2026-06-0{$i} 12:00:00",
            ]));
        }

        $service = app(BadgeEvaluatorService::class);
        $awarded = $service->evaluateAngler($angler);

        // Should have at least vol_1 ('First Cast') and vol_5 ('High Five')
        $firstCast = $awarded->first(fn (AnglerBadge $ab) => $ab->badge->slug === 'vol_1');
        $highFive = $awarded->first(fn (AnglerBadge $ab) => $ab->badge->slug === 'vol_5');

        $this->assertNotNull($firstCast);
        $this->assertSame($catches->first()->id, $firstCast->record_id);
        $this->assertSame('2026-06-01', $firstCast->awarded_at->toDateString());
        $this->assertStringContainsString('Career catch #1', $firstCast->trigger_summary);

        $this->assertNotNull($highFive);
        $this->assertSame($catches->last()->id, $highFive->record_id);
        $this->assertSame('2026-06-05', $highFive->awarded_at->toDateString());
        $this->assertStringContainsString('Career catch #5', $highFive->trigger_summary);
    }

    #[Test]
    public function evaluator_awards_diversity_badge_at_exact_threshold(): void
    {
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create();

        $breed1 = FishBreed::factory()->create(['name' => 'Walleye']);
        $breed2 = FishBreed::factory()->create(['name' => 'Northern Pike']);
        $breed3 = FishBreed::factory()->create(['name' => 'Smallmouth Bass']);

        Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed1->id,
            'caught' => '2026-07-01',
        ]);

        Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed2->id,
            'caught' => '2026-07-02',
        ]);

        $thirdCatch = Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed3->id,
            'caught' => '2026-07-03',
        ]);

        $service = app(BadgeEvaluatorService::class);
        $awarded = $service->evaluateAngler($angler);

        // 'div_sampler' requires 3 distinct species
        $samplerBadge = $awarded->first(fn (AnglerBadge $ab) => $ab->badge->slug === 'div_sampler');

        $this->assertNotNull($samplerBadge);
        $this->assertSame($thirdCatch->id, $samplerBadge->record_id);
        $this->assertSame('2026-07-03', $samplerBadge->awarded_at->toDateString());
        $this->assertStringContainsString('Diversity milestone reached (3 distinct species)', $samplerBadge->trigger_summary);
    }

    #[Test]
    public function evaluator_prevents_duplicate_badge_awards(): void
    {
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create();

        Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'caught' => '2026-05-10 14:00:00',
        ]);

        $service = app(BadgeEvaluatorService::class);

        // First evaluation awards First Cast
        $firstRun = $service->evaluateAngler($angler);
        $this->assertTrue($firstRun->contains(fn ($ab) => $ab->badge->slug === 'vol_1'));

        // Second evaluation returns no duplicates
        $secondRun = $service->evaluateAngler($angler);
        $this->assertEmpty($secondRun);

        $this->assertSame(
            1,
            AnglerBadge::where('anglers_id', $angler->id)->whereHas('badge', fn ($q) => $q->where('slug', 'vol_1'))->count()
        );
    }

    #[Test]
    public function catch_logged_event_triggers_badge_evaluation_listener(): void
    {
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create();

        $record = Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'caught' => '2026-08-01 09:00:00',
        ]);

        // Dispatch CatchLoggedEvent
        event(new CatchLoggedEvent($record));

        // The listener should have awarded First Cast to the angler
        $this->assertDatabaseHas('angler_badges', [
            'anglers_id' => $angler->id,
            'record_id' => $record->id,
        ]);
    }

    #[Test]
    public function badges_recalculate_command_runs_successfully(): void
    {
        $exitCode = Artisan::call('badges:recalculate', ['--force' => true]);

        $this->assertSame(0, $exitCode);
    }

    #[Test]
    public function merit_badge_blade_component_renders_popover_and_metadata(): void
    {
        $angler = Angler::factory()->create();
        $badge = Badge::where('slug', 'vol_100')->first();
        if (!$badge) {
            $badge = Badge::factory()->create([
                'slug' => 'vol_100',
                'name' => 'Century Club',
                'description' => 'Logged 100 career catches',
                'tier' => 'gold',
                'points' => 100,
            ]);
        }

        $lake = Lake::factory()->create(['name' => 'Lac La Croix']);
        $record = Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'length' => 28.5,
        ]);

        $pivot = AnglerBadge::factory()->create([
            'anglers_id' => $angler->id,
            'badge_id' => $badge->id,
            'record_id' => $record->id,
            'awarded_at' => '2026-06-15 15:30:00',
            'points' => 100,
            'trigger_summary' => 'Career catch #100: 28.5" Walleye at Lac La Croix',
        ]);

        $view = $this->blade('<x-meritBadge :badgePivot="$pivot" />', ['pivot' => $pivot]);

        $view->assertSee('Century Club');
        $view->assertSee('+100 pts');
        $view->assertSee('gold');
        $view->assertSee('Logged 100 career catches');
        $view->assertSee('Awarded Jun 15, 2026');
        $view->assertSee('Career catch #100: 28.5', false);
        $view->assertSee('/record/' . $record->id);
    }
}
