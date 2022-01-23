<?php

namespace App\Services\Geocoding;

interface Geocoder
{

    public function getPlaceSummaryFromPosition(float $latitude, float $longitude): ?string;

}
