<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\ActivityStats;
use App\Models\Stats;
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
            'file_id' => null,
            'linked_to' => [],
            'user_id' => fn() => User::factory()
        ];
    }
}
