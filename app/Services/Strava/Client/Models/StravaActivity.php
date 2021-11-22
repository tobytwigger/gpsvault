<?php

namespace App\Services\Strava\Client\Models;

class StravaActivity
{

    protected int $teamId;

    protected string $name;

    protected float $distance;

    protected float $elevationGain;

    protected int $movingTime;

    protected int $elapsedTime;

    protected string $type;

    /**
     * @return int
     */
    public function getTeamId(): int
    {
        return $this->teamId;
    }

    /**
     * @param int $teamId
     */
    public function setTeamId(int $teamId): void
    {
        $this->teamId = $teamId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
     */
    public function setDistance(float $distance): void
    {
        $this->distance = $distance;
    }

    /**
     * @return float
     */
    public function getElevationGain(): float
    {
        return $this->elevationGain;
    }

    /**
     * @param float $elevationGain
     */
    public function setElevationGain(float $elevationGain): void
    {
        $this->elevationGain = $elevationGain;
    }

    /**
     * @return int
     */
    public function getMovingTime(): int
    {
        return $this->movingTime;
    }

    /**
     * @param int $movingTime
     */
    public function setMovingTime(int $movingTime): void
    {
        $this->movingTime = $movingTime;
    }

    /**
     * @return int
     */
    public function getElapsedTime(): int
    {
        return $this->elapsedTime;
    }

    /**
     * @param int $elapsedTime
     */
    public function setElapsedTime(int $elapsedTime): void
    {
        $this->elapsedTime = $elapsedTime;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public static function make(int $teamId,
                                string $name = null,
                                float $distance = 0.0,
                                float $elevationGain = 0.0,
                                int $movingTime = 0,
                                int $elapsedTime = 0,
                                string $type = 'Other')
    {
        $instance = new static();

        $instance->setTeamId($teamId);
        $instance->setName($name);
        $instance->setDistance($distance);
        $instance->setElevationGain($elevationGain);
        $instance->setMovingTime($movingTime);
        $instance->setElapsedTime($elapsedTime);
        $instance->setType($type);

        return $instance;
    }

}
