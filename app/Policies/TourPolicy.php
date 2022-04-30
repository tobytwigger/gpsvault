<?php

namespace App\Policies;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TourPolicy
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
     * @param Tour $tour
     * @return Response|bool
     */
    public function view(User $user, Tour $tour)
    {
        return $tour->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this tour.');
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
     * @param Tour $tour
     * @return Response|bool
     */
    public function update(User $user, Tour $tour)
    {
        return $tour->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this tour.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Tour $tour
     * @return Response|bool
     */
    public function delete(User $user, Tour $tour)
    {
        return $tour->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this tour.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Tour $tour
     * @return Response|bool
     */
    public function restore(User $user, Tour $tour)
    {
        return $tour->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this tour.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Tour $tour
     * @return Response|bool
     */
    public function forceDelete(User $user, Tour $tour)
    {
        return $tour->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this tour.');
    }
}
