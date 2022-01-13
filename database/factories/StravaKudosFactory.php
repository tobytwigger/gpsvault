<?php

namespace Database\Factories;

use App\Integrations\Strava\Models\StravaKudos;
use App\Models\Activity;
use App\Models\ActivityStats;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StravaKudosFactory extends Factory
{
    protected $model = StravaKudos::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'activity_id' => fn() => Activity::factory()
        ];
    }
}
