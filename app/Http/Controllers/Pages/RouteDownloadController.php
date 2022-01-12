<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\User;
use App\Services\Archive\ZipCreator;
use Illuminate\Support\Facades\Auth;

class RouteDownloadController extends Controller
{
    public function downloadRoute(Route $route)
    {
        $this->authorize('view', $route);

        $file = ZipCreator::add($route)->archive();

        return redirect()->route('file.download', $file);
    }

}
