<?php

namespace App\Services\Dashboard\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface DashboardRepository
{

    public function getById(int $id): Dashboard;

    public function getMainDashboard(User $user): Dashboard;

    public function getDashboardsForUser(User $user): Collection;
}
