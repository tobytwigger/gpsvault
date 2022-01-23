<?php

namespace App\Services\Geocoding;

use App\Services\Geocoding\Nominatim\GeocoderCache;
use App\Services\Geocoding\Nominatim\GeocoderRateLimiting;
use App\Services\Geocoding\Nominatim\NominatimGeocoder;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\ServiceProvider;
use maxh\Nominatim\Nominatim;

class GeocodingServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton('nominatim', fn() => new Nominatim('https://nominatim.openstreetmap.org/', ['User-Agent' => 'Cycle Store']));

        $this->app->bind(Geocoder::class, NominatimGeocoder::class);
        $this->app->extend(NominatimGeocoder::class, function(NominatimGeocoder $geocoder) {
            $cache = $this->app->make(Repository::class);
            return new GeocoderRateLimiting(
                new GeocoderCache($geocoder, $cache),
                $this->app->make(Repository::class)
            );
        });
    }

}
