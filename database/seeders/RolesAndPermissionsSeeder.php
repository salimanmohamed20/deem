<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // User permissions
            'view_any_user', 'view_user', 'create_user', 'update_user', 'delete_user',

            // Employee permissions
            'view_any_employee', 'view_employee', 'create_employee', 'update_employee', 'delete_employee',

            // Job Title permissions
            'view_any_job_title', 'view_job_title', 'create_job_title', 'update_job_title', 'delete_job_title',

            // Team permissions
            'view_any_team', 'view_team', 'create_team', 'update_team', 'delete_team',

            // Project permissions
            'view_any_project', 'view_project', 'create_project', 'update_project', 'delete_project',

            // Task permissions
            'view_any_task', 'view_task', 'create_task', 'update_task', 'delete_task', 'assign_task',

            // Task Comment permissions
            'view_any_task_comment', 'view_task_comment', 'create_task_comment', 'update_task_comment', 'delete_task_comment',

            // Standup permissions
            'view_any_standup', 'view_standup', 'create_standup', 'update_standup', 'delete_standup',

            // Role permissions
            'view_any_role', 'view_role', 'create_role', 'update_role', 'delete_role',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Super Admin - Full Access
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());

        // Project Manager - Limited to their projects
        $projectManagerRole = Role::firstOrCreate(['name' => 'project_manager', 'guard_name' => 'web']);
        $projectManagerRole->syncPermissions([
            // Can view employees in their projects
            'view_any_employee', 'view_employee',
            // Can view job titles
            'view_any_job_title', 'view_job_title',
            // Can view teams
            'view_any_team', 'view_team',
            // Projects - view only (filtered to their projects)
            'view_any_project', 'view_project',
            // Tasks - full CRUD within their projects
            'view_any_task', 'view_task', 'create_task', 'update_task', 'delete_task', 'assign_task',
            // Comments
            'view_any_task_comment', 'view_task_comment', 'create_task_comment', 'update_task_comment',
            // Standups
            'view_any_standup', 'view_standup', 'create_standup', 'update_standup',
        ]);

        // Employee - Very limited access
        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $employeeRole->syncPermissions([
            // View only their teams
            'view_any_team', 'view_team',
            // View only their projects
            'view_any_project', 'view_project',
            // Tasks - view and update status only (filtered to assigned tasks)
            'view_any_task', 'view_task', 'update_task',
            // Comments - can add comments
            'view_any_task_comment', 'view_task_comment', 'create_task_comment', 'update_task_comment',
            // Standups - own standups only
            'view_any_standup', 'view_standup', 'create_standup', 'update_standup',
        ]);

        // Keep old 'manager' role for backward compatibility, map to project_manager
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $managerRole->syncPermissions($projectManagerRole->permissions);
    }
}
