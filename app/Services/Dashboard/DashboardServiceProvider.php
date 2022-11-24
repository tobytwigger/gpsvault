<?php

namespace App\Services\Dashboard;

use App\Services\Dashboard\Contracts\DashboardRepository;
use App\Services\Dashboard\Database\DashboardDatabaseRepository;
use App\Services\Dashboard\Widgets\RideCount;
use App\Services\Dashboard\Widgets\TotalMileage;
use App\Services\Dashboard\Widgets\TotalTime;
use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(WidgetStore::class);
        $this->app->bind(DashboardRepository::class, DashboardDatabaseRepository::class);
    }

    public function boot()
    {
        app(WidgetStore::class)->pushWidget(TotalMileage::class);
        app(WidgetStore::class)->pushWidget(TotalTime::class);
        app(WidgetStore::class)->pushWidget(RideCount::class);
    }
}
