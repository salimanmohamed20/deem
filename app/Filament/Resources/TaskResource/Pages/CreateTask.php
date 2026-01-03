<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Services\TaskNotificationService;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function afterCreate(): void
    {
        $task = $this->record;
        $assigneeIds = $task->assignees()->pluck('employees.id')->toArray();

        if (!empty($assigneeIds)) {
            $notificationService = new TaskNotificationService();
            $notificationService->notifyTaskAssigned($task, $assigneeIds);
        }
    }
}
