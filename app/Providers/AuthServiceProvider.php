<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(fn ($user) => $user->can('superadmin') ? true : null);

        Gate::define('viewLarecipe', function ($user = null, $documentation = null) {
            if ($user === null || $documentation === null) {
                return true;
            }
            if ($documentation->title === 'Logging In Clients' && !$user->can('manage-strava-clients')) {
                return false;
            }

            return true;
        });
    }
}
