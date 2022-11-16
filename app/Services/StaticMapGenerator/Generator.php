<?php

namespace App\Services\StaticMapGenerator;

use MStaack\LaravelPostgis\Geometries\LineString;

interface Generator
{
    public function ofPath(LineString $lineString): string;
}
