<?php

namespace Tests\Unit\Services\Geocoding;

use App\Services\Geocoding\Geocoder;
use App\Services\Geocoding\GeocoderCache;
use Illuminate\Contracts\Cache\Repository;
use Tests\TestCase;

class GeocoderCacheTest extends TestCase
{
    /** @test */
    public function it_returns_the_cached_version_if_in_cache()
    {
        $baseGeocoder = $this->prophesize(Geocoder::class);
        $baseGeocoder->getPlaceSummaryFromPosition(55, 33)->shouldNotBeCalled();

        $cache = $this->prophesize(Repository::class);
        $cache->has('getPlaceSummaryFromPosition@55:33')->willReturn(true);
        $cache->get('getPlaceSummaryFromPosition@55:33')->willReturn('Milton Keynes, Bucks');

        $geocoderCache = new GeocoderCache($baseGeocoder->reveal(), $cache->reveal());
        $this->assertEquals('Milton Keynes, Bucks', $geocoderCache->getPlaceSummaryFromPosition(55, 33));
    }

    /** @test */
    public function it_saves_the_information_forever_when_not_in_cache()
    {
        $baseGeocoder = $this->prophesize(Geocoder::class);
        $baseGeocoder->getPlaceSummaryFromPosition(55, 33)->shouldBeCalled()->willReturn('Milton Keynes, Bucks');

        $cache = $this->prophesize(Repository::class);
        $cache->has('getPlaceSummaryFromPosition@55:33')->willReturn(false);
        $cache->forever('getPlaceSummaryFromPosition@55:33', 'Milton Keynes, Bucks')->shouldBeCalled();

        $geocoderCache = new GeocoderCache($baseGeocoder->reveal(), $cache->reveal());
        $this->assertEquals('Milton Keynes, Bucks', $geocoderCache->getPlaceSummaryFromPosition(55, 33));
    }
}
