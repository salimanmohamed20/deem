<?php

namespace App\Filament\Pages;

use App\Models\Employee;
use App\Models\Project;
use App\Models\Standup;
use App\Models\Task;
use App\Traits\HasRoleBasedAccess;
use Carbon\Carbon;
use Filament\Pages\Page;
use BackedEnum;

class WeeklyReport extends Page
{
    use HasRoleBasedAccess;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Weekly Report';
    protected static ?int $navigationSort = 6;
    protected string $view = 'filament.pages.weekly-report';

    public static function shouldRegisterNavigation(): bool
    {
        return self::isSuperAdmin();
    }

    public static function canAccess(): bool
    {
        return self::isSuperAdmin();
    }

    public string $weekStart;
    public string $weekEnd;
    public array $summary = [];
    public array $tasksByProject = [];
    public array $topPerformers = [];
    public array $completedTasks = [];
    public array $newTasks = [];
    public array $standupStats = [];

    public function mount(): void
    {
        $this->weekStart = now()->startOfWeek()->format('Y-m-d');
        $this->weekEnd = now()->endOfWeek()->format('Y-m-d');
        $this->loadReport();
    }

    public function previousWeek(): void
    {
        $this->weekStart = Carbon::parse($this->weekStart)->subWeek()->format('Y-m-d');
        $this->weekEnd = Carbon::parse($this->weekEnd)->subWeek()->format('Y-m-d');
        $this->loadReport();
    }

    public function nextWeek(): void
    {
        $this->weekStart = Carbon::parse($this->weekStart)->addWeek()->format('Y-m-d');
        $this->weekEnd = Carbon::parse($this->weekEnd)->addWeek()->format('Y-m-d');
        $this->loadReport();
    }

    public function currentWeek(): void
    {
        $this->weekStart = now()->startOfWeek()->format('Y-m-d');
        $this->weekEnd = now()->endOfWeek()->format('Y-m-d');
        $this->loadReport();
    }

