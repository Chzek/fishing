<?php

namespace Fishinglog\Providers;

use Fishinglog\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || request()->header('X-Forwarded-Proto') === 'https' || str_starts_with(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Paginator::useTailwind();

        // Disable Debugbar on mobile devices and boat field catch route
        if (class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class)) {
            $userAgent = request()->header('User-Agent', '');
            $isMobile = preg_match('/(android|bb\d+|meego).+mobile|blackberry|iphone|ipod|opera mini|iemobile|mobile/i', $userAgent)
                || request()->is('record/quick');

            if ($isMobile) {
                \Barryvdh\Debugbar\Facades\Debugbar::disable();
            }
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
