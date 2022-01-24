<?php

namespace App\Services\Stats\Addition;

use App\Models\Stats;
use Illuminate\Support\Collection;

class StatAdder
{

    /**
     * @var Collection|Stats[]
     */
    private Collection $stats;

    public function __construct(array $stats = [])
    {
        $this->stats = collect($stats);
    }

    public function push(Stats $stat): static
    {
        $this->stats->push($stat);
        return $this;
    }

    public function toArray(): array
    {
        return [
            'distance' => $this->distance(),
            'elevation_gain' => $this->elevationGain()
        ];
    }

    private function propertyHasContent(string $property): bool
    {
        return $this->stats->filter(fn(Stats $stats) => $stats->{$property} !== null)->count() > 0;
    }

    public function distance(): ?float
    {
        return $this->propertyHasContent('distance')
            ? $this->stats->reduce(fn($cumulative, Stats $stat) => $cumulative += $stat->distance ?? 0, 0)
            : null;
    }

    public function elevationGain(): ?float
    {
        return $this->propertyHasContent('elevation_gain')
            ? $this->stats->reduce(fn($cumulative, Stats $stat) => $cumulative += $stat->elevation_gain ?? 0, 0)
            : null;
    }

}
