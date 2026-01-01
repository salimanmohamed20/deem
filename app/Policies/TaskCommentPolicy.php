<?php

namespace App\Policies;

use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskCommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_task_comment');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskComment $taskComment): bool
    {
        return $user->can('view_task_comment');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_task_comment');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskComment $taskComment): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        return $user->employee && $user->employee->id === $taskComment->employee_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskComment $taskComment): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        return $user->employee && $user->employee->id === $taskComment->employee_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TaskComment $taskComment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TaskComment $taskComment): bool
    {
        return false;
    }
}
