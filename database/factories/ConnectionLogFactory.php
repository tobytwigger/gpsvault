<?php

namespace Database\Factories;

use App\Models\ConnectionLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConnectionLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ConnectionLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement([
                ConnectionLog::ERROR,
                ConnectionLog::WARNING,
                ConnectionLog::INFO,
                ConnectionLog::DEBUG,
                ConnectionLog::SUCCESS,
            ]),
            'log' => $this->faker->sentence,
            'user_id' => fn () => User::factory(),
            'integration' => $this->faker->word
        ];
    }
}
