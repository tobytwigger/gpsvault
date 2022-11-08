<?php

namespace App\Services\Analysis;

use App\Services\Analysis\Analyser\Analyser;
use App\Services\Analysis\Analyser\Analysers\AverageHeartrate;
use App\Services\Analysis\Analyser\Analysers\AverageSpeed;
use App\Services\Analysis\Analyser\Analysers\Cadence;
use App\Services\Analysis\Analyser\Analysers\Distance;
use App\Services\Analysis\Analyser\Analysers\Duration;
use App\Services\Analysis\Analyser\Analysers\Elevation;
use App\Services\Analysis\Analyser\Analysers\ElevationGain;
use App\Services\Analysis\Analyser\Analysers\ElevationLoss;
use App\Services\Analysis\Analyser\Analysers\EndLatitude;
use App\Services\Analysis\Analyser\Analysers\EndLongitude;
use App\Services\Analysis\Analyser\Analysers\FinishedAt;
use App\Services\Analysis\Analyser\Analysers\Heartrate;
use App\Services\Analysis\Analyser\Analysers\Locations;
use App\Services\Analysis\Analyser\Analysers\MaxAltitude;
use App\Services\Analysis\Analyser\Analysers\MaxHeartrate;
use App\Services\Analysis\Analyser\Analysers\MaxSpeed;
use App\Services\Analysis\Analyser\Analysers\MinAltitude;
use App\Services\Analysis\Analyser\Analysers\MovingTime;
use App\Services\Analysis\Analyser\Analysers\Pace;
use App\Services\Analysis\Analyser\Analysers\Points\CumulativeDistance;
use App\Services\Analysis\Analyser\Analysers\Points\Elevation as PointElevation;
use App\Services\Analysis\Analyser\Analysers\Points\Speed;
use App\Services\Analysis\Analyser\Analysers\StartedAt;
use App\Services\Analysis\Analyser\Analysers\StartLatitude;
use App\Services\Analysis\Analyser\Analysers\StartLongitude;
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

            ->registerAnalyser(StartedAt::class)
            ->registerAnalyser(FinishedAt::class)
            ->registerAnalyser(Duration::class)
            ->registerAnalyser(Distance::class)
            ->registerAnalyser(MovingTime::class)
            ->registerAnalyser(AverageSpeed::class)
            ->registerAnalyser(ElevationGain::class)
            ->registerAnalyser(ElevationLoss::class)
            ->registerAnalyser(MaxAltitude::class)
            ->registerAnalyser(MinAltitude::class)
            ->registerAnalyser(Pace::class)
            ->registerAnalyser(Cadence::class)
            ->registerAnalyser(MaxHeartrate::class)
            ->registerAnalyser(AverageHeartrate::class)
            ->registerAnalyser(StartLatitude::class)
            ->registerAnalyser(StartLongitude::class)
            ->registerAnalyser(EndLatitude::class)
            ->registerAnalyser(EndLongitude::class)
            ->registerAnalyser(MaxSpeed::class)
            ->registerAnalyser(Temperature::class)
//            ->registerAnalyser(Power::class)
//            ->registerAnalyser(Calories::class)
//            ->registerAnalyser(Energy::class)
        ;
    }
}
