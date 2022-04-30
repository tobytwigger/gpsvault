<?php

namespace Database\Factories;

use App\Models\User;
use App\Services\Sync\Sync;
use Illuminate\Database\Eloquent\Factories\Factory;

class SyncFactory extends Factory
{
    protected $model = Sync::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startedAt = $this->faker->dateTimeBetween('-1 year', '-1 hour');

        return [
            'user_id' => User::factory(),
            'started_at' => $startedAt,
            'finished_at' => $startedAt->add(\DateInterval::createFromDateString(sprintf('%u seconds', $this->faker->numberBetween(1, 120)))),
        ];
    }
}
