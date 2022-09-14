<?php

namespace App\Jobs;

use App\Models\Route;
use App\Models\RoutePathWaypoint;
use App\Models\Waypoint;
use App\Services\Analysis\Analyser\Analyser;
use App\Services\Analysis\Parser\Point;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Point as PostgisPoint;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AnalyseRouteFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Route $route;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(Route $route)
    {
        $this->queue = 'stats';
        $this->route = $route;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if ($this->route->file_id === null) {
            throw new NotFoundHttpException(sprintf('Route %u does not have a file associated with it.', $this->route->id));
        }
        $analysis = Analyser::analyse($this->route->file);

        $linestring = new LineString(
            collect($analysis->getPoints())
                ->map(fn (Point $point) => new PostgisPoint($point->getLatitude(), $point->getLongitude(), $point->getElevation()))
                ->all()
        );

        $routePath = $this->route->routePaths()->create([
            'linestring' => $linestring,
            'distance' => $analysis->getDistance(),
            'elevation_gain' => $analysis->getCumulativeElevationGain(),
            'duration' => $this->getDuration(),
        ]);

        $ids = [];
        foreach (collect($analysis->getPoints()) as $point) {
            $waypoint = Waypoint::create([
                'location' => new \MStaack\LaravelPostgis\Geometries\Point($point->getLatitude(), $point->getLongitude())
            ]);
            $rpw = RoutePathWaypoint::create([
                'route_path_id' => $routePath->id,
                'waypoint_id' => $waypoint->id
            ]);
            $ids[] = $rpw->id;
        }
        RoutePathWaypoint::setNewOrder($ids);
    }

    private function getDuration(): int
    {
        return 0;
    }
}
