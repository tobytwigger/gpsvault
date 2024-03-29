<?php

namespace App\Jobs;

use App\Models\Route;
use App\Models\RoutePathWaypoint;
use App\Models\Waypoint;
use App\Services\Analysis\Analyser\Analyser;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JobStatus\Concerns\Trackable;
use Location\Coordinate;
use Location\Polyline;
use Location\Processor\Polyline\SimplifyBearing;
use Location\Processor\Polyline\SimplifyDouglasPeucker;
use MStaack\LaravelPostgis\Geometries\LineString;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AnalyseRouteFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

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

    public function alias(): ?string
    {
        return 'analyse-route-file';
    }

    public function tags(): array
    {
        return [
            'route_id' => $this->route->id,
        ];
    }

    public function users(): array
    {
        return [$this->route->user_id];
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
                ->filter(fn (Point $point) => $point->getLatitude() && $point->getLongitude())
                ->map(fn (Point $point) => new \MStaack\LaravelPostgis\Geometries\Point($point->getLatitude(), $point->getLongitude(), $point->getElevation() ?? 0))
                ->all()
        );

        $cumulativeDistance = collect($analysis->getPoints())
            ->map(fn (Point $point) => $point->getCumulativeDistance())
            ->all();

        $routePath = $this->route->routePaths()->create([
            'linestring' => $linestring,
            'distance' => $analysis->getDistance(),
            'elevation_gain' => $analysis->getCumulativeElevationGain(),
            'duration' => $analysis->getDuration() ?? 0,
            'cumulative_distance' => $cumulativeDistance,
        ]);

        $ids = [];
        foreach ($this->getOptimisedPoints($analysis) as $point) {
            $waypoint = Waypoint::create([
                'location' => new \MStaack\LaravelPostgis\Geometries\Point($point->getLat(), $point->getLng()),
                'user_id' => $this->route->user_id,
            ]);
            $rpw = RoutePathWaypoint::create([
                'route_path_id' => $routePath->id,
                'waypoint_id' => $waypoint->id,
            ]);
            $ids[] = $rpw->id;
        }
        RoutePathWaypoint::setNewOrder($ids);
    }

    /**
     * @param Analysis $analysis
     * @return array|Coordinate[]
     */
    private function getOptimisedPoints(Analysis $analysis): array
    {
        $polyline = new Polyline();

        $polyline->addPoints(
            collect($analysis->getPoints())
                ->map(fn (Point $point) => new Coordinate($point->getLatitude(), $point->getLongitude()))
                ->all()
        );

        // Remove any points that don't involve a 20 degree change in direction
        $processor = new SimplifyBearing(20);
        $newPolyline = $processor->simplify($polyline);

        // Remove any points within 20m of another
        $processor = new SimplifyDouglasPeucker(50);

        $newPoints = $processor->simplify($newPolyline)->getPoints();

        $i = 1;
        // Limit to 50 points, all the router can handle
        while (count($newPoints) > 250) {
            unset($newPoints[$i]);
            if ($i === count($newPoints)) {
                $i = 1;
            } else {
                $i++;
            }
        }

        return $newPoints;
    }
}
