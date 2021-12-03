<?php

return [
    'client_id' => env('STRAVA_CLIENT_ID'),
    'client_secret' => env('STRAVA_CLIENT_SECRET'),
    'enable_detail_fetching' => env('STRAVA_DETAIL_FETCHING', true)
];
