<?php

namespace Unit\Jobs;

use App\Jobs\AnalyseRouteFile;
use App\Models\File;
use App\Models\Route;
use App\Models\RoutePathWaypoint;
use App\Services\Analysis\Analyser\Analyser;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Analyser\AnalysisFactoryContract;
use App\Services\Analysis\Parser\Point;
use MStaack\LaravelPostgis\Geometries\LineString;
use Prophecy\Argument;
use Tests\TestCase;

class AnalyseRouteFileTest extends TestCase
{
    /** @test */
    public function todo_it_spaces_waypoints_appropriately(){
        
    }

    /** @test */
    public function it_throws_an_exception_if_a_route_is_missing_a_file()
    {
        $route = Route::factory()->create(['file_id' => null]);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Route ' . $route->id . ' does not have a file associated with it.');

        $job = new AnalyseRouteFile($route);
        $job->handle();
    }

    /** @test */
    public function it_saves_a_path_for_the_route()
    {
        $route = Route::factory()->create([
            'file_id' => File::factory()->routeFile()->create()->id,
        ]);

        $this->assertDatabaseMissing('route_paths', [
            'route_id' => $route->id,
        ]);

        $job = new AnalyseRouteFile($route);
        $job->handle();

        $this->assertDatabaseHas('route_paths', [
            'route_id' => $route->id,
        ]);
    }

    /** @test */
    public function it_maps_stats_across_to_the_model_correctly()
    {
        $analysisResult = (new Analysis())
            ->setDistance(500.88)
            ->setCumulativeElevationGain(10.4)
            ->setPoints([
                (new Point())->setLatitude(1)->setLongitude(2)->setElevation(10.2),
                (new Point())->setLatitude(3)->setLongitude(4)->setElevation(10.4),
            ]);

        $file = File::factory()->routeFile()->create();
        $route = Route::factory()->create([
            'file_id' => $file->id,
        ]);

        $analyser = $this->prophesize(AnalysisFactoryContract::class);
        $analyser->analyse(Argument::that(fn ($arg) => $file->is($arg)))->shouldBeCalled()->willReturn($analysisResult);
        $this->app->instance(AnalysisFactoryContract::class, $analyser->reveal());
        Analyser::swap($analyser->reveal());

        $job = new AnalyseRouteFile($route);
        $job->handle();

        $this->assertDatabaseHas('route_paths', [
            'route_id' => $route->id,
            'distance' => 500.88,
            'elevation_gain' => 10.4,
        ]);

        $routePoints = RoutePathWaypoint::where('route_path_id', $route->path->id)->ordered()->with('waypoint')->get()->map(fn(RoutePathWaypoint $m) => $m->waypoint);

        $this->assertEquals((new \MStaack\LaravelPostgis\Geometries\Point(1, 2)), $routePoints->shift()->location);
        $this->assertEquals((new \MStaack\LaravelPostgis\Geometries\Point(3, 4)), $routePoints->shift()->location);

        $this->assertEquals(
            new LineString([new \MStaack\LaravelPostgis\Geometries\Point(1, 2, 10.2), new \MStaack\LaravelPostgis\Geometries\Point(3, 4, 10.4)]),
            $route->path->linestring
        );
    }
}
