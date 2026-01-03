<?php

namespace App\Models;

use App\Traits\HasRoleBasedAccess;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasRoleBasedAccess;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'deadline',
        'sort_order',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignees()
    {
        return $this->belongsToMany(Employee::class)->withPivot('assigned_at');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function standupEntries()
    {
        return $this->hasMany(StandupEntry::class);
    }

    public function subtasks()
    {
        return $this->hasMany(Subtask::class)->orderBy('sort_order');
    }

    public function pendingSubtasks()
    {
        return $this->hasMany(Subtask::class)->where('is_completed', false)->orderBy('sort_order');
    }

    public function completedSubtasks()
    {
        return $this->hasMany(Subtask::class)->where('is_completed', true)->orderBy('completed_at', 'desc');
    }

    public function getCompletedSubtasksCountAttribute(): int
    {
        return $this->subtasks()->where('is_completed', true)->count();
    }

    public function getTotalSubtasksCountAttribute(): int
    {
        return $this->subtasks()->count();
    }

    public function getSubtaskProgressAttribute(): int
    {
        $total = $this->total_subtasks_count;
        if ($total === 0) return 0;
        return (int) round(($this->completed_subtasks_count / $total) * 100);
    }

    public function hasSubtasks(): bool
    {
        return $this->subtasks()->exists();
    }

    public function allSubtasksCompleted(): bool
    {
        if (!$this->hasSubtasks()) return true;
        return $this->subtasks()->where('is_completed', false)->doesntExist();
    }

    public function updateProgressFromSubtasks(): void
    {
        // Auto-mark task as done when all subtasks are completed
        if ($this->hasSubtasks() && $this->allSubtasksCompleted() && $this->status !== 'done') {
            // Optionally auto-complete the task
            // $this->update(['status' => 'done']);
        }
    }

    public function addSubtask(string $title, ?string $description = null, ?int $assignedTo = null, $dueDate = null): Subtask
    {
        return $this->subtasks()->create([
            'title' => $title,
            'description' => $description,
            'assigned_to' => $assignedTo,
            'due_date' => $dueDate,
        ]);
    }

    public function reorderSubtasks(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            $this->subtasks()->where('id', $id)->update(['sort_order' => $index]);
        }
    }

    /**
     * Scope to filter tasks based on user role
     * - Super Admin: sees all tasks
     * - Project Manager: sees tasks in their projects
     * - Employee: sees only assigned tasks
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

        // Project Manager sees tasks in their projects
        if (self::isProjectManager($user)) {
            $projectIds = self::getAccessibleProjectIds($user);
            return $query->whereIn('project_id', $projectIds);
        }

        // Employee sees only assigned tasks
        if ($user->employee) {
            return $query->whereHas('assignees', function ($q) use ($user) {
                $q->where('employees.id', $user->employee->id);
            });
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * Check if user can access this task
     */
    public function isAccessibleBy($user): bool
    {
        if (!$user) {
            return false;
        }

        if (self::isSuperAdmin($user)) {
            return true;
        }

        if (self::isProjectManager($user)) {
            $projectIds = self::getAccessibleProjectIds($user);
            return in_array($this->project_id, $projectIds);
        }

        if ($user->employee) {
            return $this->assignees()->where('employees.id', $user->employee->id)->exists();
        }

        return false;
    }
}
