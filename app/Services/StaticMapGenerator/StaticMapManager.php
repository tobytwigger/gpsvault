<?php

namespace App\Services\StaticMapGenerator;

use App\Services\StaticMapGenerator\Generators\FakeGenerator;
use App\Services\StaticMapGenerator\Generators\MapboxGenerator;
use Illuminate\Support\Manager;

class StaticMapManager extends Manager
{
    public function getDefaultDriver()
    {
        return config('app.static-map.driver', 'mapbox');
    }

    public function createMapboxDriver()
    {
        return new MapboxGenerator();
    }

    public function createFakeDriver()
    {
        return new FakeGenerator();
    }
}
