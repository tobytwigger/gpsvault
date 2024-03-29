<?php

namespace Feature\Place;

use App\Jobs\CreateThumbnailImage;
use App\Jobs\GenerateRouteThumbnail;
use App\Models\Place;
use App\Models\Route;
use App\Models\RoutePath;
use App\Models\RoutePathWaypoint;
use App\Models\Waypoint;
use Illuminate\Support\Facades\Bus;
use MStaack\LaravelPostgis\Geometries\Point;
use Tests\TestCase;

class PlaceSearchTest extends TestCase
{
    /** @test */
    public function it_filters_by_those_not_part_of_the_given_route_id()
    {
        Bus::fake([CreateThumbnailImage::class, GenerateRouteThumbnail::class]);
        $this->authenticated();

        $place1 = Place::factory()->create(['name' => 'A']);
        $place2 = Place::factory()->create(['name' => 'B']);
        $place3 = Place::factory()->create(['name' => 'C']);
        $place4 = Place::factory()->create(['name' => 'D']);
        $place5 = Place::factory()->create(['name' => 'E']);

        $route = Route::factory()->create();
        $routePath = RoutePath::factory()->create(['route_id' => $route->id]);
        $waypoint2 = Waypoint::factory()->place($place2)->create();
        $waypoint5 = Waypoint::factory()->place($place5)->create();
        RoutePathWaypoint::factory()->create(['waypoint_id' => $waypoint2->id, 'route_path_id' => $routePath->id]);
        RoutePathWaypoint::factory()->create(['waypoint_id' => $waypoint5->id, 'route_path_id' => $routePath->id]);

        $response = $this->getJson(route('place.search', ['exclude_route_id' => $route->id]));
        $response->assertOk();
        $places = $response->decodeResponseJson()->json('data');
        $this->assertCount(3, $places);

        $this->assertEquals($place1->id, $places[0]['id']);
        $this->assertEquals($place3->id, $places[1]['id']);
        $this->assertEquals($place4->id, $places[2]['id']);
    }

    /** @test */
    public function it_filters_on_bounds_given()
    {
        Bus::fake([CreateThumbnailImage::class, GenerateRouteThumbnail::class]);
        $this->authenticated();


        $place1 = Place::factory()->create(['name' => 'A', 'location' => new Point(-3.52, 50.702)]); // Exeter
        $place2 = Place::factory()->create(['name' => 'B', 'location' => new Point(-5.009, 50.208)]); // Falmouth
        $place3 = Place::factory()->create(['name' => 'C', 'location' => new Point(-2.56, 51.418849)]); // Bristol
        $place4 = Place::factory()->create(['name' => 'D', 'location' => new Point(-0.7308, 52.014)]); // MK
        $place5 = Place::factory()->create(['name' => 'E', 'location' => new Point(-1.109, 52.618)]); // Leicester

        $route = Route::factory()->create();
        $routePath = RoutePath::factory()->create(['route_id' => $route->id]);
        $waypoint2 = Waypoint::factory()->place($place2)->create();
        $waypoint5 = Waypoint::factory()->place($place5)->create();
        RoutePathWaypoint::factory()->create(['waypoint_id' => $waypoint2->id, 'route_path_id' => $routePath->id]);
        RoutePathWaypoint::factory()->create(['waypoint_id' => $waypoint5->id, 'route_path_id' => $routePath->id]);

        $response = $this->getJson(route('place.search', [
            'southwest_lng' => 49.893,
            'southwest_lat' => -3.895,
            'northeast_lng' => 52.3142,
            'northeast_lat' => 1.0599,
        ]));

        $places = $response->decodeResponseJson()->json('data');
        $this->assertCount(3, $places);

        $this->assertEquals($place1->id, $places[0]['id']);
        $this->assertEquals($place3->id, $places[1]['id']);
        $this->assertEquals($place4->id, $places[2]['id']);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $route = Route::factory()->create();

        $response = $this->getJson(route('place.search'), ['route_id' => $route->id]);
        $response->assertStatus(401);
    }
}
