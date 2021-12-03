<?php

namespace Database\Factories;

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
            'distance' => $this->faker->numberBetween(10, 200),
            'start_at' => $this->faker->dateTimeBetween('-1 year', '-2 days'),
            'linked_to' => [],
            'user_id' => fn() => User::factory()->create()
        ];
    }
}
