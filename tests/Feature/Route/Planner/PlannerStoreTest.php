<?php

namespace Feature\Route\Planner;

use App\Models\Place;
use App\Models\Route;
use App\Models\RoutePoint;
use Illuminate\Support\Str;
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

    /** @test */
    public function it_stores_the_waypoints_of_a_route()
    {
        $this->authenticated();

        $place = Place::factory()->create();
        $response = $this->post(route('planner.store', [
            'name' => 'My Route',
            'geojson' => [
                ['lat' => 55, 'lng' => 22],
                ['lat' => 56, 'lng' => 21],
                ['lat' => 57, 'lng' => 20],
            ],
            'points' => [
                ['lat' => 55, 'lng' => 25],
                ['lat' => 57, 'lng' => 26, 'place_id' => $place->id],
            ],
        ]));

        $response->assertRedirect();

        $this->assertDatabaseHas('routes', [
            'name' => 'My Route',
        ]);
        $route = Route::firstOrFail();
        $points = $route->path->routePoints;

        $this->assertCount(2, $points);
        $this->assertContainsOnlyInstancesOf(RoutePoint::class, $points);

        $this->assertEquals(new Point(55, 25), $points[0]->location);
        $this->assertEquals(new Point(57, 26), $points[1]->location);
        $this->assertNull($points[0]->place_id);
        $this->assertEquals($place->id, $points[1]->place_id);
    }

    /** @test */
    public function it_redirects_to_edit_the_new_route_path()
    {
        $this->authenticated();

        $this->post(route('planner.store', [
            'name' => 'My Route',
            'geojson' => [
                ['lat' => 55, 'lng' => 22], ['lat' => 56, 'lng' => 21],
            ],
        ]))
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

        $response = $this->post(route('planner.store'), array_merge($default, [$key => $value]));

        if (!$error) {
            $response->assertSessionMissing($returnedOverrideKey);
        } else {
            $response->assertSessionHasErrors([$returnedOverrideKey => $error]);
        }
    }

    public function validationDataProvider(): array
    {
        return [
            ['name', Str::random(300), 'The name must not be greater than 255 characters.'],
            ['name', null, 'The name field is required.'],
            ['name', 'This is a valid namne', false],
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
            ['points', [['lat' => 50, 'lng' => 20, 'place_id' => null]], false],
            ['points', fn () => ['lat' => 50, 'lng' => 20, 'place_id' => Place::factory()->create()->id], false],
        ];
    }


    /** @test */
    public function you_must_be_authenticated()
    {
        $this->post(route('planner.store', [
            'name' => 'My Route',
            'geojson' => [
                ['lat' => 55, 'lng' => 22], ['lat' => 56, 'lng' => 21],
            ],
        ]))->assertRedirect(route('login'));
    }
}
