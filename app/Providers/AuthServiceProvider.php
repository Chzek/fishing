<?php

namespace Fishinglog\Providers;

use Fishinglog\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'Fishinglog\Models\Model' => 'Fishinglog\Policies\ModelPolicy',
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
