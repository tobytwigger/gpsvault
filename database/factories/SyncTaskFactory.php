<?php

namespace Database\Factories;

use App\Services\Sync\Sync;
use App\Services\Sync\SyncTask;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SyncTaskFactory extends Factory
{
    protected $model = SyncTask::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startedAt = $this->faker->dateTimeBetween('-1 year', '-1 hour');

        return [
            'sync_id' => fn () => Sync::factory(),
            'task_id' => Str::random(20),
            'config' => [],
            'status' => 'queued',
            'messages' => [],
            'percentage' => 0,
            'started_at' => $startedAt,
            'finished_at' => $startedAt->add(\DateInterval::createFromDateString(sprintf('%u seconds', $this->faker->numberBetween(1, 120)))),
        ];
    }
}
