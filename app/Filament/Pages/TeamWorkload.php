<?php

namespace App\Filament\Pages;

use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use App\Traits\HasRoleBasedAccess;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use BackedEnum;

class TeamWorkload extends Page
{
    use HasRoleBasedAccess;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Team Workload';
    protected static ?int $navigationSort = 10;
    protected string $view = 'filament.pages.team-workload';

    public ?int $selectedProject = null;
    public Collection $teamMembers;
    public array $workloadStats = [];

    public static function canAccess(): bool
    {
        return self::isSuperAdmin() || self::isProjectManager();
    }

    public function mount(): void
    {
        $this->teamMembers = collect();
        $this->loadWorkload();
    }

    public function updatedSelectedProject(): void
    {
        $this->loadWorkload();
    }

    public function loadWorkload(): void
    {
        $user = auth()->user();
        
        // Get accessible projects
        $projectQuery = Project::forCurrentEmployee();
        
        if ($this->selectedProject) {
            $projectQuery->where('id', $this->selectedProject);
        }
        
        $projectIds = $projectQuery->pluck('id');

        // Get employees working on these projects
        $employees = Employee::with(['user', 'jobTitle'])
            ->where('is_active', true)
            ->where(function ($query) use ($projectIds) {
                $query->whereHas('tasks', function ($q) use ($projectIds) {
                    $q->whereIn('project_id', $projectIds);
                })
                ->orWhereHas('teams.projects', function ($q) use ($projectIds) {
                    $q->whereIn('projects.id', $projectIds);
                });
            })
            ->get();

        $this->teamMembers = $employees->map(function ($employee) use ($projectIds) {
            $tasksQuery = Task::whereHas('assignees', function ($q) use ($employee) {
                $q->where('employees.id', $employee->id);
            })->whereIn('project_id', $projectIds);

            $totalTasks = (clone $tasksQuery)->count();
            $todoTasks = (clone $tasksQuery)->where('status', 'to_do')->count();
            $inProgressTasks = (clone $tasksQuery)->where('status', 'in_progress')->count();
            $doneTasks = (clone $tasksQuery)->where('status', 'done')->count();
            $overdueTasks = (clone $tasksQuery)
                ->where('status', '!=', 'done')
                ->where('deadline', '<', now())
                ->count();
            $highPriorityTasks = (clone $tasksQuery)
                ->where('status', '!=', 'done')
                ->where('priority', 'high')
                ->count();

            // Calculate workload score (active tasks weighted by priority)
            $activeTasks = (clone $tasksQuery)->where('status', '!=', 'done')->get();
            $workloadScore = $activeTasks->sum(function ($task) {
                return match($task->priority) {
                    'high' => 3,
                    'medium' => 2,
                    'low' => 1,
                    default => 1,
                };
            });

            return [
                'id' => $employee->id,
                'name' => $employee->user->name ?? 'Unknown',
                'job_title' => $employee->jobTitle->name ?? 'No Title',
                'avatar' => strtoupper(substr($employee->user->name ?? 'U', 0, 2)),
                'total_tasks' => $totalTasks,
                'todo' => $todoTasks,
                'in_progress' => $inProgressTasks,
                'done' => $doneTasks,
                'overdue' => $overdueTasks,
                'high_priority' => $highPriorityTasks,
                'workload_score' => $workloadScore,
                'completion_rate' => $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0,
            ];
        })->sortByDesc('workload_score')->values();

        // Calculate team stats
        $totalActiveTasks = $this->teamMembers->sum(fn($m) => $m['todo'] + $m['in_progress']);
        $totalOverdue = $this->teamMembers->sum('overdue');
        $avgWorkload = $this->teamMembers->count() > 0 
            ? round($this->teamMembers->avg('workload_score'), 1) 
            : 0;
        $maxWorkload = $this->teamMembers->max('workload_score') ?? 0;

        $this->workloadStats = [
            'team_size' => $this->teamMembers->count(),
            'total_active_tasks' => $totalActiveTasks,
            'total_overdue' => $totalOverdue,
            'avg_workload' => $avgWorkload,
            'max_workload' => $maxWorkload,
        ];
    }

    public function getProjectOptions(): array
    {
        return Project::forCurrentEmployee()
            ->pluck('name', 'id')
            ->prepend('All Projects', '')
            ->toArray();
    }
}
