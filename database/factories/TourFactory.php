<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TourFactory extends Factory
{
    protected $model = Tour::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => fn () => User::factory(),
            'name' => $this->faker->name,
            'description' => $this->faker->paragraph,
            'notes' => $this->faker->paragraph,
            'marked_as_started_at' => null,
            'marked_as_finished_at' => null,
        ];
    }
}
