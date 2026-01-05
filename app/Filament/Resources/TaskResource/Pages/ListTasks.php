<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Tabs\Tab;
use App\Models\Task;
use Filament\Support\Enums\IconPosition;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
public function getTabs(): array
{
    return [
        'all' => Tab::make('All')
            ->icon('heroicon-m-list-bullet'),

        'done' => Tab::make('Done')
            ->icon('heroicon-m-check-circle')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('status', 'done')
            )
            ->badge(
                Task::where('status', 'done')->count()
            )
            ->badgeColor('success'),

        'inProgress' => Tab::make('In Progress')
            ->icon('heroicon-m-arrow-path')
            ->iconPosition(IconPosition::Before)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('status', 'in_progress')
            )
            ->badge(
                Task::where('status', 'in_progress')->count()
            )
            ->badgeColor('info'),

        'toDo' => Tab::make('To Do')
            ->icon('heroicon-m-clipboard-document-list')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('status', 'to_do')
            )
            ->badge(
                Task::where('status', 'to_do')->count()
            )
            ->badgeColor('gray'),
    ];
}
    protected function getTableQuery(): ?Builder
    {
        return parent::getTableQuery()->forCurrentEmployee();
    }
}
