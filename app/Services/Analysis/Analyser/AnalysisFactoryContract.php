<?php

namespace App\Services\Analysis\Analyser;

use App\Models\File;

interface AnalysisFactoryContract
{

    public function analyse(File $file): Analysis;

    public function runAnalysis(Analysis $analysis): Analysis;

    public function registerAnalyser(string $class): AnalysisFactoryContract;

}
