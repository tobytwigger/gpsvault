<?php

namespace App\Services\Dashboard\Contracts;

abstract class Widget
{

    protected array $settings = [];

    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }

    abstract public static function key(): string;

    public static function create(array $settings = []): static
    {
        $instance = app(static::class);
        $instance->setSettings($settings);
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
        ];
    }

}
