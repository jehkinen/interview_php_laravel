<?php

namespace App\Nova\Policies;

use App\Models\User;
use App\Models\SportPosition;
use App\Constants\NovaPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class NovaSportPositionPolicy
{
    public static $model = SportPosition::class;

    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(NovaPermissions::SPORT_POSITIONS_VIEW);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SportPosition  $sportPosition
     * @return mixed
     */
    public function view(User $user, SportPosition $sportPosition)
    {
        return $user->can(NovaPermissions::SPORT_POSITIONS_VIEW);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(NovaPermissions::SPORT_POSITIONS_CREATE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SportPosition  $sportPosition
     * @return mixed
     */
    public function update(User $user, SportPosition $sportPosition)
    {
        return $user->can(NovaPermissions::SPORT_POSITIONS_UPDATE);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SportPosition  $sportPosition
     * @return mixed
     */
    public function delete(User $user, SportPosition $sportPosition)
    {
        return $user->can(NovaPermissions::SPORT_POSITIONS_DELETE);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SportPosition  $sportPosition
     * @return mixed
     */
    public function restore(User $user, SportPosition $sportPosition)
    {
        return $user->can(NovaPermissions::SPORT_POSITIONS_DELETE);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SportPosition  $sportPosition
     * @return mixed
     */
    public function forceDelete(User $user, SportPosition $sportPosition)
    {
        return false;
    }
}
