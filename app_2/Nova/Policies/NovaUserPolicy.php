<?php

namespace App\Nova\Policies;

use App\Models\User;
use App\Constants\NovaPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class NovaUserPolicy
{
    public static $model = User::class;

    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo(NovaPermissions::USERS_CREATE);
    }

    /**
     * @param User $user
     * @param User $userToEdit
     * @return bool
     */
    public function update(User $user, User $userToEdit)
    {
        return $user->hasPermissionTo(NovaPermissions::USERS_UPDATE)
            || $user->id === $userToEdit->id;
    }

    /**
     * @param User $user
     * @param User $userToDelete
     * @return bool
     */
    public function delete(User $user, User $userToDelete)
    {
        return $user->hasPermissionTo(NovaPermissions::USERS_DELETE);
    }

    /**
     * @param User $user
     * @param User $userToDelete
     * @return bool
     */
    public function forceDelete(User $user, User $userToDelete)
    {
        return $user->hasPermissionTo(NovaPermissions::USERS_DELETE);
    }
}
