<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\Task;
use App\Traits\HasRoleBasedAccess;
use Carbon\Carbon;
use Filament\Pages\Page;
use BackedEnum;

class ProjectTimeline extends Page
{
    use HasRoleBasedAccess;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Project Timeline';
    protected static ?int $navigationSort = 5;
    protected string $view = 'filament.pages.project-timeline';

    public static function shouldRegisterNavigation(): bool
    {
        return self::isSuperAdmin();
    }

    public static function canAccess(): bool
    {
        return self::isSuperAdmin();
    }

    public ?int $selectedProject = null;
    public string $viewMode = 'month'; // week, month, quarter
    public ?string $startDate = null;
    public array $tasks = [];
    public array $timelineData = [];
    public ?Project $project = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $firstProject = Project::forCurrentEmployee()->first();
        if ($firstProject) {
            $this->selectedProject = $firstProject->id;
        }
        $this->loadTimeline();
    }

    public function updatedSelectedProject(): void
    {
        $this->loadTimeline();
    }

    public function updatedViewMode(): void
    {
        $this->loadTimeline();
    }

    public function previousPeriod(): void
    {
        $date = Carbon::parse($this->startDate);
        $this->startDate = match($this->viewMode) {
            'week' => $date->subWeek()->format('Y-m-d'),
            'month' => $date->subMonth()->format('Y-m-d'),
            'quarter' => $date->subMonths(3)->format('Y-m-d'),
        };
        $this->loadTimeline();
    }

    public function nextPeriod(): void
    {
        $date = Carbon::parse($this->startDate);
        $this->startDate = match($this->viewMode) {
            'week' => $date->addWeek()->format('Y-m-d'),
            'month' => $date->addMonth()->format('Y-m-d'),
            'quarter' => $date->addMonths(3)->format('Y-m-d'),
        };
        $this->loadTimeline();
    }

    public function goToToday(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->loadTimeline();
    }

    public function loadTimeline(): void
    {
        if (!$this->selectedProject) {
            $this->tasks = [];
            $this->timelineData = [];
            return;
        }

        $this->project = Project::find($this->selectedProject);
        
        $startDate = Carbon::parse($this->startDate);
        $endDate = match($this->viewMode) {
            'week' => $startDate->copy()->addWeek(),
            'month' => $startDate->copy()->addMonth(),
            'quarter' => $startDate->copy()->addMonths(3),
        };

        // Get tasks for this project
        $tasks = Task::where('project_id', $this->selectedProject)
            ->with(['assignees.user'])
            ->orderBy('deadline')
            ->orderBy('created_at')
            ->get();

        $this->tasks = $tasks->map(function ($task) use ($startDate, $endDate) {
            $taskStart = $task->created_at;
            $taskEnd = $task->deadline ? Carbon::parse($task->deadline) : $taskStart->copy()->addDays(7);
            
            // Calculate position and width as percentage
            $totalDays = $startDate->diffInDays($endDate);
            $startOffset = max(0, $startDate->diffInDays($taskStart, false));
            $duration = max(1, $taskStart->diffInDays($taskEnd));
            
            $leftPercent = ($startOffset / $totalDays) * 100;
            $widthPercent = ($duration / $totalDays) * 100;
            
            // Clamp values
            if ($leftPercent < 0) {
                $widthPercent += $leftPercent;
                $leftPercent = 0;
            }
            if ($leftPercent + $widthPercent > 100) {
                $widthPercent = 100 - $leftPercent;
            }

            $isVisible = $leftPercent < 100 && $widthPercent > 0;

            return [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status,
                'priority' => $task->priority,
                'deadline' => $task->deadline,
                'assignees' => $task->assignees->map(fn($a) => [
                    'name' => $a->user->name ?? 'Unknown',
                    'initials' => strtoupper(substr($a->user->name ?? 'U', 0, 2)),
                ])->toArray(),
                'left' => $leftPercent,
                'width' => max(2, $widthPercent), // Minimum 2% width for visibility
                'visible' => $isVisible,
                'is_overdue' => $task->deadline && Carbon::parse($task->deadline)->isPast() && $task->status !== 'done',
            ];
        })->toArray();

        // Generate timeline headers
        $this->timelineData = $this->generateTimelineHeaders($startDate, $endDate);
    }

    private function generateTimelineHeaders(Carbon $start, Carbon $end): array
    {
        $headers = [];
        $current = $start->copy();
        
        while ($current < $end) {
            if ($this->viewMode === 'week') {
                $headers[] = [
                    'label' => $current->format('D j'),
                    'is_today' => $current->isToday(),
                    'is_weekend' => $current->isWeekend(),
                ];
                $current->addDay();
            } elseif ($this->viewMode === 'month') {
                $headers[] = [
                    'label' => $current->format('j'),
                    'is_today' => $current->isToday(),
                    'is_weekend' => $current->isWeekend(),
                ];
                $current->addDay();
            } else {
                $headers[] = [
                    'label' => $current->format('M j'),
                    'is_today' => $current->isToday(),
                    'is_weekend' => false,
                ];
                $current->addDays(7);
            }
        }

        return $headers;
    }

    public function getProjectOptions(): array
    {
        return Project::forCurrentEmployee()->pluck('name', 'id')->toArray();
    }

    public function getPeriodLabel(): string
    {
        $start = Carbon::parse($this->startDate);
        return match($this->viewMode) {
            'week' => $start->format('M j') . ' - ' . $start->copy()->addWeek()->subDay()->format('M j, Y'),
            'month' => $start->format('F Y'),
            'quarter' => $start->format('M Y') . ' - ' . $start->copy()->addMonths(3)->subDay()->format('M Y'),
        };
    }
}
