<?php

namespace App\Services\Dashboard;

use App\Services\Dashboard\Contracts\DashboardRepository;
use Illuminate\Support\Facades\Facade;

class Dashboard extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DashboardRepository::class;
    }
}
