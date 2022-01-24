<?php

namespace App\Services\Geocoding\Nominatim;

use App\Services\Geocoding\Geocoder;
use Illuminate\Contracts\Cache\Repository;

class GeocoderCache implements Geocoder
{

    private Geocoder $geocoder;
    private Repository $cache;

    public function __construct(Geocoder $geocoder, Repository $cache)
    {
        $this->geocoder = $geocoder;
        $this->cache = $cache;
    }

    public function getPlaceSummaryFromPosition(float $latitude, float $longitude): ?string
    {
        $key = sprintf('getPlaceSummaryFromPosition@%s:%s', $latitude, $longitude);
        if($this->cache->has($key)) {
            return $this->cache->get($key);
        }
        $result = $this->geocoder->getPlaceSummaryFromPosition($latitude, $longitude);
        if($result !== null) {
            $this->cache->forever($key, $result);
        }
        return $result;
    }

}
