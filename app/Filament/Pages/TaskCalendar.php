<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\Task;
use App\Traits\HasRoleBasedAccess;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use BackedEnum;

class TaskCalendar extends Page implements HasForms
{
    use InteractsWithForms, HasRoleBasedAccess;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected string $view = 'filament.pages.task-calendar';
    protected static ?int $navigationSort = 2;
    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationLabel(): string
    {
        if (self::isEmployee() && !self::isProjectManager() && !self::isSuperAdmin()) {
            return 'My Calendar';
        }
        return 'Task Calendar';
    }

    public ?int $selectedProjectId = null;
    public array $calendarEvents = [];

    public function mount(): void
    {
        $this->loadEvents();
    }

    public function form(Schema $schema): Schema
    {
        $projectOptions = Project::forCurrentEmployee()->pluck('name', 'id');

        return $schema
            ->components([
                Select::make('selectedProjectId')
                    ->label('Filter by Project')
                    ->options($projectOptions)
                    ->placeholder('All Projects')
                    ->live(),
            ])
            ->columns(1);
    }

    public function updatedSelectedProjectId(): void
    {
        $this->loadEvents();
        $this->dispatch('calendar-events-updated', events: $this->calendarEvents);
    }

    public function loadEvents(): void
    {
        $query = Task::with(['project', 'assignees.user'])
            ->forCurrentEmployee()
            ->whereNotNull('deadline');

        if ($this->selectedProjectId) {
            $query->where('project_id', $this->selectedProjectId);
        }

        $tasks = $query->get();

        $this->calendarEvents = $tasks->map(function ($task) {
            $color = match ($task->status) {
                'to_do' => '#f59e0b',      // Yellow/Amber
                'in_progress' => '#3b82f6', // Blue
                'done' => '#22c55e',        // Green
                default => '#6b7280',       // Gray
            };

            $borderColor = match ($task->priority) {
                'high' => '#ef4444',    // Red border for high priority
                'medium' => '#f59e0b',  // Amber border for medium
                'low' => '#22c55e',     // Green border for low
                default => $color,
            };

            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->deadline,
                'backgroundColor' => $color,
                'borderColor' => $borderColor,
                'borderWidth' => $task->priority === 'high' ? 3 : 1,
                'extendedProps' => [
                    'project' => $task->project?->name,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'assignees' => $task->assignees->pluck('user.name')->implode(', '),
                    'taskId' => $task->id,
                ],
            ];
        })->toArray();
    }

    public function getCalendarEventsProperty(): array
    {
        return $this->calendarEvents;
    }

    public function updateTaskDate(int $taskId, string $newDate): void
    {
        $task = Task::find($taskId);
        
        if (!$task || !$task->isAccessibleBy(auth()->user())) {
            return;
        }

        // Only managers and admins can change dates
        if (!self::isSuperAdmin() && !self::isProjectManager()) {
            return;
        }

        $task->update(['deadline' => $newDate]);
        $this->loadEvents();
        $this->dispatch('calendar-events-updated', events: $this->calendarEvents);
    }
}
