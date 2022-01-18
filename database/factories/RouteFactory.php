<?php

namespace Database\Factories;

use App\Models\ActivityStats;
use App\Models\Route;
use App\Models\Stats;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RouteFactory extends Factory
{
    protected $model = Route::class;

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
            'notes' => $this->faker->paragraph,
            'default_stats_id' => null,
            'file_id' => null,
            'distance' => $this->faker->randomFloat(2, 1, 200000),
            'user_id' => fn() => User::factory()
        ];
    }
}
