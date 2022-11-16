<?php

namespace App\Services\Dashboard;

class WidgetStore
{

    private array $widgets = [];

    public function pushWidget(string $widget): void
    {
        if(!is_subclass_of($widget, \App\Services\Dashboard\Contracts\Widget::class)) {
            throw new \Exception('Not of type widget');
        }
        $this->widgets[$widget::key()] = $widget;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->widgets);
    }

    public function get(string $key): string
    {
        if(!$this->has($key)) {
            throw new \Exception('Widget not found');
        }
        return $this->widgets[$key];
    }

}
