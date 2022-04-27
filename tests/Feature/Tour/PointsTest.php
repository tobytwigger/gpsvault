<?php

namespace Tests\Feature\Tour;

use App\Models\Route;
use App\Models\Stage;
use App\Models\Stats;
use App\Models\Tour;
use App\Models\User;
use App\Services\Analysis\Parser\Point;
use Tests\TestCase;

class PointsTest extends TestCase
{

    /** @test */
    public function it_returns_an_array_of_points_for_each_stage()
    {
        $this->authenticated();
        $route = $this->createNewRoute([[1,50],[2,51],[3,52],[4,53]]);
        $route2 = $this->createNewRoute([[5,54],[6,55],[7,56],[8,57]]);
        $route3 = $this->createNewRoute([[9,58],[10,59]]);

        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        Stage::factory()->create(['route_id' => $route->id, 'tour_id' => $tour->id]);
        Stage::factory()->create(['route_id' => $route2->id, 'tour_id' => $tour->id]);
        Stage::factory()->create(['route_id' => $route3->id, 'tour_id' => $tour->id]);

        $response = $this->getJson(route('tour.points', $tour));
        $response->assertJson([
            ['latitude' => 1, 'longitude' => 50],
            ['latitude' => 2, 'longitude' => 51],
            ['latitude' => 3, 'longitude' => 52],
            ['latitude' => 4, 'longitude' => 53],
            ['latitude' => 5, 'longitude' => 54],
            ['latitude' => 6, 'longitude' => 55],
            ['latitude' => 7, 'longitude' => 56],
            ['latitude' => 8, 'longitude' => 57],
            ['latitude' => 9, 'longitude' => 58],
            ['latitude' => 10, 'longitude' => 59],
        ]);
    }

    private function createNewRoute(array $coords)
    {
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $stats = Stats::factory()->route($route)->create();

        $points = array_map(fn (array $coord) => (new Point())->setLatitude($coord[0])->setLongitude($coord[1]), $coords);
        $stats->waypoints()->createMany(collect($points)->map(fn (Point $point) => [
            'points' => new \MStaack\LaravelPostgis\Geometries\Point($point->getLatitude(), $point->getLongitude()),
        ]));

        return $route;
    }

    /** @test */
    public function it_ignores_any_stage_without_a_route()
    {
        $this->authenticated();
        $route = $this->createNewRoute([[1,50],[2,51],[3,52],[4,53]]);
        $route2 = $this->createNewRoute([[5,54],[6,55],[7,56],[8,57]]);
        $route3 = $this->createNewRoute([[9,58],[10,59]]);

        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        Stage::factory()->create(['route_id' => $route->id, 'tour_id' => $tour->id]);
        Stage::factory()->create(['route_id' => null, 'tour_id' => $tour->id]);
        Stage::factory()->create(['route_id' => $route2->id, 'tour_id' => $tour->id]);
        Stage::factory()->create(['route_id' => null, 'tour_id' => $tour->id]);
        Stage::factory()->create(['route_id' => $route3->id, 'tour_id' => $tour->id]);
        Stage::factory()->create(['route_id' => null, 'tour_id' => $tour->id]);
        Stage::factory()->create(['route_id' => null, 'tour_id' => $tour->id]);

        $response = $this->getJson(route('tour.points', $tour));
        $response->assertJson([
            ['latitude' => 1, 'longitude' => 50],
            ['latitude' => 2, 'longitude' => 51],
            ['latitude' => 3, 'longitude' => 52],
            ['latitude' => 4, 'longitude' => 53],
            ['latitude' => 5, 'longitude' => 54],
            ['latitude' => 6, 'longitude' => 55],
            ['latitude' => 7, 'longitude' => 56],
            ['latitude' => 8, 'longitude' => 57],
            ['latitude' => 9, 'longitude' => 58],
            ['latitude' => 10, 'longitude' => 59],
        ]);
    }

    /** @test */
    public function it_returns_an_empty_array_if_no_stages_have_a_route()
    {
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        Stage::factory()->create(['route_id' => null, 'tour_id' => $tour->id]);
        Stage::factory()->create(['route_id' => null, 'tour_id' => $tour->id]);
        Stage::factory()->create(['route_id' => null, 'tour_id' => $tour->id]);

        $response = $this->getJson(route('tour.points', $tour));
        $json = $response->decodeResponseJson();
        $json->assertExact([]);
    }

    /** @test */
    public function you_can_only_access_your_tours()
    {
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => User::factory()->create()->id]);

        $response = $this->getJson(route('tour.points', $tour));
        $response->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $tour = Tour::factory()->create();

        $response = $this->getJson(route('tour.points', $tour));
        $response->assertStatus(401);
    }
}
