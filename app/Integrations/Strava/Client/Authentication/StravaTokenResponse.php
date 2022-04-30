<?php

namespace App\Integrations\Strava\Client\Authentication;

use DateTime;

class StravaTokenResponse
{
    private DateTime $expiresAt;

    private int $expiresIn;

    private string $refreshToken;

    private string $accessToken;

    private int $athleteId;

    final public function __construct()
    {
    }

    /**
     * @return DateTime
     */
    public function getExpiresAt(): DateTime
    {
        return $this->expiresAt;
    }

    /**
     * @param DateTime $expiresAt
     */
    public function setExpiresAt(DateTime $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return int
     */
    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    /**
     * @param int $expiresIn
     */
    public function setExpiresIn(int $expiresIn): void
    {
        $this->expiresIn = $expiresIn;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     */
    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return int
     */
    public function getAthleteId(): int
    {
        return $this->athleteId;
    }

    /**
     * @param int $athleteId
     * @return StravaTokenResponse
     */
    public function setAthleteId(int $athleteId): StravaTokenResponse
    {
        $this->athleteId = $athleteId;

        return $this;
    }

    public static function create(
        DateTime $expiresAt,
        int $expiresIn,
        string $refreshToken,
        string $accessToken,
        int $athleteId
    ): static {
        $instance = new static();
        $instance->setExpiresAt($expiresAt);
        $instance->setExpiresIn($expiresIn);
        $instance->setRefreshToken($refreshToken);
        $instance->setAccessToken($accessToken);
        $instance->setAthleteId($athleteId);

        return $instance;
    }
}
