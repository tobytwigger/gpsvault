<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Parser\Point;

interface PointAnalyser
{

    public function processPoint(Point $point): void;

}
