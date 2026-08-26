<?php

namespace Tests\Feature;

use Fishinglog\Actions\Lures\CreateLureVariantAction;
use Fishinglog\Models\Angler;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Fishinglog\Services\ExpeditionAnalyticsService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class P0ArchitectureRefactoringTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function create_lure_variant_action_creates_single_and_batch_lures()
    {
        $action = new CreateLureVariantAction();

        // 1. Single lure creation
        $singleLures = $action->execute([
            'name' => 'Single Shad Rap',
            'brand' => 'Rapala',
            'category' => 'Crankbait',
            'color' => 'Firetiger',
            'size' => '7cm',
        ]);

        $this->assertCount(1, $singleLures);
        $this->assertDatabaseHas('lures', [
            'name' => 'Single Shad Rap',
            'color' => 'Firetiger',
        ]);

        // 2. Batch lure creation from comma separated colors
        $batchLures = $action->execute(
            [
                'name' => 'Batch Senko',
                'brand' => 'Yamamoto',
                'category' => 'Soft Plastic',
                'size' => '5in',
            ],
            'Green Pumpkin, Watermelon Red, Black Blue Flake'
        );

        $this->assertCount(3, $batchLures);
        $this->assertDatabaseHas('lures', ['name' => 'Batch Senko', 'color' => 'Green Pumpkin']);
        $this->assertDatabaseHas('lures', ['name' => 'Batch Senko', 'color' => 'Watermelon Red']);
        $this->assertDatabaseHas('lures', ['name' => 'Batch Senko', 'color' => 'Black Blue Flake']);
    }

    #[Test]
    public function expedition_analytics_service_calculates_trip_accolades_and_telemetry()
    {
        $expedition = Expedition::create([
            'description' => 'Test Trip',
            'start' => '2026-07-01',
            'finish' => '2026-07-05',
        ]);

        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create();
        $walleye = FishBreed::factory()->create(['name' => 'Walleye']);
        $lure = Lure::factory()->create();

        Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $walleye->id,
            'lures_id' => $lure->id,
            'length' => 28.5,
            'weight' => 8.2,
            'released' => 1,
            'caught' => '2026-07-02',
        ]);

        $service = new ExpeditionAnalyticsService();
        $analytics = $service->getAnalytics($expedition);

        $this->assertEquals(1, $analytics['totalRecords']);
        $this->assertEquals(100, $analytics['releaseRate']);
        $this->assertNotNull($analytics['lunker']);
        $this->assertEquals(28.5, $analytics['lunker']->length);
        $this->assertNotNull($analytics['topRod']);
        $this->assertEquals($angler->id, $analytics['topRod']->anglers_id);
    }
}
