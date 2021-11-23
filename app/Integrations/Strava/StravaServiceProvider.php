<?php

namespace App\Integrations\Strava;

use App\Integrations\Strava\Http\Controllers\StravaController;
use App\Integrations\Integration;
use App\Integrations\Strava\Http\Controllers\StravaFixController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class StravaServiceProvider extends ServiceProvider
{

    public function register()
    {
        //
    }

    public function boot()
    {
        Integration::registerIntegration('strava', StravaIntegration::class);

        Route::middleware(['web', 'auth:sanctum', 'verified'])->prefix('strava')->group(function() {
            Route::get('login', [StravaController::class, 'login'])->name('strava.login');
            Route::get('callback', [StravaController::class, 'callback'])->name('strava.callback');
            Route::post('fix', [StravaFixController::class, 'fix'])->name('strava.fix');
        });
    }

}
