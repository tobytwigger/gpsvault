<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ActivityPolicy
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
     * @param Activity $activity
     * @return Response|bool
     */
    public function view(User $user, Activity $activity)
    {
        return $activity->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this activity.');
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
     * @param Activity $activity
     * @return Response|bool
     */
    public function update(User $user, Activity $activity)
    {
        return $activity->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this activity.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Activity $activity
     * @return Response|bool
     */
    public function delete(User $user, Activity $activity)
    {
        return $activity->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this activity.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Activity $activity
     * @return Response|bool
     */
    public function restore(User $user, Activity $activity)
    {
        return $activity->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this activity.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Activity $activity
     * @return Response|bool
     */
    public function forceDelete(User $user, Activity $activity)
    {
        return $activity->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this activity.');
    }
}
