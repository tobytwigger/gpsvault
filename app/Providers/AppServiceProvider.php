<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use maxh\Nominatim\Nominatim;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('nominatim', fn() => new Nominatim('https://nominatim.openstreetmap.org/'));
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('external', function ($app, $config) {
            // Get the preferred disk, one that has space on it?

            return Storage::disk('s3');
        });
    }
}
