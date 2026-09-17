<?php

namespace Tests\Feature;

use Fishinglog\Actions\Records\CreateCatchRecordAction;
use Fishinglog\Events\CatchLoggedEvent;
use Fishinglog\Listeners\CheckTrophyMilestoneListener;
use Fishinglog\Listeners\FetchCatchWeatherListener;
use Fishinglog\Listeners\InvalidateTelemetryCacheListener;
use Fishinglog\Livewire\Modals\QuickCatchModal;
use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Fishinglog\Notifications\TrophyCatchLogged;
use Fishinglog\Services\CatchTelemetryService;
use Fishinglog\Services\WeatherTelemetryService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CatchLoggedEventPipelineTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function action_dispatches_catch_logged_event_when_record_is_created(): void
    {
        Event::fake([CatchLoggedEvent::class]);

        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create();

        $action = new CreateCatchRecordAction();
        $record = $action->execute([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'length' => 18.5,
            'caught' => '2026-08-15',
        ]);

        Event::assertDispatched(CatchLoggedEvent::class, function (CatchLoggedEvent $event) use ($record) {
            return $event->record->id === $record->id;
        });
    }

    #[Test]
    public function trophy_listener_sends_notification_on_personal_best(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $angler = Angler::factory()->create(['user_id' => $user->id]);
        $breed = FishBreed::factory()->create(['name' => 'Walleye']);
        $lake = Lake::factory()->create(['name' => 'Wawa Lake']);

        // Prior PB: 18.0 inches
        Record::create([
            'anglers_id' => $angler->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'length' => 18.0,
            'caught' => now()->subDays(5),
        ]);

        // New PB: 22.5 inches
        $newRecord = Record::create([
            'anglers_id' => $angler->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'length' => 22.5,
            'caught' => now(),
        ]);

        $event = new CatchLoggedEvent($newRecord);
        $listener = new CheckTrophyMilestoneListener();
        $listener->handle($event);

        Notification::assertSentTo($user, TrophyCatchLogged::class, function (TrophyCatchLogged $notification) use ($newRecord) {
            return $notification->record->id === $newRecord->id
                && $notification->milestone['type'] === 'species_pb';
        });
    }

    #[Test]
    public function cache_listener_invalidates_telemetry_cache(): void
    {
        Cache::put('angler_stats_overview', ['cached_stat' => 123], 3600);

        $record = Record::factory()->create();
        $event = new CatchLoggedEvent($record);

        $listener = new InvalidateTelemetryCacheListener();
        $listener->handle($event);

        $this->assertFalse(Cache::has('angler_stats_overview'));
    }

    #[Test]
    public function weather_listener_invokes_weather_telemetry_service(): void
    {
        $lake = Lake::factory()->create();
        $record = Record::factory()->create([
            'lakes_id' => $lake->id,
            'caught' => '2026-07-20',
        ]);

        $mockWeatherService = Mockery::mock(WeatherTelemetryService::class);
        $mockWeatherService->shouldReceive('fetchForLakeAndDate')
            ->once()
            ->with(Mockery::on(fn($l) => $l->id === $lake->id), Mockery::any())
            ->andReturn(null);

        $listener = new FetchCatchWeatherListener($mockWeatherService);
        $listener->handle(new CatchLoggedEvent($record));
    }

    #[Test]
    public function quick_catch_modal_executes_action_and_sets_trophy_milestone(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $angler = Angler::factory()->create(['user_id' => $user->id]);
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create(['name' => 'Smallmouth Bass']);

        $component = Livewire::test(QuickCatchModal::class)
            ->set('isOpen', true)
            ->set('anglers_id', $angler->id)
            ->set('lakes_id', $lake->id)
            ->set('fish_breeds_id', $breed->id)
            ->set('length', 20.5)
            ->set('caught', now()->toDateString())
            ->call('save')
            ->assertSet('statusType', 'success');

        $this->assertNotNull($component->get('trophyMilestone'));
    }
}
