<?php

namespace Feature\Route\Planner;

use App\Models\Route;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Point;
use Tests\TestCase;

class PlannerStoreTest extends TestCase
{

    /** @test */
    public function it_adds_a_route_data()
    {
        $this->authenticated();

        $response = $this->post(route('planner.store', [
            'name' => 'My Route',
            'geojson' => [
                ['lat' => 55, 'lng' => 22],
                ['lat' => 56, 'lng' => 21],
                ['lat' => 57, 'lng' => 20],
            ],
            'points' => [
                ['lat' => 55, 'lng' => 22],
                ['lat' => 57, 'lng' => 20],
            ],
        ]));

        $response->assertRedirect();

        $this->assertDatabaseHas('routes', [
            'name' => 'My Route',
        ]);
        $route = Route::firstOrFail();
        $routePath = $route->routePaths()->firstOrFail();
        $this->assertEquals(new LineString([
            new Point(55, 22), new Point(56, 21), new Point(57, 20),
        ]), $routePath->linestring);

        $routePoints = $route->path->routePoints()->get();
        $this->assertCount(2, $routePoints);
        $this->assertEquals(new Point(55, 22), $routePoints[0]->location);
        $this->assertEquals(new Point(57, 20), $routePoints[1]->location);
    }
}
