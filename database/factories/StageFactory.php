<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Route;
use App\Models\Stage;
use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

class StageFactory extends Factory
{
    protected $model = Stage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph,
            'date' => null,
            'is_rest_day' => false,
            'tour_id' => fn () => Tour::factory(),
            'route_id' => fn () => Route::factory(),
            'activity_id' => fn () => Activity::factory(),
        ];
    }

    public function tour(Tour $tour)
    {
        return $this->state(fn (array $attributes) => [
            'tour_id' => $tour->id,
        ]);
    }

    public function route(Route $route)
    {
        return $this->state(fn (array $attributes) => [
            'route_id' => $route->id,
        ]);
    }

    public function activity(Activity $activity)
    {
        return $this->state(fn (array $attributes) => [
            'activity_id' => $activity->id,
        ]);
    }
}
