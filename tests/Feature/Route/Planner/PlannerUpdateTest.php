<?php

namespace Feature\Route\Planner;

use App\Models\Place;
use App\Models\Route;
use App\Models\RoutePath;
use App\Models\RoutePoint;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Point;
use Tests\TestCase;

class PlannerUpdateTest extends TestCase
{

    /** @test */
    public function it_updates_route_data()
    {
        $this->authenticated();

        $route = Route::factory()->has(RoutePath::factory())->create(['user_id' => $this->user->id]);

        $response = $this->patch(route('planner.update', $route), [
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
        ]);

        $response->assertRedirect();

        $routePath = $route->mainPath()->orderBy('id', 'desc')->first();

        $this->assertEquals(new LineString([
            new Point(55, 22), new Point(56, 21), new Point(57, 20),
        ]), $routePath->linestring);

        $routePoints = $routePath->routePoints()->get();
        $this->assertCount(2, $routePoints);
        $this->assertEquals(new Point(55, 22), $routePoints[0]->location);
        $this->assertEquals(new Point(57, 20), $routePoints[1]->location);
    }

    /** @test */
    public function it_updates_the_waypoints_of_a_route()
    {
        $this->authenticated();

        $place = Place::factory()->create();
        $route = Route::factory()->has(RoutePath::factory())->create(['user_id' => $this->user->id]);
        $path = RoutePath::factory()->create(['route_id' => $route->id]);
        $point = RoutePoint::factory()->create(['route_path_id' => $path]);

        $response = $this->patch(route('planner.update', $route), [
            'geojson' => [
                ['lat' => 55, 'lng' => 22],
                ['lat' => 56, 'lng' => 21],
            ],
            'points' => [
                ['lat' => 55, 'lng' => 25, 'place_id' => $place->id],
                ['lat' => 57, 'lng' => 26],
            ],
        ]);

        $response->assertRedirect();

        $routePath = $route->mainPath()->orderBy('id', 'desc')->first();
        $routePoints = $routePath->routePoints()->get();

        $this->assertCount(2, $routePoints);
        $this->assertEquals(new Point(55, 25), $routePoints[0]->location);
        $this->assertEquals(new Point(57, 26), $routePoints[1]->location);

        $response->assertRedirect();
    }

