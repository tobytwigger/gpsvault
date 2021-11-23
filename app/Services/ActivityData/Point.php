<?php

namespace App\Services\ActivityData;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Point implements Arrayable, Jsonable
{

    /**
     * @var float
     */
    private ?float $latitude = null;

    /**
     * @var float
     */
    private ?float $longitude = null;

    /**
     * @var float|null
     */
    private ?float $elevation = null;

    /**
     * @var Carbon
     */
    private ?Carbon $time = null;

    /**
     * @var float|null
     */
    private ?float $cadence = null;

    /**
     * @var float|null
     */
    private ?float $averageTemperature = null;

    /**
     * @var float|null
     */
    private ?float $heartRate = null;

    /**
     * @var float|null
     */
    private ?float $speed = null;

    /**
     * @return float
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     * @return Point
     */
    public function setLatitude(?float $latitude): Point
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * @return float
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     * @return Point
     */
    public function setLongitude(?float $longitude): Point
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getElevation(): ?float
    {
        return $this->elevation;
    }

    /**
     * @param float|null $elevation
     * @return Point
     */
    public function setElevation(?float $elevation): Point
    {
        $this->elevation = $elevation;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getTime(): ?Carbon
    {
        return $this->time;
    }

    /**
     * @param Carbon $time
     * @return Point
     */
    public function setTime(?Carbon $time): Point
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getCadence(): ?float
    {
        return $this->cadence;
    }

    /**
     * @param float|null $cadence
     * @return Point
     */
    public function setCadence(?float $cadence): Point
    {
        $this->cadence = $cadence;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAverageTemperature(): ?float
    {
        return $this->averageTemperature;
    }

    /**
     * @param float|null $averageTemperature
     * @return Point
     */
    public function setAverageTemperature(?float $averageTemperature): Point
    {
        $this->averageTemperature = $averageTemperature;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getHeartRate(): ?float
    {
        return $this->heartRate;
    }

    /**
     * @param float|null $heartRate
     * @return Point
     */
    public function setHeartRate(?float $heartRate): Point
    {
        $this->heartRate = $heartRate;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getSpeed(): ?float
    {
        return $this->speed;
    }

    /**
     * @param float|null $speed
     * @return Point
     */
    public function setSpeed(?float $speed): Point
    {
        $this->speed = $speed;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude(),
            'elevation' => $this->getElevation(),
            'time' => $this->getTime(),
            'cadence' => $this->getCadence(),
            'averageTemperature' => $this->getAverageTemperature(),
            'heartRate' => $this->getHeartRate(),
            'speed' => $this->getSpeed(),
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
