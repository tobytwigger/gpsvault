<?php

namespace App\Services\Routing;

class RouteResult
{
    private array $waypoints;
    private float $distance;
    private float $time;
    private float $elevation;
    private array $waypointDistance;
    private array $waypointTime;

    public function __construct(array $waypoints = [], float $distance = 0.0, float $time = 0.0, float $elevation = 0.0, array $waypointDistance, array $waypointTime)
    {
        $this->waypoints = $waypoints;
        $this->distance = $distance;
        $this->time = $time;
        $this->elevation = $elevation;
        $this->waypointDistance = $waypointDistance;
        $this->waypointTime = $waypointTime;
    }

    public function toArray()
    {
        return [
            'coordinates' => $this->waypoints,
            'distance' => $this->distance,
            'time' => $this->time,
            'elevation' => $this->elevation,
            'waypoint_distance' => $this->waypointDistance,
            'waypoint_time' => $this->waypointTime
        ];
    }
}
