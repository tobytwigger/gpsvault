<?php

namespace App\Services\Dashboard\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface DashboardRepository
{

    public function getById(int $id): Dashboard;

    public function getMainDashboard(User $user): Dashboard;

    public function getDashboardsForUser(User $user): Collection;

    public function existsForUser(User $user, string $name);

    public function createDashboard(User $user, string $name, string $description, float $refreshRate);

    public function createWidget($dashboard, string $key, array $settings, array $position);
}
