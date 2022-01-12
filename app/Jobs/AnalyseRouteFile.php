<?php

namespace App\Jobs;

use App\Models\Route;
use App\Models\RouteStats;
use App\Services\Analysis\Analyser\Analyser;
use App\Services\File\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AnalyseRouteFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Route $route;

    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Route $route)
    {
        $this->queue = 'stats';
        $this->route = $route;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(!$this->route->routeFile) {
            throw new NotFoundHttpException(sprintf('Route %u does not have a file associated with it', $this->route->id));
        }
        $analysis = Analyser::analyse($this->route->routeFile);

        RouteStats::updateOrCreate(
            ['integration' => 'php', 'route_id' => $this->route->id],
            [
                'distance' => $analysis->getDistance(),
                'elevation_gain' => $analysis->getCumulativeElevationGain(),
                'elevation_loss' => $analysis->getCumulativeElevationLoss(),
                'start_latitude' => $analysis->getStartLatitude(),
                'start_longitude' => $analysis->getStartLongitude(),
                'end_latitude' => $analysis->getEndLatitude(),
                'end_longitude' => $analysis->getEndLongitude(),
                'min_altitude' => $analysis->getMinAltitude(),
                'max_altitude' => $analysis->getMaxAltitude(),
                'json_points_file_id' => Upload::routePoints($analysis->getPoints(), $this->route->user)->id
            ]
        );
    }

    public function middleware()
    {
        return [
            (new WithoutOverlapping(static::class))->releaseAfter(5)
        ];
    }

}
