<?php

namespace Database\Factories;

use App\Integrations\Strava\Models\StravaClient;
use App\Integrations\Strava\StravaToken;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StravaTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StravaToken::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'expires_at' => $this->faker->dateTimeBetween('now', '+6 hours'),
            'refresh_token' => Str::random(15),
            'access_token' => Str::random(15),
            'user_id' => fn () => User::factory(),
            'strava_client_id' => fn() => StravaClient::factory(),
            'disabled' => false
        ];
    }
}
