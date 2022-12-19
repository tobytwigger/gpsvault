<?php

namespace Tests\Unit\Models;

use App\Jobs\CreateThumbnailImage;
use App\Jobs\GenerateRouteThumbnail;
use App\Models\Activity;
use App\Models\Route;
use App\Models\RoutePath;
use App\Models\RoutePathWaypoint;
use App\Models\Stats;
use App\Models\Waypoint;
use App\Services\Geocoding\Geocoder;
use Illuminate\Support\Facades\Bus;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Point;
use Tests\TestCase;

class RoutePathTest extends TestCase
{
    /** @test */
    public function it_can_be_saved_and_retrieved_in_the_database()
    {
        Bus::fake([GenerateRouteThumbnail::class, CreateThumbnailImage::class]);

        $route = Route::factory()->create();

        $linestring = new LineString([new Point(1, 2, 0), new Point(3, 4, 1), new Point(5, 6, 20)]);
        $routePath = RoutePath::factory()->create([
            'linestring' => $linestring,
            'distance' => 20.58,
            'elevation_gain' => 200.22,
            'route_id' => $route->id,
        ]);

        $this->assertDatabaseHas('route_paths', [
            'id' => $routePath->id,
            'distance' => 20.58,
            'elevation_gain' => 200.22,
            'route_id' => $route->id,
        ]);

        $this->assertEquals($linestring, RoutePath::findOrFail($routePath->id)->linestring);
    }

    /** @test */
    public function it_has_many_points()
    {
        Bus::fake([GenerateRouteThumbnail::class, CreateThumbnailImage::class]);

        $routePath = RoutePath::factory()->create();
        $waypoint1 = Waypoint::factory()->create();
        RoutePathWaypoint::factory()->create(['waypoint_id' => $waypoint1->id, 'route_path_id' => $routePath->id]);

        $this->assertCount(1, $routePath->waypoints);
        $this->assertEquals($waypoint1->id, $routePath->waypoints[0]->id);
    }

    /** @test */
    public function it_belongs_to_a_route()
    {
        Bus::fake([GenerateRouteThumbnail::class, CreateThumbnailImage::class]);

        $route = Route::factory()->create();

        $routePath = RoutePath::factory()->create([
            'route_id' => $route->id,
        ]);

        $this->assertTrue($route->is($routePath->route));
    }

    /** @test */
    public function get_human_started_at_gets_the_human_name_for_the_started_at_location()
    {
        Bus::fake([GenerateRouteThumbnail::class, CreateThumbnailImage::class]);

        $linestring = new LineString([new Point(1,2,0),new Point(3,4,0),new Point(5,6,0),new Point(7,8,0)]);
        $routePath = RoutePath::factory()->create(['linestring' => $linestring]);
        $geocoder = $this->prophesize(Geocoder::class);
        $geocoder->getPlaceSummaryFromPosition(1, 2)->willReturn('StartSummary');
        $this->app->instance(Geocoder::class, $geocoder->reveal());

        $this->assertEquals('StartSummary', $routePath->human_started_at);
    }

    /** @test */
    public function get_human_ended_at_gets_the_human_name_for_the_ended_at_location()
    {
        Bus::fake([GenerateRouteThumbnail::class, CreateThumbnailImage::class]);

        $linestring = new LineString([new Point(1,2,0),new Point(3,4,0),new Point(5,6,0),new Point(7,8,0)]);
        $routePath = RoutePath::factory()->create(['linestring' => $linestring]);
        $geocoder = $this->prophesize(Geocoder::class);
        $geocoder->getPlaceSummaryFromPosition(7,8)->willReturn('EndSummary');
        $this->app->instance(Geocoder::class, $geocoder->reveal());

        $this->assertEquals('EndSummary', $routePath->human_ended_at);
    }
}
