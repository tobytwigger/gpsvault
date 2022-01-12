<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\ActivityStats;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityStatsFactory extends Factory
{

    protected $model = ActivityStats::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'distance' => $this->faker->randomFloat(2, 1, 100),
            'started_at' => $this->faker->dateTimeBetween('-1 year, -1 day'),
            'finished_at' => $this->faker->dateTimeBetween('now'),
            'duration' => $this->faker->randomFloat(2, 1, 100),
            'average_speed' => $this->faker->randomFloat(2, 1, 100),
            'average_pace' => $this->faker->randomFloat(2, 1, 100),
            'min_altitude' => $this->faker->randomFloat(2, 1, 100),
            'max_altitude' => $this->faker->randomFloat(2, 1, 100),
            'elevation_gain' => $this->faker->randomFloat(2, 1, 100),
            'elevation_loss' => $this->faker->randomFloat(2, 1, 100),
            'moving_time' => $this->faker->randomFloat(2, 1, 100),
            'activity_id' => fn() => Activity::factory()->create()->id,
            'integration' => $this->faker->unique()->word,
        ];
    }
}
