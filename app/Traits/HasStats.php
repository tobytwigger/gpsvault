<?php

namespace App\Traits;

use App\Models\ActivityStats;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    abstract public function statRelationship(): HasMany;

}
