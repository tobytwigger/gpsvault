<?php

namespace App\Services\Analysis\Analyser;

use Illuminate\Support\Facades\Facade;

class Analyser extends Facade
{

    protected static function getFacadeAccessor()
    {
        return AnalysisFactoryContract::class;
    }

}
