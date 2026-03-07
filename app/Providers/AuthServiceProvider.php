<?php

namespace Fishinglog\Providers;

use Fishinglog\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'Fishinglog\Model' => 'Fishinglog\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // Authorization for Pulse
        Gate::define('viewPulse', function (User $user) {
            return $user->isAdmin();
        });
    }
}
