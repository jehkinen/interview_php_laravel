<?php

namespace App\Nova\Policies;

use App\Models\User;
use App\Models\Sport;
use App\Constants\NovaPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class NovaSportPolicy
{
    public static $model = Sport::class;

    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(NovaPermissions::SPORTS_VIEW);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Sport  $sport
     * @return mixed
     */
    public function view(User $user, Sport $sport)
    {
        return $user->can(NovaPermissions::SPORTS_VIEW);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(NovaPermissions::SPORTS_CREATE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Sport  $sport
     * @return mixed
     */
    public function update(User $user, Sport $sport)
    {
        return $user->can(NovaPermissions::SPORTS_UPDATE);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Sport  $sport
     * @return mixed
     */
    public function delete(User $user, Sport $sport)
    {
        return $user->can(NovaPermissions::SPORTS_DELETE);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Sport  $sport
     * @return mixed
     */
    public function restore(User $user, Sport $sport)
    {
        return $user->can(NovaPermissions::SPORTS_DELETE);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Sport  $sport
     * @return mixed
     */
    public function forceDelete(User $user, Sport $sport)
    {
        return false;
    }
}
