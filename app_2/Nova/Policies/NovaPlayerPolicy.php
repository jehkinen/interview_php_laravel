<?php

namespace App\Nova\Policies;

use App\Models\User;
use App\Models\Player;
use App\Constants\NovaPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class NovaPlayerPolicy
{
    public static $model = Player::class;

    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(NovaPermissions::PLAYERS_VIEW);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Player  $player
     * @return mixed
     */
    public function view(User $user, Player $player)
    {
        return $user->can(NovaPermissions::PLAYERS_VIEW);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(NovaPermissions::PLAYERS_CREATE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Player  $player
     * @return mixed
     */
    public function update(User $user, Player $player)
    {
        return $user->can(NovaPermissions::PLAYERS_UPDATE);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Player  $player
     * @return mixed
     */
    public function delete(User $user, Player $player)
    {
        return $user->can(NovaPermissions::PLAYERS_DELETE);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Player  $player
     * @return mixed
     */
    public function restore(User $user, Player $player)
    {
        return $user->can(NovaPermissions::PLAYERS_DELETE);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Player  $player
     * @return mixed
     */
    public function forceDelete(User $user, Player $player)
    {
        return false;
    }
}