<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Browser;

class DuskServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Browser::macro('docsScreenshot', function (string $name) {
            $this->responsiveScreenshots('../../../resources/images/documentation/' . $name);
            return $this;
        });
    }

}
