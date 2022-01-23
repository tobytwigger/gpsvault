<?php

namespace App\Services\Geocoding\Nominatim;

use App\Services\Geocoding\Geocoder;
use Illuminate\Contracts\Cache\Repository;

class GeocoderRateLimiting implements Geocoder
{

    private Geocoder $geocoder;

    private Repository $cache;

    const CACHE_KEY = 'nominatim-usage';

    public function __construct(Geocoder $geocoder, Repository $cache)
    {
        $this->geocoder = $geocoder;
        $this->cache = $cache;
    }

    private function getUsage(): int
    {
        return $this->cache->get(static::CACHE_KEY, 0);
    }

    private function isRateLimited(): bool
    {
        return $this->getUsage() > 2;
    }

    private function markAttempt()
    {
        $this->cache->increment(static::CACHE_KEY);
    }

    public function getPlaceSummaryFromPosition(float $latitude, float $longitude): ?string
    {
        if($this->isRateLimited()) {
            return null;
        }
        $result = $this->geocoder->getPlaceSummaryFromPosition($latitude, $longitude);
        $this->markAttempt();
        return $result;
    }
}
