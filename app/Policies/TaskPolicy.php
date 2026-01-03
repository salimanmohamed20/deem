<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Traits\HasRoleBasedAccess;

class TaskPolicy
{
    use HasRoleBasedAccess;

    public function viewAny(User $user): bool
    {
        // Everyone with employee record can view (filtered by scope)
        return $user->hasRole('super_admin') || 
               $user->hasRole('project_manager') || 
               $user->hasRole('manager') || 
               $user->hasRole('employee');
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $task->isAccessibleBy($user);
    }

    public function create(User $user): bool
    {
        // Only Super Admin and Project Manager can create tasks
        return $user->hasRole('super_admin') || 
               $user->hasRole('project_manager') || 
               $user->hasRole('manager');
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Project Manager can update tasks in their projects
        if (self::isProjectManager($user)) {
            $projectIds = self::getAccessibleProjectIds($user);
            return in_array($task->project_id, $projectIds);
        }

        // Employee can only update status of their assigned tasks
        if ($user->employee) {
            return $task->assignees()->where('employees.id', $user->employee->id)->exists();
        }

        return false;
    }

    public function delete(User $user, Task $task): bool
    {
        // Only Super Admin and Project Manager can delete
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if (self::isProjectManager($user)) {
            $projectIds = self::getAccessibleProjectIds($user);
            return in_array($task->project_id, $projectIds);
        }

        return false;
    }

    public function restore(User $user, Task $task): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return $user->hasRole('super_admin');
    }
}
