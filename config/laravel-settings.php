<?php

use App\Settings\BruitAPIKey;
use App\Settings\DarkMode;
use App\Settings\StatsOrder;
use App\Settings\StravaClient;
use App\Settings\UnitSystem;
use Illuminate\Support\Facades\Auth;

return [

    'table' => 'settings',

    'cache' => [
        'ttl' => 3600
    ],

    'encryption' => [
        'default' => false
    ],

    'settings' => [

    ],

    'aliases' => [
        'unit_system' => UnitSystem::class,
        'dark_mode' => DarkMode::class,
        'strava_client_id' => StravaClient::class,
        'stats_order_preference' => StatsOrder::class,
        'bruit_api_key' => BruitAPIKey::class
    ],

    'routes' => [
        'enabled' => true,
        'prefix' => 'api/settings',
        'middleware' => []
    ],

    'js' => [
        'autoload' => [
//            'unit_system',
//            'dark_mode',
//            'stats_order_preference',
//            'bruit_api_key'
        ]
    ]

];
