<?php

namespace App\Http\Controllers\Pages\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\Dashboard;
use App\Services\Dashboard\DefaultDashboardFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $dashboard = Dashboard::getMainDashboard(Auth::user());
        } catch (ModelNotFoundException) {
            app(DefaultDashboardFactory::class)->syncDashboards(Auth::user());
            $dashboard = Dashboard::getMainDashboard(Auth::user());
        }

        return Inertia::render('Dashboard/Dashboard', [
            'initialSelection' => $dashboard->id,
            'dashboards' => Dashboard::getDashboardsForUser(Auth::user())
                ->map->toSchema(),
        ]);
    }
    
}
