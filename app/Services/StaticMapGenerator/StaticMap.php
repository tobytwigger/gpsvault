<?php

namespace App\Services\StaticMapGenerator;

use Illuminate\Support\Facades\Facade;

class StaticMap extends Facade
{
    protected static function getFacadeAccessor()
    {
        return StaticMapManager::class;
    }
}
