<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\ActivityStats;
use App\Models\File;
use App\Models\Route;
use App\Models\Stats;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatsFactory extends Factory
{

    protected $model = Stats::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'distance' => $this->faker->numberBetween(100, 200000),
            'started_at' => $this->faker->dateTimeBetween('-1 year, -1 day'),
            'finished_at' => $this->faker->dateTimeBetween('now'),
            'duration' => $this->faker->randomFloat(2, 1, 100),
            'average_speed' => $this->faker->randomFloat(2, 1, 100),
            'average_pace' => $this->faker->randomFloat(2, 1, 100),
            'min_altitude' => $this->faker->randomFloat(2, 1, 100),
            'max_altitude' => $this->faker->randomFloat(2, 1, 100),
            'elevation_gain' => $this->faker->randomFloat(2, 1, 100),
            'elevation_loss' => $this->faker->randomFloat(2, 1, 100),
            'max_speed' => $this->faker->randomFloat(2, 1, 100),
            'average_cadence' => $this->faker->randomFloat(2, 1, 100),
            'average_temp' => $this->faker->randomFloat(2, 1, 100),
            'average_watts' => $this->faker->randomFloat(2, 1, 100),
            'kilojoules' => $this->faker->randomFloat(2, 1, 100),
            'start_latitude' => $this->faker->latitude,
            'start_longitude' => $this->faker->longitude,
            'end_latitude' => $this->faker->latitude,
            'end_longitude' => $this->faker->longitude,
            'max_heartrate' => $this->faker->randomFloat(2, 1, 100),
            'average_heartrate' => $this->faker->randomFloat(2, 1, 100),
            'calories' => $this->faker->randomFloat(2, 1, 100),
            'moving_time' => $this->faker->randomFloat(2, 1, 100),
            'integration' => $this->faker->unique()->word,
        ];
    }

    public function activity(Activity $activity = null)
    {
        $activity = $activity ?? Activity::factory()->create();
        return $this->state(fn(array $attributes) => [
            'stats_id' => $activity->id,
            'stats_type' => Activity::class
        ]);
    }

    public function route(Route $route = null)
    {
        $route = $route ?? Route::factory()->create();

        return $this->state(fn(array $attributes) => [
            'stats_id' => $route->id,
            'stats_type' => Route::class
        ]);
    }
}
