<?php

return [
    'table_prefix' => 'job_status',
    'routes' => [
        'api' => [
            'prefix' => '_api',
            'enabled' => true,
            'middleware' => ['web'],
        ],
    ],
];
