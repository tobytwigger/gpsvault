<?php

namespace App\Http\Controllers\Pages\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\Dashboard;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Dashboard', [
            'schema' => Dashboard::getMainDashboard(Auth::user())->toSchema(),
            'dashboards' => Dashboard::getDashboardsForUser(Auth::user())
        ]);
    }

    public function show(int $dashboardId)
    {
        return Inertia::render('Dashboard/Dashboard', [
            'schema' => Dashboard::getById($dashboardId)->toSchema(),
            'dashboards' => Dashboard::getDashboardsForUser(Auth::user())
        ]);
    }
}
