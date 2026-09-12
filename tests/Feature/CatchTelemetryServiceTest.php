<?php

namespace Tests\Feature;

use Fishinglog\Actions\Lures\CreateLureVariantAction;
use Fishinglog\Actions\Media\ProcessPhotoUploadAction;
use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Fishinglog\Models\FishingRule;
use Fishinglog\Models\FishingZone;
use Fishinglog\Models\Lake;
use Fishinglog\Models\LakeDailyWeather;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Photo;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Fishinglog\Services\CatchTelemetryService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CatchTelemetryServiceTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function catch_telemetry_service_computes_accurate_aggregates()
    {
        $family = FishFamily::create(['name' => 'Percidae']);
        $breed = FishBreed::create(['name' => 'Walleye', 'fish_families_id' => $family->id]);
        $lake = Lake::factory()->create(['name' => 'Wawa Lake']);
        $angler = Angler::factory()->create(['firstName' => 'John', 'lastName' => 'Doe']);

        // Create 2 catches
        Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'length' => 24.0,
            'weight' => 5.0,
            'temperature' => 68.0,
            'released' => 1,
            'caught' => '2026-06-15',
        ]);

        Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'length' => 30.0,
            'weight' => 10.0,
            'temperature' => 70.0,
            'released' => 0,
            'caught' => '2026-06-16',
        ]);

        $service = app(CatchTelemetryService::class);
        $telemetry = $service->calculateTelemetry(Record::query());

        $this->assertGreaterThanOrEqual(2, $telemetry['totalCatches']);
        $this->assertGreaterThanOrEqual(54.0, $telemetry['totalInches']);
        $this->assertNotNull($telemetry['longestCatch']);
        $this->assertNotNull($telemetry['heaviestCatch']);
        $this->assertArrayHasKey('topAnglers', $telemetry);
        $this->assertArrayHasKey('topLakes', $telemetry);
        $this->assertArrayHasKey('speciesTrends', $telemetry);
        $this->assertArrayHasKey('weatherDistribution', $telemetry);
        $this->assertArrayHasKey('lakeWeatherMatrix', $telemetry);
    }

    #[Test]
    public function lure_selector_categories_are_cached_and_invalidated_on_model_mutation()
    {
        Cache::forget('lure_categories');

        Lure::create([
            'name' => 'Deep Diver',
            'brand' => 'Rapala',
            'category' => 'Crankbait',
            'color' => 'Firetiger',
        ]);

        $component = new \Fishinglog\Livewire\Ui\LureSelector();
        $component->mount();
        $view = $component->render();

        $this->assertTrue(Cache::has('lure_categories'));
        $cached = Cache::get('lure_categories');
        $this->assertContains('Crankbait', $cached);

        // Mutating a lure invalidates the cache
        Lure::create([
            'name' => 'Spinner Max',
            'brand' => 'Mepps',
            'category' => 'Inline Spinner',
            'color' => 'Silver',
        ]);

        $this->assertFalse(Cache::has('lure_categories'));
    }

    #[Test]
    public function record_scope_with_daily_weather_eager_loads_weather()
    {
        $lake = Lake::factory()->create();
        LakeDailyWeather::create([
            'lakes_id' => $lake->id,
            'date' => '2026-06-15',
            'weather_condition' => 'Clear',
            'weather_code' => 800,
            'air_temp_mean' => 72.5,
        ]);

        $record = Record::factory()->create([
            'lakes_id' => $lake->id,
            'caught' => '2026-06-15 14:00:00',
        ]);

        $loadedRecord = Record::withDailyWeather()->where('id', $record->id)->first();
        $this->assertTrue($loadedRecord->relationLoaded('lake'));
        $this->assertTrue($loadedRecord->lake->relationLoaded('dailyWeather'));
        $this->assertNotNull($loadedRecord->daily_weather);
        $this->assertSame('Clear', $loadedRecord->daily_weather->weather_condition);
    }

    #[Test]
    public function process_photo_upload_action_optimizes_and_saves_images()
    {
        Storage::fake('public');
        $action = app(ProcessPhotoUploadAction::class);

        $file = UploadedFile::fake()->image('avatar.jpg', 1200, 800);
        $action->optimizeAndSave($file, 'avatars/test_avatar.jpg', 600);

        Storage::disk('public')->assertExists('avatars/test_avatar.jpg');
    }

    #[Test]
    public function models_have_native_casts_method()
    {
        $lake = new Lake();
        $record = new Record();
        $photo = new Photo();
        $rule = new FishingRule();
        $zone = new FishingZone();
        $weather = new LakeDailyWeather();

        $this->assertIsArray($lake->getCasts());
        $this->assertIsArray($record->getCasts());
        $this->assertIsArray($photo->getCasts());
        $this->assertIsArray($rule->getCasts());
        $this->assertIsArray($zone->getCasts());
        $this->assertIsArray($weather->getCasts());

        $this->assertArrayHasKey('is_cover', $photo->getCasts());
        $this->assertArrayHasKey('bounds', $zone->getCasts());
        $this->assertArrayHasKey('hourly_telemetry', $weather->getCasts());
    }

    #[Test]
    public function store_photo_request_validates_batch_upload()
    {
        $user = User::factory()->create();
        $lake = Lake::factory()->create();

        // Missing photos array should fail validation
        $response = $this->actingAs($user)->postJson(route('photos.store'), [
            'photoable_type' => 'lake', // invalid type
            'photoable_id' => $lake->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['photoable_type', 'photos']);
    }
}
