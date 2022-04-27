<?php

namespace Tests\Unit\Services\Geocoding\Nominatim;

use App\Services\Geocoding\Geocoder;
use App\Services\Geocoding\Nominatim\GeocoderRateLimiting;
use Illuminate\Contracts\Cache\Repository;
use Tests\TestCase;

class GeocoderRateLimitTest extends TestCase
{

    /** @test */
    public function it_allows_two_calls_per_second()
    {
        $geocoder = $this->prophesize(Geocoder::class);
        $geocoder->getPlaceSummaryFromPosition(55, 33)->willReturn('MK, Bucks');

        $limiter = new GeocoderRateLimiting($geocoder->reveal(), app(Repository::class));
        $this->assertEquals('MK, Bucks', $limiter->getPlaceSummaryFromPosition(55, 33));
        $this->assertEquals('MK, Bucks', $limiter->getPlaceSummaryFromPosition(55, 33));
        $this->assertNull($limiter->getPlaceSummaryFromPosition(55, 33));
    }
}
