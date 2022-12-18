<?php

namespace Tests\Unit\Models;

use App\Jobs\CreateThumbnailImage;
use App\Jobs\GenerateRouteThumbnail;
use App\Models\Route;
use App\Models\RoutePath;
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
        $this->markTestSkipped('Many-to-many route paths needed');
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
}
