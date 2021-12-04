<?php

namespace Database\Factories;

use App\Models\ActivityStats;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
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
            'linked_to' => [],
            'activity_stats_id' => fn() => ActivityStats::factory()->create(),
            'user_id' => fn() => User::factory()->create()
        ];
    }
}
