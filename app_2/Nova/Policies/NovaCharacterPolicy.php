<?php

namespace App\Nova\Policies;

use App\Models\User;
use App\Models\Character;
use App\Constants\NovaPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class NovaCharacterPolicy
{
    use HandlesAuthorization;

    public static $model = Character::class;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(NovaPermissions::CHARACTERS_VIEW);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Character  $character
     * @return mixed
     */
    public function view(User $user, Character $character)
    {
        return $user->can(NovaPermissions::CHARACTERS_VIEW);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(NovaPermissions::CHARACTERS_CREATE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Character  $character
     * @return mixed
     */
    public function update(User $user, Character $character)
    {
        return $user->can(NovaPermissions::CHARACTERS_UPDATE);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Character  $character
     * @return mixed
     */
    public function delete(User $user, Character $character)
    {
        return $user->can(NovaPermissions::CHARACTERS_DELETE);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Character  $character
     * @return mixed
     */
    public function restore(User $user, Character $character)
    {
        return $user->can(NovaPermissions::CHARACTERS_DELETE);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Character  $character
     * @return mixed
     */
    public function forceDelete(User $user, Character $character)
    {
        return $user->can(NovaPermissions::CHARACTERS_DELETE);
    }
}
