<?php

namespace App\Policies;

use App\Models\Standup;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StandupPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_standup');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Standup $standup): bool
    {
        return $user->can('view_standup');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_standup');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Standup $standup): bool
    {
        return $user->can('update_standup');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Standup $standup): bool
    {
        return $user->can('delete_standup');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Standup $standup): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Standup $standup): bool
    {
        return false;
    }
}
