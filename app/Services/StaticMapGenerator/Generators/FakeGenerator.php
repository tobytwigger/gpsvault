<?php

namespace App\Services\StaticMapGenerator\Generators;

use App\Services\StaticMapGenerator\Generator;
use MStaack\LaravelPostgis\Geometries\LineString;

class FakeGenerator implements Generator
{
    public function ofPath(LineString $lineString): string
    {
        return '';
    }
}