    public function loadReport(): void
    {
        $start = Carbon::parse($this->weekStart)->startOfDay();
        $end = Carbon::parse($this->weekEnd)->endOfDay();

        $user = auth()->user();
        $isAdmin = self::isSuperAdmin($user);
        $isManager = self::isProjectManager($user);

        // Base query depending on role
        $projectIds = Project::forCurrentEmployee()->pluck('id');

        // Summary stats
        $tasksQuery = Task::whereIn('project_id', $projectIds);
        
        $completedThisWeek = (clone $tasksQuery)
            ->where('status', 'done')
            ->whereBetween('updated_at', [$start, $end])
            ->count();

        $createdThisWeek = (clone $tasksQuery)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $inProgressCount = (clone $tasksQuery)
            ->where('status', 'in_progress')
            ->count();

        $overdueCount = (clone $tasksQuery)
            ->where('status', '!=', 'done')
            ->where('deadline', '<', now())
            ->count();

        // Previous week for comparison
        $prevStart = $start->copy()->subWeek();
        $prevEnd = $end->copy()->subWeek();
        
        $completedLastWeek = (clone $tasksQuery)
            ->where('status', 'done')
            ->whereBetween('updated_at', [$prevStart, $prevEnd])
            ->count();

        $this->summary = [
            'completed' => $completedThisWeek,
            'completed_change' => $completedThisWeek - $completedLastWeek,
            'created' => $createdThisWeek,
            'in_progress' => $inProgressCount,
            'overdue' => $overdueCount,
        ];

        // Tasks by project
        $this->tasksByProject = Project::whereIn('id', $projectIds)
            ->withCount([
                'tasks as completed_count' => function ($q) use ($start, $end) {
                    $q->where('status', 'done')
                      ->whereBetween('updated_at', [$start, $end]);
                },
                'tasks as total_count',
                'tasks as active_count' => function ($q) {
                    $q->where('status', '!=', 'done');
                },
            ])
            ->having('completed_count', '>', 0)
            ->orHaving('active_count', '>', 0)
            ->orderByDesc('completed_count')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'name' => $p->name,
                'completed' => $p->completed_count,
                'active' => $p->active_count,
                'total' => $p->total_count,
                'progress' => $p->total_count > 0 ? round((($p->total_count - $p->active_count) / $p->total_count) * 100) : 0,
            ])
            ->toArray();

        // Top performers (employees who completed most tasks)
        if ($isAdmin || $isManager) {
            $this->topPerformers = Employee::with('user')
                ->whereHas('tasks', function ($q) use ($start, $end, $projectIds) {
                    $q->whereIn('project_id', $projectIds)
                      ->where('status', 'done')
                      ->whereBetween('tasks.updated_at', [$start, $end]);
                })
                ->withCount(['tasks as completed_count' => function ($q) use ($start, $end, $projectIds) {
                    $q->whereIn('project_id', $projectIds)
                      ->where('status', 'done')
                      ->whereBetween('tasks.updated_at', [$start, $end]);
                }])
                ->orderByDesc('completed_count')
                ->limit(5)
                ->get()
                ->map(fn($e) => [
                    'name' => $e->user->name ?? 'Unknown',
                    'initials' => strtoupper(substr($e->user->name ?? 'U', 0, 2)),
                    'completed' => $e->completed_count,
                ])
                ->toArray();
        }

        // Completed tasks list
        $this->completedTasks = Task::whereIn('project_id', $projectIds)
            ->where('status', 'done')
            ->whereBetween('updated_at', [$start, $end])
            ->with(['project', 'assignees.user'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get()
            ->map(fn($t) => [
                'title' => $t->title,
                'project' => $t->project->name,
                'assignee' => $t->assignees->first()?->user?->name ?? 'Unassigned',
                'completed_at' => $t->updated_at->format('M j'),
                'priority' => $t->priority,
            ])
            ->toArray();

        // New tasks list
        $this->newTasks = Task::whereIn('project_id', $projectIds)
            ->whereBetween('created_at', [$start, $end])
            ->with(['project', 'assignees.user'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn($t) => [
                'title' => $t->title,
                'project' => $t->project->name,
                'assignee' => $t->assignees->first()?->user?->name ?? 'Unassigned',
                'status' => $t->status,
                'priority' => $t->priority,
            ])
            ->toArray();

        // Standup stats
        if ($isAdmin || $isManager) {
            $employeeIds = Employee::whereHas('tasks', function ($q) use ($projectIds) {
                $q->whereIn('project_id', $projectIds);
            })->pluck('id');

            $totalEmployees = $employeeIds->count();
            $workingDays = $this->getWorkingDays($start, $end);
            $expectedStandups = $totalEmployees * $workingDays;
            
            $actualStandups = Standup::whereIn('employee_id', $employeeIds)
                ->whereBetween('date', [$start, $end])
                ->count();

            $this->standupStats = [
                'submitted' => $actualStandups,
                'expected' => $expectedStandups,
                'rate' => $expectedStandups > 0 ? round(($actualStandups / $expectedStandups) * 100) : 0,
            ];
        }
    }

    private function getWorkingDays(Carbon $start, Carbon $end): int
    {
        $count = 0;
        $current = $start->copy();
        while ($current <= $end) {
            if ($current->isWeekday()) {
                $count++;
            }
            $current->addDay();
        }
        return $count;
    }

    public function getWeekLabel(): string
    {
        $start = Carbon::parse($this->weekStart);
        $end = Carbon::parse($this->weekEnd);
        
        if ($start->isSameMonth($end)) {
            return $start->format('M j') . ' - ' . $end->format('j, Y');
        }
        return $start->format('M j') . ' - ' . $end->format('M j, Y');
    }

    public function isCurrentWeek(): bool
    {
        return Carbon::parse($this->weekStart)->isCurrentWeek();
    }
}
