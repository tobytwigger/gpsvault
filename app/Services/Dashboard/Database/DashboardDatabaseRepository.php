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
}
