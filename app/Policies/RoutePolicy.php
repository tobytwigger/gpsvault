<?php

namespace App\Policies;

use App\Models\Route;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class RoutePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Route $route
     * @return Response|bool
     */
    public function view(User $user, Route $route)
    {
        return $route->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this route.');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Route $route
     * @return Response|bool
     */
    public function update(User $user, Route $route)
    {
        return $route->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this route.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Route $route
     * @return Response|bool
     */
    public function delete(User $user, Route $route)
    {
        return $route->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this route.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Route $route
     * @return Response|bool
     */
    public function restore(User $user, Route $route)
    {
        return $route->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this route.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Route $route
     * @return Response|bool
     */
    public function forceDelete(User $user, Route $route)
    {
        return $route->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this route.');
    }
}
