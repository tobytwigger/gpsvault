<?php

namespace App\Services\Routing;

use App\Services\Routing\Strategies\ValhallaRouterStrategy;
use Illuminate\Support\Manager;

class RouterManager extends Manager
{

    public function getDefaultDriver()
    {
        return 'valhalla';
    }

    public function createValhallaDriver()
    {
        return new ValhallaRouterStrategy();
    }
}
