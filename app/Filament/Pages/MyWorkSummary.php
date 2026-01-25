<?php

namespace App\Filament\Pages;

use App\Models\Task;
use App\Models\Standup;
use App\Traits\HasRoleBasedAccess;
use Carbon\Carbon;
use Filament\Pages\Page;
use BackedEnum;

class MyWorkSummary extends Page
{
    use HasRoleBasedAccess;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'My Work';
    protected static ?int $navigationSort = 0;
    protected string $view = 'filament.pages.my-work-summary';

    public array $todayTasks = [];
    public array $upcomingTasks = [];
    public array $overdueTasks = [];
    public array $recentActivity = [];
    public array $stats = [];
    public ?Standup $todayStandup = null;
    public bool $hasStandupToday = false;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $user = auth()->user();
        $employee = $user->employee;

        // If user doesn't have an employee record, don't show any data
        if (!$employee) {
            return;
        }

        $employeeId = $employee->id;
        $today = Carbon::today();

        // Base query for user's tasks
        $myTasksQuery = Task::whereHas('assignees', function ($q) use ($employeeId) {
            $q->where('employees.id', $employeeId);
        });

        // Today's tasks (due today)
        $this->todayTasks = (clone $myTasksQuery)
            ->whereDate('deadline', $today)
            ->where('status', '!=', 'done')
            ->with('project')
            ->orderBy('priority', 'desc')
            ->get()
            ->toArray();

        // Upcoming tasks (next 7 days)
        $this->upcomingTasks = (clone $myTasksQuery)
            ->whereDate('deadline', '>', $today)
            ->whereDate('deadline', '<=', $today->copy()->addDays(7))
            ->where('status', '!=', 'done')
            ->with('project')
            ->orderBy('deadline')
            ->limit(5)
            ->get()
            ->toArray();

        // Overdue tasks
        $this->overdueTasks = (clone $myTasksQuery)
            ->whereDate('deadline', '<', $today)
            ->where('status', '!=', 'done')
            ->with('project')
            ->orderBy('deadline')
            ->get()
            ->toArray();

        // Recent activity (tasks updated in last 7 days)
        $this->recentActivity = (clone $myTasksQuery)
            ->where('updated_at', '>=', $today->copy()->subDays(7))
            ->with('project')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'project' => $task->project->name,
                    'status' => $task->status,
                    'updated_at' => $task->updated_at,
                ];
            })
            ->toArray();

        // Stats
        $totalTasks = (clone $myTasksQuery)->count();
        $completedTasks = (clone $myTasksQuery)->where('status', 'done')->count();
        $inProgressTasks = (clone $myTasksQuery)->where('status', 'in_progress')->count();
        $completedThisWeek = (clone $myTasksQuery)
            ->where('status', 'done')
            ->where('updated_at', '>=', $today->copy()->startOfWeek())
            ->count();

        $this->stats = [
            'total' => $totalTasks,
            'completed' => $completedTasks,
            'in_progress' => $inProgressTasks,
            'completed_this_week' => $completedThisWeek,
            'overdue_count' => count($this->overdueTasks),
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
        ];

        // Today's standup - Check if employee has submitted standup today
        // This banner shows for ALL employees (not just super admins)
        $this->todayStandup = Standup::where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->first();
        $this->hasStandupToday = $this->todayStandup !== null;
    }

    public function getTitle(): string
    {
        $hour = now()->hour;
        $greeting = match(true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };
        
        return $greeting . ', ' . (auth()->user()->name ?? 'there') . '!';
    }
}
