<?php

namespace Database\Factories;

use App\Models\AdditionalData;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdditionalDataFactory extends Factory
{
    protected $model = AdditionalData::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'key' => $this->faker->word,
            'value' => $this->faker->word
        ];
    }
}
