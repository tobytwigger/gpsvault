<?php

namespace Tests\Unit\Services\Geocoding\Nominatim;

use App\Services\Geocoding\Nominatim\NominatimGeocoder;
use maxh\Nominatim\Nominatim;
use maxh\Nominatim\Reverse;
use Prophecy\Argument;
use Tests\TestCase;

class NominatimGeocoderTest extends TestCase
{

    /**
     * @test
     * @dataProvider nominatimParsingDataProvider
     * @param mixed $address
     * @param mixed $expected
     */
    public function it_parses_information_correctly($address, $expected)
    {
        $reverse = $this->prophesize(Reverse::class);
        $reverse->latlon(55, 33)->willReturn($reverse->reveal());

        $nominatim = $this->prophesize(Nominatim::class);
        $nominatim->newReverse()->willReturn($reverse->reveal());
        $nominatim->find(Argument::any())->willReturn([
            'address' => $address
        ]);

        $this->app->instance('nominatim', $nominatim->reveal());

        $geocoder = new NominatimGeocoder();
        $this->assertEquals($expected, $geocoder->getPlaceSummaryFromPosition(55, 33));
    }

    public function nominatimParsingDataProvider()
    {
        return [
            [[
                'road' => 'Lennon Drive',
                'suburb' => 'Crownhill',
                'town' => 'Milton Keynes',
                'county' => 'Milton Keynes',
                'state_district' => 'South East England',
                'state' => 'England',
                'postcode' => 'MK8 0AR',
                'country' => 'United Kingdom',
                'country_code' => 'gb',

            ], 'Milton Keynes, Milton Keynes'],
            [[
                'road' => 'Lennon Drive',
                'suburb' => 'Crownhill',
                'town' => 'Milton Keynes',
                'state_district' => 'South East England',
                'state' => 'England',
                'postcode' => 'MK8 0AR',
                'country' => 'United Kingdom',
                'country_code' => 'gb',
            ], 'Milton Keynes, South East England'],
        ];
    }
}

//[
//    'road' => 'Prince Rd',
//    'town' => 'Milton Keynes',
//    'village' => 'Crownhill',
//
//]

//continent
//    country, country_code
//    region, state, state_district, county
//    municipality, city, town, village
//    city_district, district, borough, suburb, subdivision
//    road
