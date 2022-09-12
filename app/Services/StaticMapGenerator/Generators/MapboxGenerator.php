<?php

namespace App\Services\StaticMapGenerator\Generators;

use App\Services\StaticMapGenerator\Generator;
use App\Services\StaticMapGenerator\GooglePolylineEncoder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use MStaack\LaravelPostgis\Geometries\LineString;

class MapboxGenerator implements Generator
{
    public function ofPath(LineString $lineString): string
    {
        $encoder = new GooglePolylineEncoder();
        foreach($lineString->getPoints() as $point) {
            $encoder->addPoint($point->getLat(), $point->getLng());
        }

        $url = sprintf(
            'https://api.mapbox.com/styles/v1/%s/%s/static/pin-s+9ed4bd(%s),pin-s+050c09(%s),%s/%s/%sx%s?access_token=%s',
            config('services.mapbox.username'),
            config('services.mapbox.style_id'),
            sprintf('%s,%s', Arr::first($lineString->getPoints())->getLng(), Arr::first($lineString->getPoints())->getLat()),
            sprintf('%s,%s', Arr::last($lineString->getPoints())->getLng(), Arr::last($lineString->getPoints())->getLat()),
            sprintf(
                'path-%s+%s-%s(%s)',
                config('services.mapbox.strokeWidth'),
                config('services.mapbox.strokeColor'),
                config('services.mapbox.strokeOpacity'),
                urlencode($encoder->encodedString())
            ),
            config('services.mapbox.position'),
            config('services.mapbox.width'),
            config('services.mapbox.height'),
            config('services.mapbox.key')
        );

        return file_get_contents($url);
    }
}
