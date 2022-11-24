<?php

namespace App\Services\Dashboard\Database;

use App\Models\User;
use App\Services\Dashboard\Contracts\Dashboard;
use App\Services\Dashboard\Contracts\DashboardRepository;
use App\Services\Dashboard\Database\Dashboard as DashboardModel;
use Illuminate\Support\Collection;

class DashboardDatabaseRepository implements DashboardRepository
{
    public function getById(int $id): Dashboard
    {
        return DashboardModel::findOrFail($id);
    }

    public function getMainDashboard(User $user): Dashboard
    {
        return DashboardModel::where('user_id', $user->id)->firstOrFail();
    }

    public function getDashboardsForUser(User $user): Collection
    {
        return DashboardModel::where('user_id', $user->id)->get();
    }

    public function existsForUser(User $user, string $name): bool
    {
        return DashboardModel::where('user_id', $user->id)
            ->where('name', $name)
            ->exists();
    }

    public function createDashboard(User $user, string $name, string $description, float $refreshRate)
    {
        return DashboardModel::create([
            'user_id' => $user->id,
            'name' => $name,
            'description' => $description,
            'refresh_rate_in_seconds' => $refreshRate,
        ]);
    }

    public function createWidget($dashboard, string $key, array $settings, array $position)
    {
        return Widget::create([
            'dashboard_id' => $dashboard->id,
            'widget_key' => $key,
            'settings' => $settings,
            'position' => $position,
        ]);
    }
}
