<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\Stats;
use App\Services\Dashboard\Contracts\Widget;
use App\Services\Dashboard\Widgets\Traits\WidgetDateConstraints;

class RideCount extends Widget
{
    use WidgetDateConstraints;

    public static function key(): string
    {
        return 'ride-count';
    }

    public function component(): string
    {
        return 'w-basic-widget';
    }

    public function gatherData(): array
    {
        return [
            'widgetName' => 'Number of rides',
            'description' => 'rides ' . $this->getDurationText(),
            'data' => (string) Stats::orderByPreference()
                ->select(['id', 'activity_id'])
                ->where('started_at', '>', $this->getLowerBound())
                ->where('finished_at', '<', $this->getUpperBound())
                ->get()
                ->unique(fn (Stats $stats) => $stats->activity_id)
                ->count(),
        ];
    }
}
