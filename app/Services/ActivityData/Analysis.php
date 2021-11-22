<?php

namespace App\Services\ActivityData;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Analysis implements Arrayable, Jsonable
{

    /** @var float Distance in meters */
    private float $distance = 0.0;

    private ?float $averageSpeed;

    private ?float $averagePace;

    private ?float $minAltitude;

    private ?float $maxAltitude;

    private ?float $cumulativeElevationGain;

    private ?float $cumulativeElevationLoss;

    private Carbon $startedAt;

    private Carbon $finishedAt;

    /**
     * The difference between started at and finished at.
     *
     * @var float
     */
    private ?float $duration;

    private array $points = [];

    public function pushPoint(Point $point): Analysis
    {
        $this->points[] = $point;
        return $this;
    }

    /**
     * @return float
     */
    public function getDistance(): float
    {
        return $this->distance;
    }

    /**
     * @param float $distance
     * @return Analysis
     */
    public function setDistance(float $distance): Analysis
    {
        $this->distance = $distance;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAverageSpeed(): ?float
    {
        return $this->averageSpeed;
    }

    /**
     * @param float|null $averageSpeed
     * @return Analysis
     */
    public function setAverageSpeed(?float $averageSpeed): Analysis
    {
        $this->averageSpeed = $averageSpeed;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAveragePace(): ?float
    {
        return $this->averagePace;
    }

    /**
     * @param float|null $averagePace
     * @return Analysis
     */
    public function setAveragePace(?float $averagePace): Analysis
    {
        $this->averagePace = $averagePace;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMinAltitude(): ?float
    {
        return $this->minAltitude;
    }

    /**
     * @param float|null $minAltitude
     * @return Analysis
     */
    public function setMinAltitude(?float $minAltitude): Analysis
    {
        $this->minAltitude = $minAltitude;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMaxAltitude(): ?float
    {
        return $this->maxAltitude;
    }

    /**
     * @param float|null $maxAltitude
     * @return Analysis
     */
    public function setMaxAltitude(?float $maxAltitude): Analysis
    {
        $this->maxAltitude = $maxAltitude;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getCumulativeElevationGain(): ?float
    {
        return $this->cumulativeElevationGain;
    }

    /**
     * @param float|null $cumulativeElevationGain
     * @return Analysis
     */
    public function setCumulativeElevationGain(?float $cumulativeElevationGain): Analysis
    {
        $this->cumulativeElevationGain = $cumulativeElevationGain;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getCumulativeElevationLoss(): ?float
    {
        return $this->cumulativeElevationLoss;
    }

    /**
     * @param float|null $cumulativeElevationLoss
     * @return Analysis
     */
    public function setCumulativeElevationLoss(?float $cumulativeElevationLoss): Analysis
    {
        $this->cumulativeElevationLoss = $cumulativeElevationLoss;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getStartedAt(): Carbon
    {
        return $this->startedAt;
    }

    /**
     * @param Carbon $startedAt
     * @return Analysis
     */
    public function setStartedAt(Carbon $startedAt): Analysis
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getFinishedAt(): Carbon
    {
        return $this->finishedAt;
    }

    /**
     * @param Carbon $finishedAt
     * @return Analysis
     */
    public function setFinishedAt(Carbon $finishedAt): Analysis
    {
        $this->finishedAt = $finishedAt;
        return $this;
    }

    /**
     * @return float
     */
    public function getDuration(): ?float
    {
        return $this->duration;
    }

    /**
     * @param float $duration
     * @return Analysis
     */
    public function setDuration(?float $duration): Analysis
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * @return array
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    /**
     * @param array $points
     * @return Analysis
     */
    public function setPoints(array $points): Analysis
    {
        $this->points = $points;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'distance' => $this->getDistance(),
            'averageSpeed' => $this->getAverageSpeed(),
            'averagePace' => $this->getAveragePace(),
            'minAltitude' => $this->getMinAltitude(),
            'maxAltitude' => $this->getMaxAltitude(),
            'cumulativeElevationGain' => $this->getCumulativeElevationGain(),
            'cumulativeElevationLoss' => $this->getCumulativeElevationLoss(),
            'startedAt' => $this->getStartedAt(),
            'finishedAt' => $this->getFinishedAt(),
            'duration' => $this->getDuration(),
            'points' => array_map(fn(Point $point) => $point->toArray(), $this->getPoints())
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray());
    }

}

