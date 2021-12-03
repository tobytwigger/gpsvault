<?php

namespace App\Services\ActivityImport;

use App\Models\File;
use Carbon\Carbon;

class ActivityDetails
{

    protected ?File $activityFile = null;

    protected string $name;

    protected ?string $description = null;

    protected array $media = [];

    protected array $additionalData = [];

    protected array $additionalArrayData = [];

    protected array $linkedTo = [];

    protected ?int $distance = null;

    protected ?Carbon $startedAt = null;

    /**
     * @return array
     */
    public function getLinkedTo(): array
    {
        return $this->linkedTo;
    }

    /**
     * @param array $linkedTo
     * @return ActivityDetails
     */
    public function setLinkedTo(array $linkedTo): ActivityDetails
    {
        $this->linkedTo = $linkedTo;
        return $this;
    }

    public function addLinkedTo(string $integration): ActivityDetails
    {
        $this->linkedTo[] = $integration;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getActivityFile(): ?File
    {
        return $this->activityFile;
    }

    /**
     * @param File|null $activityFile
     * @return ActivityDetails
     */
    public function setActivityFile(?File $activityFile): ActivityDetails
    {
        $this->activityFile = $activityFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name ?? 'New Ride';
    }

    /**
     * @param string $name
     * @return ActivityDetails
     */
    public function setName(string $name): ActivityDetails
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return ActivityDetails
     */
    public function setDescription(?string $description): ActivityDetails
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return array
     */
    public function getMedia(): array
    {
        return $this->media;
    }

    /**
     * @param array $media
     * @return ActivityDetails
     */
    public function setMedia(array $media): ActivityDetails
    {
        $this->media = $media;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDistance(): ?int
    {
        return $this->distance;
    }

    /**
     * @param int|null $distance
     * @return ActivityDetails
     */
    public function setDistance(?int $distance): ActivityDetails
    {
        $this->distance = $distance;
        return $this;
    }

    /**
     * @return Carbon|null
     */
    public function getStartedAt(): ?Carbon
    {
        return $this->startedAt;
    }

    /**
     * @param Carbon|null $startedAt
     * @return ActivityDetails
     */
    public function setStartedAt(?Carbon $startedAt): ActivityDetails
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    /**
     * @return array
     */
    public function getAllAdditionalData(): array
    {
        return array_merge($this->additionalData, $this->additionalArrayData);
    }

    /**
     * @return array
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

    /**
     * @return array
     */
    public function getAdditionalArrayData(): array
    {
        return $this->additionalArrayData;
    }

    /**
     * @param array $additionalData
     * @return ActivityDetails
     */
    public function setAdditionalData(array $additionalData): ActivityDetails
    {
        $this->additionalData = $additionalData;
        return $this;
    }

    public function unsetAdditionalData(string $key)
    {
        if(isset($this->additionalData[$key])) {
            unset($this->additionalData[$key]);
        }
        if(isset($this->additionalArrayData[$key])) {
            unset($this->additionalArrayData[$key]);
        }
    }

    /**
     * @param array $additionalArrayData
     * @return ActivityDetails
     */
    public function setAdditionalArrayData(array $additionalArrayData): ActivityDetails
    {
        $this->additionalArrayData = $additionalArrayData;
        return $this;
    }

    public function setAdditionalDataKey(string $key, mixed $value)
    {
        $this->additionalData[$key] = $value;
    }

    public function appendAdditionalDataKey(string $key, mixed $value): void
    {
        if(!isset($this->additionalArrayData[$key])) {
            $this->additionalArrayData[$key] = [];
        }
        $this->additionalArrayData[$key][] = $value;
    }

}
