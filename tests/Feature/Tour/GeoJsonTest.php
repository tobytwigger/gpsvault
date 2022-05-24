<?php

namespace Tests\Feature\Tour;

use App\Models\Route;
use App\Models\RoutePath;
use App\Models\Stage;
use App\Models\Tour;
use App\Models\User;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Point;
use Tests\TestCase;

class GeoJsonTest extends TestCase
{
    private function createNewRoute(array $coords)
    {
        $points = array_map(fn (array $coord) => (new Point($coord[0], $coord[1])), $coords);

        $route = Route::factory()
            ->has(RoutePath::factory()->state([
                'linestring' => new LineString($points),
            ]))
            ->create(['user_id' => $this->user->id]);

        return $route;
    }

    /** @test */
    public function you_get_a_list_of_points_with_routes_appended_together()
    {
        $this->authenticated();
        $route = $this->createNewRoute([[1,50],[2,51],[3,52],[4,53]]);
        $route2 = $this->createNewRoute([[5,54],[6,55],[7,56],[8,57]]);
        $route3 = $this->createNewRoute([[9,58],[10,59]]);

        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        Stage::factory()->create(['route_id' => $route->id, 'tour_id' => $tour->id]);
        Stage::factory()->create(['route_id' => $route2->id, 'tour_id' => $tour->id]);
        Stage::factory()->create(['route_id' => $route3->id, 'tour_id' => $tour->id]);

        $response = $this->getJson(route('tour.geojson', $tour));

        $response->assertStatus(200);
        $response->assertJson([
            'type' => 'LineString',
            'coordinates' => [
                [50,1],[51,2],[52,3],[53,4],
                [54,5],[55,6],[56,7],[57,8],
                [58,9],[59,10],
            ],
        ]);
    }

    /** @test */
    public function you_can_only_access_your_tours()
    {
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => User::factory()->create()->id]);

        $response = $this->getJson(route('tour.geojson', $tour));
        $response->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $tour = Tour::factory()->create();

        $response = $this->getJson(route('tour.geojson', $tour));
        $response->assertStatus(401);
    }

    /** @test */
    public function it_returns_an_empty_array_if_no_stages_exist()
    {
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson(route('tour.geojson', $tour));

        $response->assertStatus(200);
        $response->assertJson([
            'type' => 'LineString',
            'coordinates' => [],
        ]);
    }

    /** @test */
    public function it_skips_over_a_stage_missing_a_route()
    {
        $this->authenticated();
        $route = $this->createNewRoute([[1,50],[2,51],[3,52],[4,53]]);
        $route2 = $this->createNewRoute([[5,54],[6,55],[7,56],[8,57]]);
        $route3 = $this->createNewRoute([[9,58],[10,59]]);

        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        Stage::factory()->create(['route_id' => $route->id, 'tour_id' => $tour->id]);
        Stage::factory()->create(['route_id' => $route2->id, 'tour_id' => $tour->id]);
        Stage::factory()->create(['route_id' => null, 'tour_id' => $tour->id]);
        Stage::factory()->create(['route_id' => $route3->id, 'tour_id' => $tour->id]);
        Stage::factory()->create(['route_id' => null, 'tour_id' => $tour->id]);

        $response = $this->getJson(route('tour.geojson', $tour));

        $response->assertStatus(200);
        $response->assertJson([
            'type' => 'LineString',
            'coordinates' => [
                [50,1],[51,2],[52,3],[53,4],
                [54,5],[55,6],[56,7],[57,8],
                [58,9],[59,10],
            ],
        ]);
    }
}
