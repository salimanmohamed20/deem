<?php

namespace App\Models;

use App\Traits\HasRoleBasedAccess;
use Illuminate\Database\Eloquent\Model;

class Standup extends Model
{
    use HasRoleBasedAccess;

    protected $fillable = ['employee_id', 'date'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function entries()
    {
        return $this->hasMany(StandupEntry::class);
    }

    /**
     * Scope to filter standups based on user role
     * - Super Admin: sees all standups
     * - Project Manager: sees standups from employees in their projects
     * - Employee: sees only their own standups
     */
    public function scopeForCurrentEmployee($query)
    {
        $user = auth()->user();
        
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // Super Admin sees everything
        if (self::isSuperAdmin($user)) {
            return $query;
        }

        // Project Manager sees standups from their project team members
        if (self::isProjectManager($user) && $user->employee) {
            $projectIds = self::getAccessibleProjectIds($user);
            
            // Get employee IDs from accessible projects
            $employeeIds = Employee::whereHas('tasks', function ($q) use ($projectIds) {
                $q->whereIn('project_id', $projectIds);
            })->orWhereHas('teams.projects', function ($q) use ($projectIds) {
                $q->whereIn('projects.id', $projectIds);
            })->pluck('id')->toArray();

            // Include own standups
            $employeeIds[] = $user->employee->id;

            return $query->whereIn('employee_id', array_unique($employeeIds));
        }

        // Employee sees only their own standups
        if ($user->employee) {
            return $query->where('employee_id', $user->employee->id);
        }

        return $query->whereRaw('1 = 0');
    }
}
