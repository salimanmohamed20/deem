<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Project;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): ?Builder
    {
        return parent::getTableQuery()->forCurrentEmployee();
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->icon('heroicon-m-list-bullet'),

            'planned' => Tab::make('Planned')
                ->icon('heroicon-m-calendar')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', 'planned')
                )
                ->badge(Project::where('status', 'planned')->count())
                ->badgeColor('warning'),

            'active' => Tab::make('Active')
                ->icon('heroicon-m-play-circle')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', 'active')
                )
                ->badge(Project::where('status', 'active')->count())
                ->badgeColor('success'),

            'completed' => Tab::make('Completed')
                ->icon('heroicon-m-check-circle')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', 'completed')
                )
                ->badge(Project::where('status', 'completed')->count())
                ->badgeColor('info'),

            'archived' => Tab::make('Archived')
                ->icon('heroicon-m-archive-box')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', 'archived')
                )
                ->badge(Project::where('status', 'archived')->count())
                ->badgeColor('gray'),
        ];
    }
}
