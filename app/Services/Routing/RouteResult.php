<?php

namespace App\Services\Routing;

class RouteResult
{
    private array $waypoints;
    private float $distance;
    private float $time;

    public function __construct(array $waypoints = [], float $distance, float $time)
    {
        $this->waypoints = $waypoints;
        $this->distance = $distance;
        $this->time = $time;
    }

    public function toArray()
    {
        return [
            'coordinates' => $this->waypoints,
            'distance' => $this->distance,
            'time' => $this->time,
        ];
    }
}
