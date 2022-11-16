<?php

namespace App\Services\StaticMapGenerator\Generators;

use App\Services\PolylineEncoder\GooglePolylineEncoder;
use App\Services\StaticMapGenerator\Generator;
use Illuminate\Support\Arr;
use Location\Coordinate;
use Location\Polyline;
use Location\Processor\Polyline\SimplifyBearing;
use Location\Processor\Polyline\SimplifyDouglasPeucker;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Point;

class FakeGenerator implements Generator
{
    public function ofPath(LineString $lineString): string
    {
        return '';
    }
}
