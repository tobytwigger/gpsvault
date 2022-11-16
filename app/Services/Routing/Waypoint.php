<?php

namespace App\Services\Routing;

class Waypoint
{
    private float $latitude;

    private float $longitude;

    public function __construct(float $latitude, float $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getLat()
    {
        return $this->latitude;
    }

    public function getLng()
    {
        return $this->longitude;
    }
}
