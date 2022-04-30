<?php

namespace App\Providers;

use App\Settings\BruitAPIKey;
use App\Settings\DarkMode;
use App\Settings\StatsOrder;
use App\Settings\StravaClient;
use App\Settings\UnitSystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Settings\Setting;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Storage::extend('external', function ($app, $config) {
            // Get the preferred disk, one that has space on it?

            return Storage::disk('s3');
        });

        Setting::register(new UnitSystem());
        Setting::register(new DarkMode());
        Setting::register(new StravaClient());
        Setting::register(new StatsOrder());
        Setting::register(new BruitAPIKey());
    }
}
