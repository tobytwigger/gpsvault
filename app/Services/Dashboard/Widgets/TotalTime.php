<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\Activity;
use App\Models\Stats;
use App\Services\Dashboard\Contracts\Widget;
use App\Services\Dashboard\Widgets\Traits\WidgetDateConstraints;

class TotalTime extends Widget
{
    use WidgetDateConstraints;

    public static function key(): string
    {
        return 'total-time';
    }

    public function component(): string
    {
        return 'w-basic-widget';
    }

    public function gatherData(): array
    {
        return [
            'widgetName' => 'Total time',
            'description' => 'riding ' . $this->getDurationText(),
            'data' => (Stats::where('stats_type', Activity::class)
                ->orderByPreference()
                ->whereNotNull('duration')
                ->select(['id', 'stats_id', 'duration'])
                ->where('started_at', '>', $this->getLowerBound())
                ->where('finished_at', '<', $this->getUpperBound())
                ->get()
                ->unique(fn(Stats $stats) => $stats->stats_id)
                ->sum(fn(Stats $stats) => $stats->duration) /(60*60)) . 'h',
        ];
    }
}
