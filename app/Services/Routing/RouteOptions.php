<?php

namespace App\Services\Routing;

class RouteOptions
{
    private array $costingOptions;

    public function __construct(array $costingOptions = [])
    {
        $this->costingOptions = $costingOptions;
    }

    public function toArray(): array
    {
        return [
            'costing_options' => $this->costingOptions
        ];
    }

}
