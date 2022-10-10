<?php

namespace App\Services\Dashboard\Contracts;

abstract class Widget
{

    protected array $settings = [];

    protected array $position = [];

    protected int $id;

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param array $position
     */
    public function setPosition(array $position): void
    {
        $this->position = $position;
    }

    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }

    public function getSetting(string $key): mixed
    {
        return data_get($this->settings, $key);
    }

    abstract public static function key(): string;

    public static function create(int $id, array $settings = [], array $position = []): static
    {
        $instance = app(static::class);
        $instance->setId($id);
        $instance->setSettings($settings);
        $instance->setPosition($position);
        return $instance;
    }

    /**
     * The Vue component representing the widget
     *
     * @return string
     */
    abstract public function component(): string;

    /**
     * Gather the data to return to the controller.
     * @return array
     */
    abstract public function gatherData(): array;

    public function toSchema(): array
    {
        return [
            'component' => $this->component(),
            'data' => $this->gatherData(),
            'position' => $this->position,
            'id' => $this->id
        ];
    }

}
