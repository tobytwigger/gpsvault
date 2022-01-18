<?php

namespace App\Traits;

use App\Jobs\AnalyseFile;
use App\Models\File;
use App\Models\Route;
use App\Models\Stats;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;

trait HasStats
{


    public static function bootHasStats()
    {
        static::deleting(function($model) {
            $model->stats()->delete();
            $model->file()->delete();
        });
    }

//
//
//    public function getStatsAsArray(bool $withPoints = false)
//    {
//        return $this->statRelationship()->get()->mapWithKeys(function($stats) use ($withPoints) {
//            if($withPoints) {
//                $stats->append('points');
//            }
//            return [$stats->integration => $stats->toArray()];
//        });
//    }
//
//    public function getStatsAttribute()
//    {
//        return $this->getStatsAsArray(false);
//    }
//
//    public function defaultStats(bool $withPoints = false)
//    {
//        $stats = $this->getStatsAsArray($withPoints);
//        if(count($stats) === 0) {
//            throw new \Exception('There has been no analysis of this file.', 404);
//        }
//        if(array_key_exists($stats['php'])) {
//            return $stats['php'];
//        }
//        return Arr::first($stats);
//    }


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
     * The ID of the default stats
     *
     * @return int|null
     */
    public function defaultStatsId(): ?int
    {
        return $this->default_stats_id;
    }

    /**
     * The ID of the default stats
     *
     * @return int|null
     */
    public function setDefaultStatsId(?int $id): void
    {
        $this->default_stats_id = $id;
        $this->save();
    }

}
