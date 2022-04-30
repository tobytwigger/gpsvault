<?php

namespace Database\Factories;

use App\Models\Route;
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
            'public' => false,
            'file_id' => null,
            'user_id' => fn () => User::factory(),
        ];
    }
}
