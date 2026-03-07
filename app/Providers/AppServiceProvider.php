<?php

namespace Fishinglog\Providers;

use Fishinglog\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
// use Laravel\Pulse\Facades\Pulse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Paginator::useBootstrap();

    // TODO: Uncomment when laravel/pulse is installed
    // Pulse::users(function ($ids) {
    //     return User::findMany($ids)->map(fn ($user) => [
    //         'id' => $user->id,
    //         'name' => $user->name,
    //         'extra' => $user->email,
    //         'avatar' => $user->avatar,
    //     ]);
    // });
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
