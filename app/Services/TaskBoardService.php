<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Employee;
use App\Traits\HasRoleBasedAccess;
use Illuminate\Database\Eloquent\Collection;

class TaskBoardService
{
    use HasRoleBasedAccess;

    public function getTasksByStatus(array $filters = []): array
    {
        $query = Task::with(['project', 'assignees.user', 'subtasks'])
            ->forCurrentEmployee();

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

        // "My Tasks Only" filter for admins/managers who want to see only their assigned tasks
        if (!empty($filters['my_tasks_only']) && auth()->user()->employee) {
            $query->whereHas('assignees', function ($q) {
                $q->where('employees.id', auth()->user()->employee->id);
            });
        }

        $tasks = $query->orderBy('sort_order')->orderBy('priority', 'desc')->get();

        return [
            'to_do' => $tasks->where('status', 'to_do')->values(),
            'in_progress' => $tasks->where('status', 'in_progress')->values(),
            'done' => $tasks->where('status', 'done')->values(),
        ];
    }

    public function getTasksByStatusWithSwimlanes(array $filters = [], string $groupBy = 'none'): array
    {
        $query = Task::with(['project', 'assignees.user', 'subtasks'])
            ->forCurrentEmployee();

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

        $tasks = $query->orderBy('sort_order')->orderBy('priority', 'desc')->get();

        if ($groupBy === 'none') {
            return [
                'swimlanes' => [
                    [
                        'id' => 'all',
                        'name' => 'All Tasks',
                        'tasks' => [
                            'to_do' => $tasks->where('status', 'to_do')->values(),
                            'in_progress' => $tasks->where('status', 'in_progress')->values(),
                            'done' => $tasks->where('status', 'done')->values(),
                        ],
                    ],
                ],
            ];
        }

        if ($groupBy === 'priority') {
            return $this->groupByPriority($tasks);
        }

        if ($groupBy === 'assignee') {
            return $this->groupByAssignee($tasks);
        }

        return [
            'swimlanes' => [
                [
                    'id' => 'all',
                    'name' => 'All Tasks',
                    'tasks' => [
                        'to_do' => $tasks->where('status', 'to_do')->values(),
                        'in_progress' => $tasks->where('status', 'in_progress')->values(),
                        'done' => $tasks->where('status', 'done')->values(),
                    ],
                ],
            ],
        ];
    }

    protected function groupByPriority($tasks): array
    {
        $priorities = [
            'high' => ['id' => 'high', 'name' => '🔴 High Priority', 'color' => 'danger'],
            'medium' => ['id' => 'medium', 'name' => '🟡 Medium Priority', 'color' => 'warning'],
            'low' => ['id' => 'low', 'name' => '🟢 Low Priority', 'color' => 'success'],
        ];

        $swimlanes = [];
        foreach ($priorities as $key => $priority) {
            $priorityTasks = $tasks->where('priority', $key);
            $swimlanes[] = [
                'id' => $priority['id'],
                'name' => $priority['name'],
                'color' => $priority['color'],
                'tasks' => [
                    'to_do' => $priorityTasks->where('status', 'to_do')->values(),
                    'in_progress' => $priorityTasks->where('status', 'in_progress')->values(),
                    'done' => $priorityTasks->where('status', 'done')->values(),
                ],
            ];
        }

        return ['swimlanes' => $swimlanes];
    }

    protected function groupByAssignee($tasks): array
    {
        // Get all unique assignees from tasks
        $assigneeIds = $tasks->flatMap(fn($task) => $task->assignees->pluck('id'))->unique();
        $employees = Employee::with('user')->whereIn('id', $assigneeIds)->get();

        $swimlanes = [];

        // Unassigned tasks
        $unassignedTasks = $tasks->filter(fn($task) => $task->assignees->isEmpty());
        if ($unassignedTasks->isNotEmpty()) {
            $swimlanes[] = [
                'id' => 'unassigned',
                'name' => '👤 Unassigned',
                'color' => 'gray',
                'tasks' => [
                    'to_do' => $unassignedTasks->where('status', 'to_do')->values(),
                    'in_progress' => $unassignedTasks->where('status', 'in_progress')->values(),
                    'done' => $unassignedTasks->where('status', 'done')->values(),
                ],
            ];
        }

        // Tasks by assignee
        foreach ($employees as $employee) {
            $employeeTasks = $tasks->filter(fn($task) => $task->assignees->contains('id', $employee->id));
            $swimlanes[] = [
                'id' => 'employee_' . $employee->id,
                'name' => $employee->user->name ?? 'Unknown',
                'color' => 'primary',
                'tasks' => [
                    'to_do' => $employeeTasks->where('status', 'to_do')->values(),
                    'in_progress' => $employeeTasks->where('status', 'in_progress')->values(),
                    'done' => $employeeTasks->where('status', 'done')->values(),
                ],
            ];
        }

        return ['swimlanes' => $swimlanes];
    }

    public function updateTaskStatus(Task $task, string $status): bool
    {
        return $task->update(['status' => $status]);
    }

    public function updateTaskOrder(int $taskId, string $status, int $newIndex): bool
    {
        $task = Task::find($taskId);
        if (!$task) {
            return false;
        }

        $tasksInColumn = Task::where('status', $status)
            ->where('id', '!=', $taskId)
            ->orderBy('sort_order')
            ->get();

        $task->status = $status;

        $order = 0;
        $inserted = false;
        
        foreach ($tasksInColumn as $t) {
            if ($order === $newIndex && !$inserted) {
                $task->sort_order = $order;
                $task->save();
                $inserted = true;
                $order++;
            }
            $t->sort_order = $order;
            $t->save();
            $order++;
        }

        if (!$inserted) {
            $task->sort_order = $order;
            $task->save();
        }

        return true;
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
            ->orderBy('sort_order')
            ->orderBy('priority', 'desc')
            ->orderBy('deadline')
            ->get();
    }
}
