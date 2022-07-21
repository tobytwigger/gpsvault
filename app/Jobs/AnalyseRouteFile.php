<?php

namespace App\Jobs;

use App\Models\Route;
use App\Services\Analysis\Analyser\Analyser;
use App\Services\Analysis\Parser\Point;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Point as PostgisPoint;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AnalyseRouteFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Route $model;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(Route $model)
    {
        $this->queue = 'stats';
        $this->model = $model;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if (!$this->model->hasFile()) {
            throw new NotFoundHttpException(sprintf('Route %u does not have a model associated with it.', $this->model->id));
        }
        $analysis = Analyser::analyse($this->model->file);

        $linestring = new LineString(
            collect($analysis->getPoints())
                ->map(fn (Point $point) => new PostgisPoint($point->getLatitude(), $point->getLongitude()))
                ->all()
        );

        $this->model->routePaths()->create([
            'linestring' => $linestring,
            'distance' => $analysis->getDistance(),
            'elevation_gain' => $analysis->getCumulativeElevationGain(),
        ]);
    }

    public function middleware()
    {
        return [
            (new WithoutOverlapping('RouteAnalyser')),
        ];
    }
}
