<?php

namespace Tests\Unit\Models;

use App\Models\Route;
use App\Models\RoutePath;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Point;
use Tests\TestCase;

class RoutePathTest extends TestCase
{
    /** @test */
    public function it_can_be_saved_and_retrieved_in_the_database()
    {
        $route = Route::factory()->create();

        $linestring = new LineString([new Point(1, 2), new Point(3, 4), new Point(5, 6)]);
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
        $this->markTestSkipped('Many-to-many route paths needed');
    }

    /** @test */
    public function it_belongs_to_a_route()
    {
        $route = Route::factory()->create();

        $routePath = RoutePath::factory()->create([
            'route_id' => $route->id,
        ]);

        $this->assertTrue($route->is($routePath->route));
    }
}
