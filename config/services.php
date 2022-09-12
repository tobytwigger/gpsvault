<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
     */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'bruit' => [
        'key' => env('BRUIT_API_KEY'),
    ],

    'strava' => [
        'base_url' => 'https://www.strava.com/api/v3/',
    ],

    'mapbox' => [
        'username' => env('MAPBOX_USERNAME'),
        'style_id' => env('MAPBOX_STYLE_ID'),
        'strokeWidth' => 5,
        'strokeColor' => 'f44',
        'strokeOpacity' => 0.5,
        'fillColor' => 'f44',
        'fillOpacity' => 0.5,
        'key' => env('MAPBOX_API_KEY'),
        'position' => 'auto',
        'width' => 344,
        'height' => 200
    ],
];
