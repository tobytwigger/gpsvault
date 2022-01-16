<?php

namespace Database\Factories;

use App\Models\Route;
use App\Models\RouteStats;
use Illuminate\Database\Eloquent\Factories\Factory;

class RouteStatsFactory extends Factory
{

    protected $model = RouteStats::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'distance' => $this->faker->numberBetween(100, 200000),
            'min_altitude' => $this->faker->randomFloat(2, 1, 100),
            'max_altitude' => $this->faker->randomFloat(2, 1, 100),
            'elevation_gain' => $this->faker->randomFloat(2, 1, 100),
            'elevation_loss' => $this->faker->randomFloat(2, 1, 100),
            'start_latitude' => $this->faker->latitude,
            'start_longitude' => $this->faker->longitude,
            'end_latitude' => $this->faker->latitude,
            'end_longitude' => $this->faker->longitude,
            'route_id' => fn() => Route::factory(),
            'integration' => $this->faker->unique()->word,
            'json_points_file_id' => null
        ];
    }
}
