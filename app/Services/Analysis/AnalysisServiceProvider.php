<?php

namespace App\Services\Analysis;

use App\Services\Analysis\Analyser\Analyser;
use App\Services\Analysis\Analyser\Analysers\AverageSpeed;
use App\Services\Analysis\Analyser\Analysers\Cadence;
use App\Services\Analysis\Analyser\Analysers\Distance;
use App\Services\Analysis\Analyser\Analysers\Elevation;
use App\Services\Analysis\Analyser\Analysers\Heartrate;
use App\Services\Analysis\Analyser\Analysers\Locations;
use App\Services\Analysis\Analyser\Analysers\MaxSpeed;
use App\Services\Analysis\Analyser\Analysers\MovingTime;
use App\Services\Analysis\Analyser\Analysers\Pace;
use App\Services\Analysis\Analyser\Analysers\Points\CumulativeDistance;
use App\Services\Analysis\Analyser\Analysers\Points\Elevation as PointElevation;
use App\Services\Analysis\Analyser\Analysers\Points\Speed;
use App\Services\Analysis\Analyser\Analysers\Temperature;
use App\Services\Analysis\Analyser\Analysers\TimesAndDurations;
use App\Services\Analysis\Analyser\AnalysisFactory;
use App\Services\Analysis\Analyser\AnalysisFactoryContract;
use App\Services\Analysis\Parser\ParserFactory;
use App\Services\Analysis\Parser\ParserFactoryContract;
use Illuminate\Support\ServiceProvider;

class AnalysisServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(AnalysisFactoryContract::class, AnalysisFactory::class);
        $this->app->singleton(ParserFactoryContract::class, ParserFactory::class);
    }

    public function boot()
    {
        Analyser::registerAnalyser(CumulativeDistance::class)
            ->registerAnalyser(Speed::class)
//            ->registerAnalyser(PointElevation::class)

            ->registerAnalyser(TimesAndDurations::class)
            ->registerAnalyser(Distance::class)
            ->registerAnalyser(MovingTime::class)
            ->registerAnalyser(AverageSpeed::class)
            ->registerAnalyser(Elevation::class)
            ->registerAnalyser(Pace::class)
            ->registerAnalyser(Cadence::class)
            ->registerAnalyser(Heartrate::class)
            ->registerAnalyser(Locations::class)
            ->registerAnalyser(MaxSpeed::class)
            ->registerAnalyser(Temperature::class)
//            ->registerAnalyser(Power::class)
//            ->registerAnalyser(Calories::class)
//            ->registerAnalyser(Energy::class)
        ;
    }
}
