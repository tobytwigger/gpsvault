<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use MStaack\LaravelPostgis\Geometries\Point;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Place>
 */
class PlaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'description' => $this->faker->paragraph,
            'type' => $this->faker->randomElement([
                'food_drink', 'shops', 'toilets', 'water', 'tourist', 'accommodation', 'other',
            ]),
            'url' => $this->faker->url,
            'phone_number' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'address' => $this->faker->address,
            'user_id' => User::factory(),
            'location' => new Point(-0.770416, 52.027825),
        ];
    }
}
