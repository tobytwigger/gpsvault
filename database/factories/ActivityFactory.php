<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\ActivityStats;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->title,
            'description' => $this->faker->paragraph,
            'activity_file_id' => null,
            'distance' => $this->faker->randomFloat(2, 1, 200000),
            'started_at' => $this->faker->dateTimeBetween('-1 year', '-2 hours'),
            'linked_to' => [],
            'user_id' => fn() => User::factory()
        ];
    }
}
