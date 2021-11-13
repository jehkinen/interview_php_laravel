<?php

namespace App\Nova\Policies;

use App\Models\User;
use App\Models\EventTemplate;
use App\Constants\NovaPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class NovaEventTemplatePolicy
{
    public static $model = EventTemplate::class;

    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(NovaPermissions::EVENT_TEMPLATES_VIEW);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EventTemplate  $eventTemplate
     * @return mixed
     */
    public function view(User $user, EventTemplate $eventTemplate)
    {
        return $user->can(NovaPermissions::EVENT_TEMPLATES_VIEW);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(NovaPermissions::EVENT_TEMPLATES_CREATE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EventTemplate  $eventTemplate
     * @return mixed
     */
    public function update(User $user, EventTemplate $eventTemplate)
    {
        return $user->can(NovaPermissions::EVENT_TEMPLATES_UPDATE);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EventTemplate  $eventTemplate
     * @return mixed
     */
    public function delete(User $user, EventTemplate $eventTemplate)
    {
        return $user->can(NovaPermissions::EVENT_TEMPLATES_DELETE);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EventTemplate  $eventTemplate
     * @return mixed
     */
    public function restore(User $user, EventTemplate $eventTemplate)
    {
        return $user->can(NovaPermissions::EVENT_TEMPLATES_DELETE);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EventTemplate  $eventTemplate
     * @return mixed
     */
    public function forceDelete(User $user, EventTemplate $eventTemplate)
    {
        return false;
    }
}
