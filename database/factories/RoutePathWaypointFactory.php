<?php

namespace Database\Factories;

use App\Models\RoutePath;
use App\Models\Waypoint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoutePathWaypoint>
 */
class RoutePathWaypointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'route_path_id' => RoutePath::factory(),
            'waypoint_id' => Waypoint::factory(),
        ];
    }
}
