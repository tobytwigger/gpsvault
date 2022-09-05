<?php

namespace Tests\Unit\Models;

use App\Models\Place;
use App\Models\RoutePath;
use App\Models\RoutePoint;
use MStaack\LaravelPostgis\Geometries\Point;
use Tests\TestCase;

class RoutePointTest extends TestCase
{
    /** @test */
    public function it_can_be_saved_and_retrieved_in_the_database()
    {
        $routePath = RoutePath::factory()->create();
        $location = new Point(1, 2);
        $place = Place::factory()->create();

        $routePoint = RoutePoint::factory()->create([
            'location' => $location,
            'route_path_id' => $routePath->id,
            'place_id' => $place->id,
        ]);

        $this->assertDatabaseHas('route_points', [
            'id' => $routePoint->id,
            'place_id' => $place->id,
        ]);

        $this->assertEquals($location, RoutePoint::findOrFail($routePoint->id)->location);
    }

    /** @test */
    public function it_belongs_to_many_route_paths()
    {
        $this->markTestSkipped('Many-to-many route paths needed');
    }

    /** @test */
    public function it_can_belong_to_a_place()
    {
        $place = Place::factory()->create();
        $routePoint = RoutePoint::factory()->create(['place_id' => $place->id]);
        $this->assertTrue($place->is($routePoint->place));
    }

    /** @test */
    public function place_is_optional()
    {
        $this->assertNull(RoutePoint::factory()->create(['place_id' => null])->place);
    }
}
