<?php

return [
    'default' => [
        'yearly', 'monthly', 'all-time'
    ],
    'dashboards' => [
        'yearly' => [
            'name' => 'Yearly stats',
            'description' => 'Information about your year to date',
            'refresh_rate_in_seconds' => 60,
            'widgets' => [
                [
                    'key' => \App\Services\Dashboard\Widgets\TotalMileage::key(),
                    'settings' => ['period' => 'current-year'],
                    'position' => [
                        'x' => 0, 'y' => 0, 'w' => 1, 'h' => 1
                    ]
                ],
                [
                    'key' => \App\Services\Dashboard\Widgets\TotalTime::key(),
                    'settings' => ['period' => 'current-year'],
                    'position' => [
                        'x' => 1, 'y' => 0, 'w' => 1, 'h' => 1
                    ]
                ],
                [
                    'key' => \App\Services\Dashboard\Widgets\RideCount::key(),
                    'settings' => ['period' => 'current-year'],
                    'position' => [
                        'x' => 2, 'y' => 0, 'w' => 1, 'h' => 2
                    ]
                ]
            ]
        ],
        'monthly' => [
            'name' => 'Monthly stats',
            'description' => 'Information about your month to date',
            'refresh_rate_in_seconds' => 60,
            'widgets' => [
                [
                    'key' => \App\Services\Dashboard\Widgets\TotalMileage::key(),
                    'settings' => ['period' => 'current-month'],
                    'position' => [
                        'x' => 0, 'y' => 0, 'w' => 1, 'h' => 1
                    ]
                ],
                [
                    'key' => \App\Services\Dashboard\Widgets\TotalTime::key(),
                    'settings' => ['period' => 'current-month'],
                    'position' => [
                        'x' => 1, 'y' => 0, 'w' => 1, 'h' => 1
                    ]
                ],
                [
                    'key' => \App\Services\Dashboard\Widgets\RideCount::key(),
                    'settings' => ['period' => 'current-month'],
                    'position' => [
                        'x' => 2, 'y' => 0, 'w' => 1, 'h' => 2
                    ]
                ]
            ]
        ],
        'all-time' => [
            'name' => 'All time stats',
            'description' => 'Information about your riding career',
            'refresh_rate_in_seconds' => 60,
            'widgets' => [
                [
                    'key' => \App\Services\Dashboard\Widgets\TotalMileage::key(),
                    'settings' => ['period' => 'all-time'],
                    'position' => [
                        'x' => 0, 'y' => 0, 'w' => 1, 'h' => 1
                    ]
                ],
                [
                    'key' => \App\Services\Dashboard\Widgets\TotalTime::key(),
                    'settings' => ['period' => 'all-time'],
                    'position' => [
                        'x' => 1, 'y' => 0, 'w' => 1, 'h' => 1
                    ]
                ],
                [
                    'key' => \App\Services\Dashboard\Widgets\RideCount::key(),
                    'settings' => ['period' => 'all-time'],
                    'position' => [
                        'x' => 2, 'y' => 0, 'w' => 1, 'h' => 2
                    ]
                ]
            ]
        ],
    ]
];
