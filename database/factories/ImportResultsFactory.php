<?php

namespace Database\Factories;

use App\Integrations\Strava\Import\Models\Import;
use App\Integrations\Strava\Import\Models\ImportResult;
use App\Models\ActivityStats;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImportResultsFactory extends Factory
{
    protected $model = ImportResult::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => 'activity',
            'message' => $this->faker->paragraph,
            'success' => $this->faker->boolean,
            'data' => null,
            'import_id' => fn() => Import::factory()
        ];
    }
}
