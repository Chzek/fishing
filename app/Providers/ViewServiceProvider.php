<?php

namespace Fishinglog\Providers;

use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer(
            ['record.create', 'record.edit'],
            \Fishinglog\Http\View\Composers\RecordFormComposer::class
        );

        \Illuminate\Support\Facades\View::composer(
            ['fish.breed.create', 'fish.breed.edit'],
            \Fishinglog\Http\View\Composers\FishBreedFormComposer::class
        );

        \Illuminate\Support\Facades\View::composer(
            ['expedition.crew.create'],
            \Fishinglog\Http\View\Composers\CrewFormComposer::class
        );

        \Illuminate\Support\Facades\View::composer(
            ['angler.create', 'angler.edit'],
            \Fishinglog\Http\View\Composers\AnglerFormComposer::class
        );
    }
}
