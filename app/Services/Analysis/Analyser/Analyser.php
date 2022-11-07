<?php

namespace App\Services\Analysis\Analyser;

use App\Models\File;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Analysis analyse(File $file) Analyse a file
 * @method static Analysis runAnalysis(Analysis $analysis) Run an analysis on an existing analysis model
 * @method static AnalysisFactoryContract registerAnalyser(string $class) Register a new analyser type
 */
class Analyser extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AnalysisFactoryContract::class;
    }
}
