<?php

namespace App\Services\Analysis;

use App\Services\Analysis\Analyser\Analyser;
use App\Services\Analysis\Analyser\Analysers\AddPointsToAnalysis;
use App\Services\Analysis\Analyser\Analysers\AverageSpeed;
use App\Services\Analysis\Analyser\Analysers\Distance;
use App\Services\Analysis\Analyser\Analysers\Elevation;
use App\Services\Analysis\Analyser\Analysers\Pace;
use App\Services\Analysis\Analyser\Analysers\TimesAndDurations;
use App\Services\Analysis\Analyser\AnalysisFactory;
use App\Services\Analysis\Analyser\AnalysisFactoryContract;
use App\Services\Analysis\Parser\ParserFactory;
use App\Services\Analysis\Parser\ParserFactoryContract;
use Illuminate\Support\ServiceProvider;
use App\Services\Analysis\Analyser\Analysers\Cadence;
use App\Services\Analysis\Analyser\Analysers\Calories;
use App\Services\Analysis\Analyser\Analysers\Energy;
use App\Services\Analysis\Analyser\Analysers\Heartrate;
use App\Services\Analysis\Analyser\Analysers\Locations;
use App\Services\Analysis\Analyser\Analysers\MaxSpeed;
use App\Services\Analysis\Analyser\Analysers\MovingTime;
use App\Services\Analysis\Analyser\Analysers\Power;
use App\Services\Analysis\Analyser\Analysers\Temperature;

class AnalysisServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(AnalysisFactoryContract::class, AnalysisFactory::class);
        $this->app->singleton(ParserFactoryContract::class, ParserFactory::class);
    }

    public function boot()
    {
        Analyser::registerAnalyser(TimesAndDurations::class)
            ->registerAnalyser(Distance::class)
            ->registerAnalyser(AverageSpeed::class)
            ->registerAnalyser(Elevation::class)
            ->registerAnalyser(Pace::class)
//            ->registerAnalyser(Cadence::class)
//            ->registerAnalyser(Calories::class)
//            ->registerAnalyser(Energy::class)
//            ->registerAnalyser(Heartrate::class)
            ->registerAnalyser(Locations::class)
            ->registerAnalyser(MaxSpeed::class)
//            ->registerAnalyser(MovingTime::class)
//            ->registerAnalyser(Power::class)
//            ->registerAnalyser(Temperature::class)
            ;
    }

}
