<?php

namespace App\Services\StaticMapGenerator\Generators;

use App\Services\StaticMapGenerator\Generator;
use MStaack\LaravelPostgis\Geometries\LineString;

class MapBoxApiGenerator implements Generator
{
    public function ofPath(LineString $lineString): string
    {
        $file = file_get_contents(__DIR__ . '/../../../../tests/assets/images/image1.jpeg');
        if ($file === false) {
            throw new \Exception('ERROR');
        }

        return $file;
    }
}
