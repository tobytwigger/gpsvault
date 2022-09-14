<?php

namespace App\Services\Valhalla;

use Illuminate\Support\Facades\Http;

class Valhalla
{

    public function request(string $method, string $url, array $options = [])
    {
        $fullUrl = 'https://valhalla1.openstreetmap.de' . $url;
        return Http::send($method, $fullUrl, $options);
    }

    public function route(array $locations, array $options = [])
    {
        $defaultOptions = [
            'costing' => 'bicycle'
        ];
        $response = $this->request(
            'GET',
            sprintf('/route?json=%s&api_key=%s',
                json_encode(array_merge($defaultOptions, $options, ['locations' => $locations])),
                '')
        );

        return $response->json();
    }

    public function elevationForLineString(string $encodedPolyline)
    {
        $response = $this->request(
            'GET',
            sprintf('/height?json=%s',
            json_encode([
                'range' => true,
                'encoded_polyline' => $encodedPolyline,
                'shape_format' => 'polyline6'
            ]))
        );

        return $response->json();
    }

}
