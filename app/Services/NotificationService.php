<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationService
{
    public function notifyMentionedEmployees(array $employeeIds, array $data): void
    {
        $users = User::whereHas('employee', function ($q) use ($employeeIds) {
            $q->whereIn('employees.id', $employeeIds);
        })->get();

        foreach ($users as $user) {
            $user->notify(new \App\Notifications\MentionNotification($data));
        }
    }

    public function markAsRead(string $notificationId): bool
    {
        $notification = DatabaseNotification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        return false;
    }

    public function getUserNotifications(User $user, int $limit = 10): Collection
    {
        return $user->notifications()
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }
}
