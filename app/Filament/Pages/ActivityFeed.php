<?php

namespace App\Filament\Pages;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\Standup;
use App\Models\Project;
use App\Traits\HasRoleBasedAccess;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use BackedEnum;

class ActivityFeed extends Page
{
    use HasRoleBasedAccess;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationLabel = 'Activity Feed';
    protected static ?int $navigationSort = 7;
    protected string $view = 'filament.pages.activity-feed';

    public static function shouldRegisterNavigation(): bool
    {
        return self::isSuperAdmin();
    }

    public static function canAccess(): bool
    {
        return self::isSuperAdmin();
    }

    public string $filter = 'all';
    public Collection $activities;
    public int $limit = 30;

    public function mount(): void
    {
        $this->activities = collect();
        $this->loadActivities();
    }

    public function updatedFilter(): void
    {
        $this->loadActivities();
    }

    public function loadMore(): void
    {
        $this->limit += 20;
        $this->loadActivities();
    }

    public function loadActivities(): void
    {
        $projectIds = Project::forCurrentEmployee()->pluck('id');
        $activities = collect();

        // Task status changes (completed tasks)
        if ($this->filter === 'all' || $this->filter === 'tasks') {
            $completedTasks = Task::whereIn('project_id', $projectIds)
                ->where('status', 'done')
                ->where('updated_at', '>=', now()->subDays(7))
                ->with(['project', 'assignees.user'])
                ->orderByDesc('updated_at')
                ->limit($this->limit)
                ->get()
                ->map(fn($task) => [
                    'type' => 'task_completed',
                    'icon' => 'check-circle',
                    'color' => 'success',
                    'title' => 'Task completed',
                    'description' => $task->title,
                    'meta' => $task->project->name,
                    'user' => $task->assignees->first()?->user?->name ?? 'Someone',
                    'user_initials' => strtoupper(substr($task->assignees->first()?->user?->name ?? 'U', 0, 2)),
                    'timestamp' => $task->updated_at,
                    'link' => route('filament.admin.resources.tasks.edit', $task->id),
                ]);
            $activities = $activities->concat($completedTasks);

            // New tasks created
            $newTasks = Task::whereIn('project_id', $projectIds)
                ->where('created_at', '>=', now()->subDays(7))
                ->with(['project', 'assignees.user'])
                ->orderByDesc('created_at')
                ->limit($this->limit)
                ->get()
                ->map(fn($task) => [
                    'type' => 'task_created',
                    'icon' => 'plus-circle',
                    'color' => 'info',
                    'title' => 'New task created',
                    'description' => $task->title,
                    'meta' => $task->project->name,
                    'user' => 'System',
                    'user_initials' => 'SY',
                    'timestamp' => $task->created_at,
                    'link' => route('filament.admin.resources.tasks.edit', $task->id),
                ]);
            $activities = $activities->concat($newTasks);

            // Tasks moved to in progress
            $inProgressTasks = Task::whereIn('project_id', $projectIds)
                ->where('status', 'in_progress')
                ->where('updated_at', '>=', now()->subDays(7))
                ->whereColumn('updated_at', '!=', 'created_at')
                ->with(['project', 'assignees.user'])
                ->orderByDesc('updated_at')
                ->limit($this->limit)
                ->get()
                ->map(fn($task) => [
                    'type' => 'task_started',
                    'icon' => 'play',
                    'color' => 'warning',
                    'title' => 'Task started',
                    'description' => $task->title,
                    'meta' => $task->project->name,
                    'user' => $task->assignees->first()?->user?->name ?? 'Someone',
                    'user_initials' => strtoupper(substr($task->assignees->first()?->user?->name ?? 'U', 0, 2)),
                    'timestamp' => $task->updated_at,
                    'link' => route('filament.admin.resources.tasks.edit', $task->id),
                ]);
            $activities = $activities->concat($inProgressTasks);
        }

        // Comments
        if ($this->filter === 'all' || $this->filter === 'comments') {
            $comments = TaskComment::whereHas('task', fn($q) => $q->whereIn('project_id', $projectIds))
                ->where('created_at', '>=', now()->subDays(7))
                ->with(['task.project', 'author.user'])
                ->orderByDesc('created_at')
                ->limit($this->limit)
                ->get()
                ->map(fn($comment) => [
                    'type' => 'comment',
                    'icon' => 'chat-bubble-left',
                    'color' => 'gray',
                    'title' => 'New comment',
                    'description' => \Str::limit(strip_tags($comment->comment), 80),
                    'meta' => $comment->task->title,
                    'user' => $comment->author?->user?->name ?? 'Someone',
                    'user_initials' => strtoupper(substr($comment->author?->user?->name ?? 'U', 0, 2)),
                    'timestamp' => $comment->created_at,
                    'link' => route('filament.admin.resources.tasks.edit', $comment->task_id),
                ]);
            $activities = $activities->concat($comments);
        }

        // Standups
        if ($this->filter === 'all' || $this->filter === 'standups') {
            $standups = Standup::forCurrentEmployee()
                ->where('created_at', '>=', now()->subDays(7))
                ->with(['employee.user'])
                ->orderByDesc('created_at')
                ->limit($this->limit)
                ->get()
                ->map(fn($standup) => [
                    'type' => 'standup',
                    'icon' => 'clipboard-document-check',
                    'color' => 'primary',
                    'title' => 'Standup submitted',
                    'description' => 'Daily standup for ' . Carbon::parse($standup->date)->format('M j, Y'),
                    'meta' => null,
                    'user' => $standup->employee?->user?->name ?? 'Someone',
                    'user_initials' => strtoupper(substr($standup->employee?->user?->name ?? 'U', 0, 2)),
                    'timestamp' => $standup->created_at,
                    'link' => route('filament.admin.resources.standups.edit', $standup->id),
                ]);
            $activities = $activities->concat($standups);
        }

        // Sort by timestamp and limit
        $this->activities = $activities
            ->sortByDesc('timestamp')
            ->take($this->limit)
            ->values();
    }

    public function getFilterOptions(): array
    {
        return [
            'all' => 'All Activity',
            'tasks' => 'Tasks Only',
            'comments' => 'Comments Only',
            'standups' => 'Standups Only',
        ];
    }
}
