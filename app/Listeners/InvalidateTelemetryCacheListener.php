<?php

namespace Fishinglog\Listeners;

use Fishinglog\Events\CatchLoggedEvent;
use Fishinglog\Services\CatchTelemetryService;
use Illuminate\Support\Facades\Cache;

class InvalidateTelemetryCacheListener
{
    /**
     * Handle the event.
     *
     * @param CatchLoggedEvent $event
     * @return void
     */
    public function handle(CatchLoggedEvent $event): void
    {
        Cache::forget('angler_stats_overview');
        CatchTelemetryService::clearCache();
    }
}
