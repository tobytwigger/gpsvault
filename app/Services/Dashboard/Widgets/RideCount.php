<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\Activity;
use App\Models\Stats;
use App\Services\Dashboard\Contracts\Widget;
use Carbon\Carbon;

class RideCount extends Widget
{
    public static function key(): string
    {
        return 'ride-count';
    }

    public function component(): string
    {
        return 'w-ride-count';
    }

    public function gatherData(): array
    {
        return [
            'count' => Stats::where('stats_type', Activity::class)
                ->orderByPreference()
                ->select(['id', 'stats_id'])
                ->where('finished_at', '>', Carbon::createFromDate(now()->year, 1, 1))
                ->get()
                ->unique(fn (Stats $stats) => $stats->stats_id)
                ->count(),
        ];
    }
}
