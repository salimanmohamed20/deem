<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subtask extends Model
{
    protected $fillable = [
        'task_id',
        'title',
        'description',
        'is_completed',
        'sort_order',
        'completed_by',
        'completed_at',
        'due_date',
        'assigned_to',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'due_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function ($subtask) {
            if (!$subtask->sort_order) {
                $maxOrder = static::where('task_id', $subtask->task_id)->max('sort_order') ?? 0;
                $subtask->sort_order = $maxOrder + 1;
            }
        });
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function completedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'completed_by');
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function markComplete(?int $employeeId = null): void
    {
        $this->is_completed = true;
        $this->completed_by = $employeeId ?? auth()->user()?->employee?->id;
        $this->completed_at = now();
        $this->save();
    }

    public function markIncomplete(): void
    {
        $this->is_completed = false;
        $this->completed_by = null;
        $this->completed_at = null;
        $this->save();
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !$this->is_completed;
    }

    public function isDueSoon(): bool
    {
        return $this->due_date && 
               !$this->due_date->isPast() && 
               $this->due_date->diffInDays(now()) <= 2 && 
               !$this->is_completed;
    }
}
