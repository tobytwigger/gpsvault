<?php

namespace App\Traits;

use App\Models\File;
use App\Models\Stats;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Arr;

trait HasStats
{





    public function getStatsAsArray(bool $withPoints = false)
    {
        return $this->statRelationship()->get()->mapWithKeys(function($stats) use ($withPoints) {
            if($withPoints) {
                $stats->append('points');
            }
            return [$stats->integration => $stats->toArray()];
        });
    }

    public function getStatsAttribute()
    {
        return $this->getStatsAsArray(false);
    }

    public function statRelationshipFrom(string $integration)
    {
        return $this->statRelationship()->whereIntegration($integration);
    }

    public function defaultStats(bool $withPoints = false)
    {
        $stats = $this->getStatsAsArray($withPoints);
        if(count($stats) === 0) {
            throw new \Exception('There has been no analysis of this file.', 404);
        }
        if(array_key_exists($stats['php'])) {
            return $stats['php'];
        }
        return Arr::first($stats);
    }




    /**
     * A relationship to all the linked stats
     * @return HasManyThrough
     */
    public function stats(): HasManyThrough
    {
        return $this->hasManyThrough(Stats::class, File::class);
    }

    /**
     * Notify the model that new stats have been added
     *
     * @param Stats $stats
     */
    public function notifyAboutNewStats(Stats $stats){
        if($this->defaultStatsId() === null) {
            $this->setDefaultStatsId($stats->id);
        }
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
