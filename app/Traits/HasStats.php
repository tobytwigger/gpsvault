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
