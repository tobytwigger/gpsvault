<?php

namespace App\Http\Controllers\Pages\Route;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Services\Archive\ZipCreator;

class RouteDownloadController extends Controller
{
    public function downloadRoute(Route $route)
    {
        $this->authorize('view', $route);

        $file = ZipCreator::add($route)->archive();

        return redirect()->route('file.download', $file);
    }
}