    /** @test */
    public function it_redirects_to_edit_the_new_route_path()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);

        $this->patch(route('planner.update', $route), [
            'name' => 'My Route',
            'geojson' => [
                ['lat' => 55, 'lng' => 22], ['lat' => 56, 'lng' => 21],
            ],
        ])
            ->assertRedirect(route('planner.edit', Route::first()->id));
    }

    /**
     * @test
     *
     * @dataProvider validationDataProvider
     * @param mixed $key
     * @param mixed $value
     * @param mixed $error
     * @param null|mixed $returnedOverrideKey
     */
    public function it_validates($key, $value, $error, $returnedOverrideKey = null)
    {
        $returnedOverrideKey = $returnedOverrideKey ?? $key;

        $this->authenticated();
        if (is_callable($value)) {
            $value = call_user_func($value, $this->user);
        }

        $default = [
            'name' => 'My Route',
            'geojson' => [
                ['lat' => 55, 'lng' => 22],
                ['lat' => 56, 'lng' => 21],
            ],
            'points' => [
                ['lat' => 55, 'lng' => 22],
                ['lat' => 57, 'lng' => 20],
            ], ];

        $route = Route::factory()->create(['user_id' => $this->user->id]);

        $response = $this->patch(route('planner.update', $route), array_merge($default, [$key => $value]));

        if (!$error) {
            $response->assertSessionMissing($returnedOverrideKey);
        } else {
            $response->assertSessionHasErrors([$returnedOverrideKey => $error]);
        }
    }

    public function validationDataProvider(): array
    {
        return [
            ['geojson', 'This is not an array', 'The geojson must be an array.'],
            ['geojson', [['lat' => 50, 'lng' => 20]], 'The geojson must have at least 2 items.'],
            ['geojson', [['lat' => 50, 'lng' => 20], ['lat' => 'not a number', 'lng' => 20]], 'The geojson.1.lat must be a number.', 'geojson.1.lat'],
            ['geojson', [['lat' => 50, 'lng' => 20], ['lat' => 50, 'lng' => 'not a number']], 'The geojson.1.lng must be a number.', 'geojson.1.lng'],
            ['geojson', [['lat' => null, 'lng' => 20], ['lat' => 10, 'lng' => 20]], 'The geojson.0.lat field is required.', 'geojson.0.lat'],
            ['geojson', [['lat' => 50, 'lng' => null], ['lat' => 20, 'lng' => 20]], 'The geojson.0.lng field is required.', 'geojson.0.lng'],
            ['geojson', [['lat' => -91, 'lng' => 20], ['lat' => 20, 'lng' => 20]], 'The geojson.0.lat must be at least -90.', 'geojson.0.lat'],
            ['geojson', [['lat' => 91, 'lng' => 20], ['lat' => 20, 'lng' => 20]], 'The geojson.0.lat must not be greater than 90.', 'geojson.0.lat'],
            ['geojson', [['lat' => 2, 'lng' => -181], ['lat' => 20, 'lng' => 20]], 'The geojson.0.lng must be at least -180.', 'geojson.0.lng'],
            ['geojson', [['lat' => 2, 'lng' => 181], ['lat' => 20, 'lng' => 20]], 'The geojson.0.lng must not be greater than 180.', 'geojson.0.lng'],
            ['points', null, false],
            ['points', [], false],
            ['points', [['lat' => 50, 'lng' => 20]], false],
            ['points', [['lat' => -91, 'lng' => 20], ['lat' => 20, 'lng' => 20]], 'The points.0.lat must be at least -90.', 'points.0.lat'],
            ['points', [['lat' => 91, 'lng' => 20], ['lat' => 20, 'lng' => 20]], 'The points.0.lat must not be greater than 90.', 'points.0.lat'],
            ['points', [['lat' => 2, 'lng' => -181], ['lat' => 20, 'lng' => 20]], 'The points.0.lng must be at least -180.', 'points.0.lng'],
            ['points', [['lat' => 2, 'lng' => 181], ['lat' => 20, 'lng' => 20]], 'The points.0.lng must not be greater than 180.', 'points.0.lng'],
            ['points', [['lat' => 50, 'lng' => 20, 'place_id' => 500]], 'The selected points.0.place_id is invalid.', 'points.0.place_id'],
            ['points', [['lat' => 50, 'lng' => 20, 'place_id' => 500, 'id' => 5000]], 'The selected points.0.place_id is invalid.', 'points.0.place_id'],
            ['points', [['lat' => 50, 'lng' => 20, 'id' => 5000]], 'The selected points.0.id is invalid.', 'points.0.id'],
            ['points', [['lat' => 50, 'lng' => 20, 'place_id' => null]], false],
            ['points', fn () => ['lat' => 50, 'lng' => 20, 'place_id' => Place::factory()->create()->id], false],
        ];
    }


    /** @test */
    public function you_must_be_authenticated()
    {
        $route = Route::factory()->create();

        $this->patch(route('planner.update', $route), [
            'name' => 'My Route',
            'geojson' => [
                ['lat' => 55, 'lng' => 22], ['lat' => 56, 'lng' => 21],
            ],
        ])->assertRedirect(route('login'));
    }

    /** @test */
    public function you_can_only_update_your_own_routes()
    {
        $this->authenticated();
        $route = Route::factory()->create();

        $this->patch(route('planner.update', $route), [
            'name' => 'My Route',
            'geojson' => [
                ['lat' => 55, 'lng' => 22], ['lat' => 56, 'lng' => 21],
            ],
        ])->assertStatus(403);
    }
}
