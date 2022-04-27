<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;

class Energy extends AnalyserContract
{
    public function canRun(Analysis $analysis): bool
    {
        return false;
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        return $analysis;
    }
}
