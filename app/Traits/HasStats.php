<?php

namespace App\Traits;

use App\Jobs\AnalyseFile;
use App\Models\Activity;
use App\Models\File;
use App\Models\Route;
use App\Models\Stats;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;

trait HasStats
{

    public function getDistanceAttribute(): ?int
    {
        return $this->getPreferredStatValue('distance');
    }

    public function getStartedAtAttribute(): ?Carbon
    {
        return $this->getPreferredStatValue('started_at');
    }

    public function getPreferredStatValue(string $stat)
    {
        return $this->stats()->orderByPreferred()->first()?->{$stat};
    }

    public static function bootHasStats()
    {
        static::deleting(function($model) {
            $model->stats()->delete();
            $model->file()->delete();
        });
    }

    public function statsFrom(string $integration)
    {
        return $this->stats()->where('integration', $integration);
    }

    /**
     * A relationship to all the linked stats
     * @return MorphMany
     */
    public function stats(): MorphMany
    {
        return $this->morphMany(Stats::class, 'stats');
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function hasFile(): bool
    {
        return $this->file()->exists();
    }

    public function scopeWithoutFile(Builder $query)
    {
        return $query->whereDoesntHave('file');
    }

    public function analyse()
    {
        if(!$this->hasFile()) {
            throw new \Exception('No file exists on this model');
        }
        dispatch(new AnalyseFile($this));
    }

    /**
     * @param Builder $query
     * @param string $stat One of distance, started_at
     */
    public static function scopeOrderByStat(Builder $query, string $stat)
    {
        $orderedActivities = Stats::where('stats_type', Activity::class)
            ->orderByPreference()
            ->select(['id', 'stats_id', $stat])
            ->get()
            ->unique(fn(Stats $stats) => $stats->stats_id)
            ->sort(function (Stats $a, Stats $b) use ($stat) {
                if (!$a->{$stat}) {
                    return !$b->{$stat} ? 1 : 0;
                }
                if (!$b->{$stat}) {
                    return 1;
                }
                if ($a->{$stat} == $b->{$stat}) {
                    return 0;
                }

                return $a->{$stat} < $b->{$stat} ? 1 : -1;
            })
            ->map(fn(Stats $stats) => sprintf('"%s"', $stats->stats_id));

        $query->with('stats', fn(MorphMany $subQuery) => $subQuery->orderByPreference())
            ->when(
                $orderedActivities->count() > 0,
                fn(Builder $subQuery) => $subQuery->orderByRaw(sprintf('FIELD(id, %s)', $orderedActivities->join(', ')))
            );
    }

}
