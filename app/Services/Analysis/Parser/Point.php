<?php

namespace App\Services\Analysis\Parser;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Point implements Arrayable, Jsonable
{
    private ?float $cumulativeDistance = null;

    private ?float $grade = null;

    private ?float $battery = null;

    private ?float $calories = null;

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
    private ?float $temperature = null;

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
     * @return float|null
     */
    public function getGrade(): ?float
    {
        return $this->grade;
    }

    /**
     * @param float|null $grade
     * @return Point
     */
    public function setGrade(?float $grade): Point
    {
        $this->grade = $grade;

        return $this;
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
    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    /**
     * @param float|null $temperature
     * @return Point
     */
    public function setTemperature(?float $temperature): Point
    {
        $this->temperature = $temperature;

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

    /**
     * @return float|null
     */
    public function getBattery(): ?float
    {
        return $this->battery;
    }

    /**
     * @param float|null $battery
     * @return Point
     */
    public function setBattery(?float $battery): Point
    {
        $this->battery = $battery;

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
     * @return Point
     */
    public function setCalories(?float $calories): Point
    {
        $this->calories = $calories;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getCumulativeDistance(): ?float
    {
        return $this->cumulativeDistance;
    }

    /**
     * @param float|null $cumulativeDistance
     * @return Point
     */
    public function setCumulativeDistance(?float $cumulativeDistance): Point
    {
        $this->cumulativeDistance = $cumulativeDistance;

        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude(),
            'elevation' => $this->getElevation(),
            'time' => $this->getTime()?->unix(),
            'cadence' => $this->getCadence(),
            'temperature' => $this->getTemperature(),
            'heart_rate' => $this->getHeartRate(),
            'speed' => $this->getSpeed(),
            'grade' => $this->getGrade(),
            'battery' => $this->getBattery(),
            'calories' => $this->getCalories(),
            'cumulative_distance' => $this->getCumulativeDistance()
        ]);
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
