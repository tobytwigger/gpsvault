<?php

namespace Database\Factories;

use App\Integrations\Strava\Models\StravaComment;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

class StravaCommentsFactory extends Factory
{
    protected $model = StravaComment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'strava_id' => $this->faker->numberBetween(1000000, 9999999),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'text' => $this->faker->paragraph,
            'posted_at' => $this->faker->dateTimeBetween('-1 year', '-1 hour'),
            'activity_id' => fn () => Activity::factory(),
        ];
    }
}
