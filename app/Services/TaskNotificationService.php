<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Task;
use App\Models\TaskComment;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskCommentNotification;
use Illuminate\Support\Facades\Log;

class TaskNotificationService
{
    /**
     * Notify employees when they are assigned to a task
     */
    public function notifyTaskAssigned(Task $task, array $employeeIds, ?string $assignedBy = null): void
    {
        $assignedByName = $assignedBy ?? auth()->user()?->name ?? 'System';

        foreach ($employeeIds as $employeeId) {
            $employee = Employee::with('user')->find($employeeId);
            if ($employee && $employee->user) {
                $employee->user->notify(new TaskAssignedNotification($task, $assignedByName));
            }
        }
    }

    /**
     * Notify task assignees about a new comment
     */
    public function notifyTaskComment(Task $task, TaskComment $comment, array $mentionedEmployeeIds = []): void
    {
        $commentByName = $comment->author?->user?->name ?? 'Someone';
        $currentEmployeeId = (int) auth()->user()?->employee?->id;

        // Convert all IDs to integers for proper comparison
        $mentionedEmployeeIds = array_map('intval', $mentionedEmployeeIds);

        // Notify mentioned employees
        foreach ($mentionedEmployeeIds as $employeeId) {
            if ($employeeId === $currentEmployeeId) {
                continue; // Don't notify yourself
            }
            
            $employee = Employee::with('user')->find($employeeId);
            if ($employee && $employee->user) {
                $employee->user->notify(new TaskCommentNotification($task, $comment, $commentByName, true));
            }
        }

        // Notify other task assignees (not mentioned and not the commenter)
        $task->load('assignees.user');
        foreach ($task->assignees as $assignee) {
            $assigneeId = (int) $assignee->id;
            
            if ($assigneeId === $currentEmployeeId) {
                continue; // Don't notify yourself
            }
            
            if (in_array($assigneeId, $mentionedEmployeeIds, true)) {
                continue; // Already notified as mention
            }
            
            if ($assignee->user) {
                $assignee->user->notify(new TaskCommentNotification($task, $comment, $commentByName, false));
            }
        }
    }
}
