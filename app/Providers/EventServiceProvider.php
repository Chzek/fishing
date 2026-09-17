<?php

namespace Fishinglog\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \Fishinglog\Events\CatchLoggedEvent::class => [
            \Fishinglog\Listeners\CheckTrophyMilestoneListener::class,
            \Fishinglog\Listeners\InvalidateTelemetryCacheListener::class,
            \Fishinglog\Listeners\FetchCatchWeatherListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
