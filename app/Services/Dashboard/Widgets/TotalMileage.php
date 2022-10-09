<?php

namespace App\Services\Dashboard\Widgets;

use App\Services\Dashboard\Contracts\Widget;

class TotalMileage extends Widget
{

    public static function key(): string
    {
        return 'total-mileage';
    }

    public function component(): string
    {
        return 'w-total-mileage';
    }

    public function gatherData(): array
    {
        return [
            'distance' => 1000
        ];
    }
}
