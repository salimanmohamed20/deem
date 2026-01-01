<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\Task;
use App\Services\TaskBoardService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Livewire\Attributes\On;
use BackedEnum;

class TaskBoard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    protected string $view = 'filament.pages.task-board';

    protected static ?int $navigationSort = 1;

    public ?array $filters = [
        'project_id' => null,
        'priority' => null,
        'deadline_from' => null,
        'deadline_to' => null,
        'my_tasks_only' => false,
    ];

    public array $tasks = [];

    public function mount(): void
    {
        $this->loadTasks();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('filters.project_id')
                    ->label('Project')
                    ->options(Project::pluck('name', 'id'))
                    ->placeholder('All Projects')
                    ->live(),
                Select::make('filters.priority')
                    ->label('Priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ])
                    ->placeholder('All Priorities')
                    ->live(),
                DatePicker::make('filters.deadline_from')
                    ->label('From')
                    ->live(),
                DatePicker::make('filters.deadline_to')
                    ->label('To')
                    ->live(),
                Toggle::make('filters.my_tasks_only')
                    ->label('My Tasks Only')
                    ->live(),
            ])
            ->columns(5);
    }

    public function updated($property): void
    {
        if (str_starts_with($property, 'filters')) {
            $this->loadTasks();
        }
    }

    public function loadTasks(): void
    {
        $service = new TaskBoardService();
        $this->tasks = $service->getTasksByStatus($this->filters);
    }

    #[On('task-moved')]
    public function updateTaskStatus(int $taskId, string $newStatus): void
    {
        $task = Task::find($taskId);
        if ($task) {
            $task->update(['status' => $newStatus]);
            $this->loadTasks();
        }
    }

    public function getTasksProperty(): array
    {
        return $this->tasks;
    }
}
