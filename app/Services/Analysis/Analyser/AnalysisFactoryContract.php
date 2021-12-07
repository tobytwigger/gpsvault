<?php

namespace App\Services\Analysis\Analyser;

use App\Models\Activity;
use Illuminate\Support\Collection;

interface AnalysisFactoryContract
{

    public function analyse(Activity $activity): Analysis;

    public function runAnalysis(Analysis $analysis): Analysis;

    public function registerAnalyser(string $class): AnalysisFactoryContract;

}
