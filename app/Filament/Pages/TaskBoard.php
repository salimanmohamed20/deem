<?php

namespace App\Filament\Pages;

use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskBoardService;
use App\Traits\HasRoleBasedAccess;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Livewire\Attributes\On;
use BackedEnum;

class TaskBoard extends Page implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions, HasRoleBasedAccess;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';
    protected string $view = 'filament.pages.task-board';
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = true;

    public ?array $filters = [
        'project_id' => null,
        'priority' => null,
        'deadline_from' => null,
        'deadline_to' => null,
        'my_tasks_only' => false,
        'group_by' => 'none',
    ];

    public array $tasks = [];
    public array $swimlanes = [];
    public ?Task $selectedTask = null;

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        if (self::isEmployee() && !self::isProjectManager() && !self::isSuperAdmin()) {
            return 'My Tasks';
        }
        return 'Task Board';
    }

    public function mount(): void
    {
        $this->loadTasks();
    }

    public function form(Schema $schema): Schema
    {
        $user = auth()->user();
        $isAdmin = self::isSuperAdmin($user);
        $isManager = self::isProjectManager($user);

        $projectOptions = Project::forCurrentEmployee()->pluck('name', 'id');

        $filterFields = [
            Select::make('filters.project_id')
                ->label('Project')
                ->options($projectOptions)
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
        ];

        if ($isAdmin || $isManager) {
            $filterFields[] = Toggle::make('filters.my_tasks_only')
                ->label('My Tasks Only')
                ->inline()
                ->live();
        }

        $filterFields[] = Select::make('filters.group_by')
            ->label('Group By')
            ->options([
                'none' => 'No Grouping',
                'priority' => 'Priority',
                'assignee' => 'Assignee',
            ])
            ->default('none')
            ->live();

        return $schema
            ->components($filterFields)
            ->columns([
                'default' => 2,
                'sm' => 3,
                'md' => count($filterFields),
                'lg' => count($filterFields),
            ]);
    }

    protected function getHeaderActions(): array
    {
        $canCreate = self::isSuperAdmin() || self::isProjectManager();

        if (!$canCreate) {
            return [];
        }

        return [
            Action::make('createTask')
                ->label('New Task')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form([
                    Select::make('project_id')
                        ->label('Project')
                        ->options(Project::forCurrentEmployee()->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Enter task title'),
                    RichEditor::make('description')
                        ->label('Description')
                        ->placeholder('Describe the task...')
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'bulletList',
                            'orderedList',
                        ]),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'to_do' => 'To Do',
                            'in_progress' => 'In Progress',
                            'done' => 'Done',
                        ])
                        ->default('to_do')
                        ->required(),
                    Select::make('priority')
                        ->label('Priority')
                        ->options([
                            'low' => 'Low',
                            'medium' => 'Medium',
                            'high' => 'High',
                        ])
                        ->default('medium')
                        ->required(),
                    DatePicker::make('deadline')
                        ->label('Deadline')
                        ->native(false),
                    CheckboxList::make('assignees')
                        ->label('Assign To')
                        ->options(Employee::with('user')->get()->pluck('user.name', 'id'))
                        ->columns(2)
                        ->searchable(),
                ])
                ->modalHeading('Create New Task')
                ->modalWidth('lg')
                ->action(function (array $data): void {
                    $task = Task::create([
                        'project_id' => $data['project_id'],
                        'title' => $data['title'],
                        'description' => $data['description'] ?? null,
                        'status' => $data['status'],
                        'priority' => $data['priority'],
                        'deadline' => $data['deadline'] ?? null,
                    ]);

                    if (!empty($data['assignees'])) {
                        $task->assignees()->attach($data['assignees'], [
                            'assigned_at' => now(),
                        ]);
                    }

                    $this->loadTasks();

                    Notification::make()
                        ->title('Task created successfully')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewTaskAction(): Action
    {
        return Action::make('viewTask')
            ->modalHeading(fn () => $this->selectedTask?->title ?? 'Task Details')
            ->modalWidth('lg')
            ->modalContent(view('filament.pages.partials.task-quick-view', ['task' => $this->selectedTask]))
            ->modalFooterActions([
                Action::make('edit')
                    ->label('Edit Task')
                    ->url(fn () => $this->selectedTask ? route('filament.admin.resources.tasks.edit', $this->selectedTask) : '#')
                    ->icon('heroicon-o-pencil')
                    ->color('primary'),
                Action::make('close')
                    ->label('Close')
                    ->color('gray')
                    ->close(),
            ]);
    }

    public function openTaskQuickView(int $taskId): void
    {
        $this->selectedTask = Task::with(['project', 'assignees.user', 'comments.author.user', 'attachments'])->find($taskId);
        
        if ($this->selectedTask && $this->selectedTask->isAccessibleBy(auth()->user())) {
            $this->mountAction('viewTask');
        }
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
        $groupBy = $this->filters['group_by'] ?? 'none';
        
        if ($groupBy === 'none') {
            $this->tasks = $service->getTasksByStatus($this->filters);
            $this->swimlanes = [];
        } else {
            $result = $service->getTasksByStatusWithSwimlanes($this->filters, $groupBy);
            $this->swimlanes = $result['swimlanes'];
            $this->tasks = [];
        }
    }

    #[On('task-moved')]
    public function updateTaskStatus(int $taskId, string $newStatus, ?int $newIndex = null): void
    {
        $task = Task::find($taskId);
        if (!$task || !$task->isAccessibleBy(auth()->user())) {
            return;
        }

        $service = new TaskBoardService();
        
        if ($newIndex !== null) {
            $service->updateTaskOrder($taskId, $newStatus, $newIndex);
        } else {
            $task->update(['status' => $newStatus]);
        }
        
        $this->loadTasks();
    }

    #[On('task-reordered')]
    public function reorderTask(int $taskId, string $status, int $newIndex): void
    {
        $task = Task::find($taskId);
        if (!$task || !$task->isAccessibleBy(auth()->user())) {
            return;
        }

        $service = new TaskBoardService();
        $service->updateTaskOrder($taskId, $status, $newIndex);
        $this->loadTasks();
    }

    public function getTasksProperty(): array
    {
        return $this->tasks;
    }
}
