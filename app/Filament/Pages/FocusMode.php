<?php

namespace App\Filament\Pages;

use App\Models\Task;
use App\Models\TaskComment;
use App\Traits\HasRoleBasedAccess;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use BackedEnum;

class FocusMode extends Page
{
    use HasRoleBasedAccess;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationLabel = 'Focus Mode';
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.pages.focus-mode';

    public Collection $taskQueue;
    public ?Task $currentTask = null;
    public int $currentIndex = 0;
    public string $newComment = '';
    public bool $showCompleteConfirm = false;

    public function mount(): void
    {
        $this->loadTasks();
    }

    public function loadTasks(): void
    {
        $user = auth()->user();
        $employeeId = $user->employee?->id;

        if (!$employeeId) {
            $this->taskQueue = collect();
            return;
        }

        // Get tasks assigned to user, prioritized
        $this->taskQueue = Task::whereHas('assignees', function ($q) use ($employeeId) {
            $q->where('employees.id', $employeeId);
        })
        ->where('status', '!=', 'done')
        ->with(['project', 'assignees.user', 'comments.author.user'])
        ->orderByRaw("CASE 
            WHEN status = 'in_progress' THEN 1 
            WHEN priority = 'high' THEN 2 
            WHEN priority = 'medium' THEN 3 
            ELSE 4 
        END")
        ->orderBy('deadline')
        ->get();

        $this->selectTask(0);
    }

    public function selectTask(int $index): void
    {
        if ($index >= 0 && $index < $this->taskQueue->count()) {
            $this->currentIndex = $index;
            $this->currentTask = $this->taskQueue[$index];
            $this->showCompleteConfirm = false;
            $this->newComment = '';
        }
    }

    public function nextTask(): void
    {
        $nextIndex = $this->currentIndex + 1;
        if ($nextIndex < $this->taskQueue->count()) {
            $this->selectTask($nextIndex);
        }
    }

    public function previousTask(): void
    {
        $prevIndex = $this->currentIndex - 1;
        if ($prevIndex >= 0) {
            $this->selectTask($prevIndex);
        }
    }

    public function startTask(): void
    {
        if ($this->currentTask && $this->currentTask->status === 'to_do') {
            $this->currentTask->update(['status' => 'in_progress']);
            $this->currentTask->refresh();
            
            Notification::make()
                ->title('Task started!')
                ->success()
                ->send();
        }
    }

    public function confirmComplete(): void
    {
        $this->showCompleteConfirm = true;
    }

    public function cancelComplete(): void
    {
        $this->showCompleteConfirm = false;
    }

    public function completeTask(): void
    {
        if ($this->currentTask) {
            $this->currentTask->update(['status' => 'done']);
            
            Notification::make()
                ->title('Task completed! 🎉')
                ->success()
                ->send();

            // Remove from queue and move to next
            $this->taskQueue = $this->taskQueue->filter(fn($t) => $t->id !== $this->currentTask->id)->values();
            
            if ($this->taskQueue->count() > 0) {
                $newIndex = min($this->currentIndex, $this->taskQueue->count() - 1);
                $this->selectTask($newIndex);
            } else {
                $this->currentTask = null;
            }
            
            $this->showCompleteConfirm = false;
        }
    }

    public function addComment(): void
    {
        if ($this->currentTask && !empty(trim($this->newComment))) {
            $user = auth()->user();
            
            TaskComment::create([
                'task_id' => $this->currentTask->id,
                'employee_id' => $user->employee->id,
                'comment' => $this->newComment,
            ]);

            $this->currentTask->load('comments.author.user');
            $this->newComment = '';

            Notification::make()
                ->title('Comment added')
                ->success()
                ->send();
        }
    }

    public function skipTask(): void
    {
        // Move current task to end of queue
        if ($this->taskQueue->count() > 1) {
            $task = $this->taskQueue->pull($this->currentIndex);
            $this->taskQueue->push($task);
            $this->taskQueue = $this->taskQueue->values();
            $this->selectTask($this->currentIndex);
            
            Notification::make()
                ->title('Task skipped')
                ->body('Moved to end of queue')
                ->info()
                ->send();
        }
    }

    public function getProgress(): int
    {
        $total = $this->taskQueue->count();
        if ($total === 0) return 100;
        return round(($this->currentIndex / $total) * 100);
    }
}
