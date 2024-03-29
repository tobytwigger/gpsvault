<?php

namespace Database\Factories;

use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\Factory;
use MStaack\LaravelPostgis\Geometries\Point;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Waypoint>
 */
class WaypointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'place_id' => Place::factory(),
            'location' => new Point(-0.770416, 52.027825),
            'distance' => $this->faker->randomFloat(),
            'elevation_gain' => $this->faker->randomFloat(),
            'duration' => $this->faker->randomFloat(),
        ];
    }

    public function place(Place $place)
    {
        return $this->state(fn (array $attributes) => [
            'place_id' => $place->id,
        ]);
    }
}
