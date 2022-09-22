<?php

namespace App\Services\StaticMapGenerator\Generators;

use App\Services\PolylineEncoder\GooglePolylineEncoder;
use App\Services\StaticMapGenerator\Generator;
use Illuminate\Support\Arr;
use Location\Coordinate;
use Location\Polyline;
use Location\Processor\Polyline\SimplifyBearing;
use Location\Processor\Polyline\SimplifyDouglasPeucker;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Point;

class MapboxGenerator implements Generator
{
    public function ofPath(LineString $lineString): string
    {
        $polyline = new Polyline();

        $polyline->addPoints(
            collect($lineString->getPoints())
                ->map(fn (Point $point) => new Coordinate($point->getLat(), $point->getLng()))
                ->all()
        );

        // Remove any points that don't involve a 20 degree change in direction
        $processor = new SimplifyBearing(20);
        $newPolyline = $processor->simplify($polyline);

        // Remove any points within 20m of another
        $processor = new SimplifyDouglasPeucker(20);

        $newPoints = $processor->simplify($newPolyline)->getPoints();


        $encoder = new GooglePolylineEncoder();
        foreach ($newPoints as $point) {
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
