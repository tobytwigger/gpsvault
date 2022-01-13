<?php

namespace Database\Factories;

use App\Models\ActivityStats;
use App\Models\ConnectionLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DropboxTokensFactory extends Factory
{
    protected $model = ConnectionLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'access_token' => Str::random(30),
            'user_id' => fn() => User::factory()
        ];
    }
}
