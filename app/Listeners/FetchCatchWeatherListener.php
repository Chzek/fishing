<?php

namespace Fishinglog\Listeners;

use Fishinglog\Events\CatchLoggedEvent;
use Fishinglog\Services\WeatherTelemetryService;
use Illuminate\Support\Facades\Log;

class FetchCatchWeatherListener
{
    /**
     * Create the event listener.
     */
    public function __construct(
        public WeatherTelemetryService $weatherService
    ) {}

    /**
     * Handle the event.
     *
     * @param CatchLoggedEvent $event
     * @return void
     */
    public function handle(CatchLoggedEvent $event): void
    {
        try {
            $record = $event->record;
            if ($record->lake && $record->caught) {
                $this->weatherService->fetchForLakeAndDate($record->lake, $record->caught);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to fetch weather telemetry in FetchCatchWeatherListener: ' . $e->getMessage());
        }
    }
}
