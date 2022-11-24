<?php

namespace App\Services\Routing;

class RouteResult
{
    private array $waypoints;
    private float $distance;
    private float $time;
    private float $elevation;

    public function __construct(array $waypoints = [], float $distance = 0.0, float $time = 0.0, float $elevation = 0.0)
    {
        $this->waypoints = $waypoints;
        $this->distance = $distance;
        $this->time = $time;
        $this->elevation = $elevation;
    }

    public function toArray()
    {
        return [
            'coordinates' => $this->waypoints,
            'distance' => $this->distance,
            'time' => $this->time,
            'elevation' => $this->elevation,
        ];
    }
}
