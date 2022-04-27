<?php

namespace App\Services\Analysis\Analyser;

use App\Services\Analysis\Parser\Point;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Analysis implements Arrayable, Jsonable
{
    private ?float $averageHeartrate = null;

    private ?float $maxHeartrate = null;

    private ?float $calories = null;

    private ?float $movingTime = null;

    private ?float $maxSpeed = null;

    private ?float $averageCadence = null;

    private ?float $averageTemp = null;

    private ?float $averageWatts = null;

    private ?float $kilojoules = null;

    private ?float $startLatitude = null;

    private ?float $startLongitude = null;

    private ?float $endLatitude = null;

    private ?float $endLongitude = null;

    private ?float $distance = null;

    private ?float $averageSpeed = null;

    private ?float $averagePace = null;

    private ?float $minAltitude = null;

    private ?float $maxAltitude = null;

    private ?float $cumulativeElevationGain = null;

    private ?float $cumulativeElevationLoss = null;

    private ?Carbon $startedAt = null;

    private ?Carbon $finishedAt = null;

    /**
     * The difference between started at and finished at.
     *
     * @var float
     */
    private ?float $duration = null;

    private array $points = [];

    public function pushPoint(Point $point): Analysis
    {
        $this->points[] = $point;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getDistance(): ?float
    {
        return $this->distance;
    }

    /**
     * @param float|null $distance
     * @return Analysis
     */
    public function setDistance(?float $distance): Analysis
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
    public function getStartedAt(): ?Carbon
    {
        return $this->startedAt;
    }

    /**
     * @param Carbon $startedAt
     * @return Analysis
     */
    public function setStartedAt(?Carbon $startedAt): Analysis
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getFinishedAt(): ?Carbon
    {
        return $this->finishedAt;
    }

    /**
     * @param Carbon $finishedAt
     * @return Analysis
     */
    public function setFinishedAt(?Carbon $finishedAt): Analysis
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

    /**
     * @return float|null
     */
    public function getAverageHeartrate(): ?float
    {
        return $this->averageHeartrate;
    }

    /**
     * @param float|null $averageHeartrate
     * @return Analysis
     */
    public function setAverageHeartrate(?float $averageHeartrate): Analysis
    {
        $this->averageHeartrate = $averageHeartrate;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getMaxHeartrate(): ?float
    {
        return $this->maxHeartrate;
    }

    /**
     * @param float|null $maxHeartrate
     * @return Analysis
     */
    public function setMaxHeartrate(?float $maxHeartrate): Analysis
    {
        $this->maxHeartrate = $maxHeartrate;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getCalories(): ?float
    {
        return $this->calories;
    }

    /**
     * @param float|null $calories
     * @return Analysis
     */
    public function setCalories(?float $calories): Analysis
    {
        $this->calories = $calories;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getMovingTime(): ?float
    {
        return $this->movingTime;
    }

    /**
     * @param float|null $movingTime
     * @return Analysis
     */
    public function setMovingTime(?float $movingTime): Analysis
    {
        $this->movingTime = $movingTime;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getMaxSpeed(): ?float
    {
        return $this->maxSpeed;
    }

    /**
     * @param float|null $maxSpeed
     * @return Analysis
     */
    public function setMaxSpeed(?float $maxSpeed): Analysis
    {
        $this->maxSpeed = $maxSpeed;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getAverageCadence(): ?float
    {
        return $this->averageCadence;
    }

    /**
     * @param float|null $averageCadence
     * @return Analysis
     */
    public function setAverageCadence(?float $averageCadence): Analysis
    {
        $this->averageCadence = $averageCadence;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getAverageTemp(): ?float
    {
        return $this->averageTemp;
    }

    /**
     * @param float|null $averageTemp
     * @return Analysis
     */
    public function setAverageTemp(?float $averageTemp): Analysis
    {
        $this->averageTemp = $averageTemp;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getAverageWatts(): ?float
    {
        return $this->averageWatts;
    }

    /**
     * @param float|null $averageWatts
     * @return Analysis
     */
    public function setAverageWatts(?float $averageWatts): Analysis
    {
        $this->averageWatts = $averageWatts;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getKilojoules(): ?float
    {
        return $this->kilojoules;
    }

    /**
     * @param float|null $kilojoules
     * @return Analysis
     */
    public function setKilojoules(?float $kilojoules): Analysis
    {
        $this->kilojoules = $kilojoules;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getStartLatitude(): ?float
    {
        return $this->startLatitude;
    }

    /**
     * @param float|null $startLatitude
     * @return Analysis
     */
    public function setStartLatitude(?float $startLatitude): Analysis
    {
        $this->startLatitude = $startLatitude;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getStartLongitude(): ?float
    {
        return $this->startLongitude;
    }

    /**
     * @param float|null $startLongitude
     * @return Analysis
     */
    public function setStartLongitude(?float $startLongitude): Analysis
    {
        $this->startLongitude = $startLongitude;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getEndLatitude(): ?float
    {
        return $this->endLatitude;
    }

    /**
     * @param float|null $endLatitude
     * @return Analysis
     */
    public function setEndLatitude(?float $endLatitude): Analysis
    {
        $this->endLatitude = $endLatitude;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getEndLongitude(): ?float
    {
        return $this->endLongitude;
    }

    /**
     * @param float|null $endLongitude
     * @return Analysis
     */
    public function setEndLongitude(?float $endLongitude): Analysis
    {
        $this->endLongitude = $endLongitude;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'distance' => $this->getDistance(),
            'average_speed' => $this->getAverageSpeed(),
            'average_pace' => $this->getAveragePace(),
            'min_altitude' => $this->getMinAltitude(),
            'max_altitude' => $this->getMaxAltitude(),
            'elevation_gain' => $this->getCumulativeElevationGain(),
            'elevation_loss' => $this->getCumulativeElevationLoss(),
            'started_at' => $this->getStartedAt(),
            'finished_at' => $this->getFinishedAt(),
            'duration' => $this->getDuration(),
            'average_heartrate' => $this->getAverageHeartrate(),
            'max_heartrate' => $this->getMaxHeartrate(),
            'calories' => $this->getCalories(),
            'moving_time' => $this->getMovingTime(),
            'max_speed' => $this->getMaxSpeed(),
            'average_cadence' => $this->getAverageCadence(),
            'average_temp' => $this->getAverageTemp(),
            'average_watts' => $this->getAverageWatts(),
            'kilojoules' => $this->getKilojoules(),
            'start_latitude' => $this->getStartLatitude(),
            'start_longitude' => $this->getStartLongitude(),
            'end_latitude' => $this->getEndLatitude(),
            'end_longitude' => $this->getEndLongitude(),
            //            'points' => array_map(fn(Point $point) => $point->toArray(), $this->getPoints())
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray());
    }
}
