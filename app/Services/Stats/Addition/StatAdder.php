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
            'elevation_gain' => $this->elevationGain(),
            'start_latitude' => $this->startLatitude(),
            'start_longitude' => $this->startLongitude(),
            'end_latitude' => $this->endLatitude(),
            'end_longitude' => $this->endLongitude(),
        ];
    }

    private function propertyHasContent(string $property): bool
    {
        return $this->stats->filter(fn (Stats $stats) => $stats->{$property} !== null)->count() > 0;
    }

    public function distance(): ?float
    {
        return $this->propertyHasContent('distance')
            ? $this->stats->reduce(fn ($cumulative, Stats $stat) => $cumulative += $stat->distance ?? 0, 0)
            : null;
    }

    public function elevationGain(): ?float
    {
        return $this->propertyHasContent('elevation_gain')
            ? $this->stats->reduce(fn ($cumulative, Stats $stat) => $cumulative += $stat->elevation_gain ?? 0, 0)
            : null;
    }

    public function startLongitude(): ?float
    {
        return $this->propertyHasContent('start_longitude')
            ? $this->stats->first(fn (Stats $stat) => $stat->start_longitude !== null)?->start_longitude
            : null;
    }

    public function startLatitude(): ?float
    {
        return $this->propertyHasContent('start_latitude')
            ? $this->stats->first(fn (Stats $stat) => $stat->start_latitude !== null)?->start_latitude
            : null;
    }

    public function endLongitude(): ?float
    {
        return $this->propertyHasContent('end_longitude')
            ? $this->stats->last(fn (Stats $stat) => $stat->end_longitude !== null)?->end_longitude
            : null;
    }

    public function endLatitude(): ?float
    {
        return $this->propertyHasContent('end_latitude')
            ? $this->stats->last(fn (Stats $stat) => $stat->end_latitude !== null)->end_latitude
            : null;
    }
}
