<?php

namespace App\Notifications;

use App\Models\Task;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    protected Task $task;
    protected string $assignedBy;

    public function __construct(Task $task, string $assignedBy)
    {
        $this->task = $task;
        $this->assignedBy = $assignedBy;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $message = "You have been assigned to task: {$this->task->title}";

        return FilamentNotification::make()
            ->title('New Task Assigned')
            ->body($message)
            ->icon('heroicon-o-clipboard-document-list')
            ->iconColor('info')
            ->getDatabaseMessage();
    }
}
