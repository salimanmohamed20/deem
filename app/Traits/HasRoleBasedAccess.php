<?php

namespace App\Traits;

use App\Models\User;

trait HasRoleBasedAccess
{
    /**
     * Check if user is Super Admin
     */
    public static function isSuperAdmin(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        return $user && $user->hasRole('super_admin');
    }

    /**
     * Check if user is Project Manager
     */
    public static function isProjectManager(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        return $user && ($user->hasRole('project_manager') || $user->hasRole('manager'));
    }

    /**
     * Check if user is Employee (basic role)
     */
    public static function isEmployee(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        return $user && $user->hasRole('employee');
    }

    /**
     * Check if user has admin-level access
     */
    public static function hasAdminAccess(?User $user = null): bool
    {
        return self::isSuperAdmin($user);
    }

    /**
     * Check if user has manager-level access
     */
    public static function hasManagerAccess(?User $user = null): bool
    {
        return self::isSuperAdmin($user) || self::isProjectManager($user);
    }

    /**
     * Get current user's employee ID
     */
    public static function getCurrentEmployeeId(): ?int
    {
        return auth()->user()?->employee?->id;
    }

    /**
     * Get project IDs accessible by the current user
     */
    public static function getAccessibleProjectIds(?User $user = null): array
    {
        $user = $user ?? auth()->user();
        
        if (!$user || !$user->employee) {
            return [];
        }

        if (self::isSuperAdmin($user)) {
            return \App\Models\Project::pluck('id')->toArray();
        }

        $employeeId = $user->employee->id;

        // Projects where user is manager
        $managerProjects = \App\Models\Project::where('project_manager_id', $employeeId)->pluck('id');

        // Projects where user's team is assigned
        $teamProjects = \App\Models\Project::whereHas('teams.members', function ($q) use ($employeeId) {
            $q->where('employees.id', $employeeId);
        })->pluck('id');

        // Projects where user has assigned tasks
        $taskProjects = \App\Models\Project::whereHas('tasks.assignees', function ($q) use ($employeeId) {
            $q->where('employees.id', $employeeId);
        })->pluck('id');

        return $managerProjects->merge($teamProjects)->merge($taskProjects)->unique()->values()->toArray();
    }
}
