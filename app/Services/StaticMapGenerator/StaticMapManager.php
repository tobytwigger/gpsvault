<?php

namespace App\Services\StaticMapGenerator;

use App\Services\StaticMapGenerator\Generators\MapBoxApiGenerator;
use Illuminate\Support\Manager;

class StaticMapManager extends Manager
{
    public function getDefaultDriver()
    {
        return 'mapbox';
    }

    public function createMapboxDriver()
    {
        return new MapBoxApiGenerator();
    }
}
