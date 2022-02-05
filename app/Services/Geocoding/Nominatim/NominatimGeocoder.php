<?php

namespace App\Services\Geocoding\Nominatim;

use App\Services\Geocoding\Geocoder;
use Illuminate\Support\Arr;
use maxh\Nominatim\Nominatim;

class NominatimGeocoder implements Geocoder
{

    private function nominatim(): Nominatim
    {
        return app('nominatim');
    }

    public function getPlaceSummaryFromPosition(float $latitude, float $longitude): ?string
    {
        $result = $this->nominatim()->find(
            $this->nominatim()->newReverse()->latlon($latitude, $longitude)
        );
        if(array_key_exists('address', $result)) {
            $address = Arr::only($result['address'], ['town', 'city', 'county', 'state_district', 'state', 'country']);
            return join(', ', array_slice($address, 0, 2));
        }
        return null;
    }
}
