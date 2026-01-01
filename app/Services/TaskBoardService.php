<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskBoardService
{
    public function getTasksByStatus(array $filters = []): array
    {
        $query = Task::with(['project', 'assignees.user']);

        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['deadline_from'])) {
            $query->where('deadline', '>=', $filters['deadline_from']);
        }

        if (!empty($filters['deadline_to'])) {
            $query->where('deadline', '<=', $filters['deadline_to']);
        }

        if (!empty($filters['my_tasks_only']) && auth()->user()->employee) {
            $query->whereHas('assignees', function ($q) {
                $q->where('employees.id', auth()->user()->employee->id);
            });
        }

        $tasks = $query->get();

        return [
            'to_do' => $tasks->where('status', 'to_do')->values(),
            'in_progress' => $tasks->where('status', 'in_progress')->values(),
            'done' => $tasks->where('status', 'done')->values(),
        ];
    }

    public function updateTaskStatus(Task $task, string $status): bool
    {
        return $task->update(['status' => $status]);
    }

    public function getMyTasks(): Collection
    {
        if (!auth()->user()->employee) {
            return collect();
        }

        return Task::with(['project', 'assignees.user'])
            ->whereHas('assignees', function ($q) {
                $q->where('employees.id', auth()->user()->employee->id);
            })
            ->where('status', '!=', 'done')
            ->orderBy('priority', 'desc')
            ->orderBy('deadline')
            ->get();
    }
}
