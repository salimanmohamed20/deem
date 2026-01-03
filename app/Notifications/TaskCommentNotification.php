<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\TaskComment;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskCommentNotification extends Notification
{
    use Queueable;

    protected Task $task;
    protected TaskComment $comment;
    protected string $commentBy;
    protected bool $isMention;

    public function __construct(Task $task, TaskComment $comment, string $commentBy, bool $isMention = false)
    {
        $this->task = $task;
        $this->comment = $comment;
        $this->commentBy = $commentBy;
        $this->isMention = $isMention;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $message = $this->isMention 
            ? "{$this->commentBy} mentioned you in a comment on: {$this->task->title}"
            : "{$this->commentBy} commented on task: {$this->task->title}";

        $title = $this->isMention ? 'You were mentioned' : 'New Comment';
        $icon = $this->isMention ? 'heroicon-o-at-symbol' : 'heroicon-o-chat-bubble-left';
        $color = $this->isMention ? 'warning' : 'info';

        return FilamentNotification::make()
            ->title($title)
            ->body($message)
            ->icon($icon)
            ->iconColor($color)
            ->getDatabaseMessage();
    }
}
