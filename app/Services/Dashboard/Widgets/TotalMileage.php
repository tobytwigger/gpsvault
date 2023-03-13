<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\Activity;
use App\Models\Stats;
use App\Services\Dashboard\Contracts\Widget;
use App\Services\Dashboard\Widgets\Traits\WidgetDateConstraints;

class TotalMileage extends Widget
{
    use WidgetDateConstraints;

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
            'widgetName' => 'Total Mileage',
            'description' => 'travelled ' . $this->getDurationText(),
            'distance' => Stats::orderByPreference()
                ->whereNotNull('distance')
                ->select(['id', 'activity_id', 'distance'])
                ->where('started_at', '>', $this->getLowerBound())
                ->where('finished_at', '<', $this->getUpperBound())
                ->get()
                ->unique(fn (Stats $stats) => $stats->activity_id)
                ->sum(fn (Stats $stats) => $stats->distance),
        ];
    }
}
