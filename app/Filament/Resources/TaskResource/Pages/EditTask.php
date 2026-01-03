<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Services\TaskNotificationService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected array $previousAssigneeIds = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeFill(): void
    {
        // Store current assignees before edit
        $this->previousAssigneeIds = $this->record->assignees()->pluck('employees.id')->toArray();
    }

    protected function afterSave(): void
    {
        $task = $this->record;
        $currentAssigneeIds = $task->assignees()->pluck('employees.id')->toArray();

        // Find newly added assignees
        $newAssigneeIds = array_diff($currentAssigneeIds, $this->previousAssigneeIds);

        if (!empty($newAssigneeIds)) {
            $notificationService = new TaskNotificationService();
            $notificationService->notifyTaskAssigned($task, $newAssigneeIds);
        }
    }
}
