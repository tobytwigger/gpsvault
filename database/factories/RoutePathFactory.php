<?php

namespace Database\Factories;

use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Point;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoutePath>
 */
class RoutePathFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'linestring' => new LineString(collect()->range(1, 6)->map(fn () => new Point($this->faker->latitude, $this->faker->longitude, $this->faker->numberBetween(0, 500)))->all()),
            'distance' => $this->faker->randomFloat(),
            'elevation_gain' => $this->faker->randomFloat(),
            'duration' => $this->faker->randomFloat(),
            'route_id' => Route::factory(),
        ];
    }

    public function route(Route $route)
    {
        return $this->state(fn ($attributes) => [
            'route_id' => $route->id,
        ]);
    }
}
