<?php

namespace App\Models;

use App\Traits\HasRoleBasedAccess;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasRoleBasedAccess;

    protected $fillable = [
        'name',
        'description',
        'project_manager_id',
        'status',
        'start_date',
        'end_date',
    ];

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'project_manager_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function standupEntries()
    {
        return $this->hasMany(StandupEntry::class);
    }

    /**
     * Scope to filter projects based on user role
     * - Super Admin: sees all projects
     * - Project Manager: sees projects they manage or are team member of
     * - Employee: sees projects where they have assigned tasks or are team member
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

        if (!$user->employee) {
            return $query->whereRaw('1 = 0');
        }

        $employeeId = $user->employee->id;

        return $query->where(function ($q) use ($employeeId) {
            // Projects where user is the manager
            $q->where('project_manager_id', $employeeId)
              // Or projects where user's team is assigned
              ->orWhereHas('teams.members', function ($teamQuery) use ($employeeId) {
                  $teamQuery->where('employees.id', $employeeId);
              })
              // Or projects where user has assigned tasks
              ->orWhereHas('tasks.assignees', function ($taskQuery) use ($employeeId) {
                  $taskQuery->where('employees.id', $employeeId);
              });
        });
    }

    /**
     * Check if user can manage this project (is project manager)
     */
    public function isManagerOf($user): bool
    {
        if (!$user || !$user->employee) {
            return false;
        }

        return $this->project_manager_id === $user->employee->id;
    }
}
