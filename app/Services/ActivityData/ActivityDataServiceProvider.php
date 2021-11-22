<?php

namespace App\Services\ActivityData;

use App\Services\ActivityData\Contracts\ActivityDataFactory as ActivityDataFactoryContract;
use Illuminate\Support\ServiceProvider;

class ActivityDataServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(ActivityDataFactoryContract::class, ActivityDataFactory::class);
    }

}
