<?php

namespace Tests\Feature;

use Fishinglog\Actions\Lakes\CreateLakeAction;
use Fishinglog\Actions\Lakes\UpdateLakeAction;
use Fishinglog\Actions\Lures\CreateLureVariantAction;
use Fishinglog\Actions\Records\UpdateCatchRecordAction;
use Fishinglog\Events\CatchLoggedEvent;
use Fishinglog\Models\Angler;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\LakeDailyWeather;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Fishinglog\Services\AnglerTelemetryService;
use Fishinglog\Services\ExpeditionAnalyticsService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
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

        // 3. Normalizes redundant brand prefix in model name
        $normalizedLures = $action->execute([
            'name' => 'Rapala Shad Rap',
            'brand' => 'Rapala',
            'category' => 'Crankbait',
            'color' => 'Bleeding Olive',
        ]);
        $this->assertDatabaseHas('lures', [
            'brand' => 'Rapala',
            'name' => 'Shad Rap',
            'color' => 'Bleeding Olive',
        ]);
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

    #[Test]
    public function angler_telemetry_service_calculates_career_stats_and_deduplicated_species_pbs(): void
    {
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create(['name' => 'Catfish Lake']);
        $walleye = FishBreed::factory()->create(['name' => 'Walleye', 'trophy_length_bench' => 28.0]);
        $pike = FishBreed::factory()->create(['name' => 'Northern Pike', 'trophy_length_bench' => 36.0]);
        $lure = Lure::factory()->create(['name' => 'Little Cleo']);

        Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $walleye->id,
            'lures_id' => $lure->id,
            'length' => 22.0,
            'weight' => 4.0,
            'released' => 1,
            'caught' => '2026-06-10',
        ]);

        $pbWalleye = Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $walleye->id,
            'lures_id' => $lure->id,
            'length' => 29.0,
            'weight' => 9.5,
            'released' => 1,
            'caught' => '2026-06-12',
        ]);

        $pbPike = Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $pike->id,
            'lures_id' => $lure->id,
            'length' => 34.0,
            'weight' => 10.5,
            'released' => 0,
            'caught' => '2026-06-14',
        ]);

        $service = app(AnglerTelemetryService::class);
        $telemetry = $service->getAnglerTelemetry($angler);

        $this->assertSame(3, $telemetry['record_count']);
        $this->assertSame(1, $telemetry['lake_count']);
        $this->assertSame(85.0, $telemetry['totalInches']);
        $this->assertSame(67, $telemetry['releaseRate']);
        $this->assertSame($pbPike->id, $telemetry['longest']?->id);
        $this->assertSame($pbPike->id, $telemetry['fattest']?->id);
        $this->assertSame('June', $telemetry['peakMonthName']);
        $this->assertCount(2, $telemetry['speciesPbs']);
        $this->assertSame($pbPike->id, $telemetry['speciesPbs']->first()->id);
        $this->assertSame($pbWalleye->id, $telemetry['speciesPbs']->last()->id);

        $emptyTelemetry = $service->getAnglerTelemetry(null);
        $this->assertSame(0, $emptyTelemetry['record_count']);
        $this->assertNull($emptyTelemetry['angler']);
    }

    #[Test]
    public function update_catch_record_action_updates_record_and_dispatches_event_on_metric_changes(): void
    {
        Event::fake([CatchLoggedEvent::class]);

        $record = Record::factory()->create([
            'length' => 18.0,
            'weight' => 2.5,
            'released' => 1,
        ]);

        $action = app(UpdateCatchRecordAction::class);
        $updated = $action->execute($record, [
            'anglers_id' => $record->anglers_id,
            'lakes_id' => $record->lakes_id,
            'fish_breeds_id' => $record->fish_breeds_id,
            'lures_id' => $record->lures_id,
            'length' => 26.5,
            'weight' => 7.25,
            'temperature' => 64.0,
            'released' => 1,
            'caught' => '2026-07-15',
        ]);

        $this->assertSame(26.5, (float) $updated->length);
        $this->assertSame(7.25, (float) $updated->weight);
        Event::assertDispatched(CatchLoggedEvent::class, function (CatchLoggedEvent $event) use ($record) {
            return $event->record->id === $record->id && ($event->context['updated'] ?? false) === true;
        });
    }

    #[Test]
    public function create_and_update_lake_actions_persist_attributes_and_spatial_coordinates(): void
    {
        $createAction = new CreateLakeAction();
        $lake = $createAction->execute([
            'name' => 'Action Test Lake',
            'latitude' => 48.125,
            'longitude' => -84.625,
            'structure' => 'Rock & Weedline',
            'max_depth' => 65,
        ]);

        $this->assertDatabaseHas('lakes', [
            'id' => $lake->id,
            'name' => 'Action Test Lake',
            'structure' => 'Rock & Weedline',
            'max_depth' => 65,
        ]);

        $updateAction = new UpdateLakeAction();
        $updated = $updateAction->execute($lake, [
            'name' => 'Action Updated Lake',
            'latitude' => 48.200,
            'longitude' => -84.700,
            'structure' => 'Deep Drop-off',
            'max_depth' => 80,
        ]);

        $this->assertSame('Action Updated Lake', $updated->name);
        $this->assertSame(80.0, $updated->max_depth);
    }

    #[Test]
    public function hydrate_daily_weather_for_collection_matches_exact_lake_and_date(): void
    {
        $lake = Lake::factory()->create();
        $weatherMatch = LakeDailyWeather::create([
            'lakes_id' => $lake->id,
            'date' => '2026-07-04',
            'weather_condition' => 'Partly Cloudy',
            'air_temp_min' => 58.0,
            'air_temp_max' => 74.0,
            'air_temp_mean' => 66.0,
            'barometric_pressure' => 1014.2,
            'wind_speed_max' => 11.5,
        ]);

        LakeDailyWeather::create([
            'lakes_id' => $lake->id,
            'date' => '2026-07-05',
            'weather_condition' => 'Rain Showers',
            'air_temp_min' => 52.0,
            'air_temp_max' => 61.0,
            'air_temp_mean' => 56.5,
            'barometric_pressure' => 1004.0,
            'wind_speed_max' => 18.0,
        ]);

        $record = Record::factory()->create([
            'lakes_id' => $lake->id,
            'caught' => '2026-07-04 14:00:00',
        ]);

        Record::hydrateDailyWeatherForCollection(collect([$record]));

        $this->assertTrue($record->relationLoaded('dailyWeather'));
        $this->assertNotNull($record->dailyWeather);
        $this->assertSame($weatherMatch->id, $record->dailyWeather->id);
        $this->assertSame('Partly Cloudy', $record->dailyWeather->weather_condition);
    }
}
