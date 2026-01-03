<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use App\Traits\HasRoleBasedAccess;

class ProjectPolicy
{
    use HasRoleBasedAccess;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || 
               $user->hasRole('project_manager') || 
               $user->hasRole('manager') || 
               $user->hasRole('employee');
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        $accessibleIds = self::getAccessibleProjectIds($user);
        return in_array($project->id, $accessibleIds);
    }

    public function create(User $user): bool
    {
        // Only Super Admin can create projects
        return $user->hasRole('super_admin');
    }

    public function update(User $user, Project $project): bool
    {
        // Only Super Admin can update projects
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Project $project): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return $user->hasRole('super_admin');
    }
}
