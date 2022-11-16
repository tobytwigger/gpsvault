<?php

namespace App\Services\Routing;

use Illuminate\Support\Facades\Facade;

class Router extends Facade
{
    protected static function getFacadeAccessor()
    {
        return RouterManager::class;
    }
}
