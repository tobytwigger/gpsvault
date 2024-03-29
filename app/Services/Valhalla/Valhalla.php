<?php

namespace App\Services\Valhalla;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Valhalla
{
    public function request(string $method, string $url, array $options = [])
    {
        $fullUrl = 'https://valhalla1.openstreetmap.de' . $url;

        $response = Http::send($method, $fullUrl, $options);
        if ($response->failed()) {
            throw new HttpException($response->json('status_code'), sprintf(
                'Error code %s. An error occured planning your route: %s.',
                $response->json('error_code'),
                $response->json('error'),
            ));
        }

        return $response;
    }


//
//dd(Http::get('https://valhalla1.openstreetmap.de/route?json=' . json_encode($options), [
////            'json' =>
//])->toException());
//$response = $this->request(
//'POST',
//sprintf('/route'),
//[
//'query' => json_encode([
//'json' => array_merge($defaultOptions, $options, ['locations' => $locations])
//])
//]
//);
//dd($response);
//if($response->failed()) {
//throw $response->toException();
//}
//return $response->json();

    public function route(array $locations, array $options = [])
    {
        $options = array_merge([
            'costing' => 'bicycle',
        ], $options, ['locations' => $locations]);

        $response = $this->request(
            'GET',
            sprintf('/route?json=%s', urlencode(json_encode($options)))
        );

        return $response->json();
    }

    public function elevationForLineString(string $encodedPolyline)
    {
        $response = $this->request(
            'GET',
            sprintf(
                '/height?json=%s',
                urlencode(json_encode([
                    'range' => true,
                    'encoded_polyline' => $encodedPolyline,
                    'shape_format' => 'polyline6',
                ]))
            )
        );

        return $response->json();
    }

    public function matrix(array $sources, array $targets)
    {
        $response = $this->request(
            'GET',
            sprintf(
                '/sources_to_targets?json=%s',
                urlencode(json_encode([
                    'sources' => $sources,
                    'targets' => $targets,
                    'costing' => 'bicycle',
                ]))
            )
        );

        return $response->json();
    }
}
