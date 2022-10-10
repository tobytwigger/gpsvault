<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Services\Dashboard\Contracts\DashboardRepository;
use Illuminate\Support\Arr;

class DefaultDashboardFactory
{

    private DashboardRepository $repository;

    public function __construct(DashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function syncDashboards(User $user)
    {
        foreach(config('dashboard.default', []) as $dashboard) {
            $dashboardData = $this->getDataFor($dashboard);
            if(!$this->userHasDashboard($user, $dashboardData['name'])) {
                $this->createDashboard($user, $dashboardData);
            }
        }
    }

    private function getDataFor(string $dashboard): array
    {
        return config('dashboard.dashboards.' . $dashboard);
    }

    private function userHasDashboard(User $user, string $name): bool
    {
        return $this->repository->existsForUser($user, $name);
    }

    private function createDashboard(User $user, array $dashboardData)
    {
        $dashboard = $this->repository->createDashboard($user, $dashboardData['name'], $dashboardData['description'], $dashboardData['refresh_rate_in_seconds']);
        foreach($dashboardData['widgets'] as $widgetData) {
            $this->repository->createWidget($dashboard, $widgetData['key'], $widgetData['settings'], $widgetData['position']);
        }
    }


}
