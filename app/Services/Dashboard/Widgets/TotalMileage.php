<?php

namespace App\Services\Dashboard\Widgets;

use App\Models\Activity;
use App\Models\Stats;
use App\Services\Dashboard\Contracts\Widget;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

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
            'distance' =>Stats::where('stats_type', Activity::class)
                ->orderByPreference()
                ->whereNotNull('distance')
                ->select(['id', 'stats_id', 'distance'])
                ->where('finished_at', '>', Carbon::createFromDate(now()->year, 1, 1))
                ->get()
                ->unique(fn (Stats $stats) => $stats->stats_id)
                ->sum(fn (Stats $stats) => $stats->distance)
        ];
    }
}
