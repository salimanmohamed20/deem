<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentionNotification extends Notification
{
    use Queueable;

    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'mention',
            'task_id' => $this->data['task_id'] ?? null,
            'comment_id' => $this->data['comment_id'] ?? null,
            'mentioned_by' => $this->data['mentioned_by'] ?? null,
            'message' => $this->data['message'] ?? 'You were mentioned in a comment',
        ];
    }
}
