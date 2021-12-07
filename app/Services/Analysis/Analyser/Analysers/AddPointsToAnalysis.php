<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;

class AddPointsToAnalysis extends AnalyserContract
{

    protected function run(Analysis $analysis): Analysis
    {
        return $analysis->setPoints(collect($analysis->getPoints())->all());
    }
}
