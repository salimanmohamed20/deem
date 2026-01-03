<?php

namespace App\Filament\Resources\StandupResource\Pages;

use App\Filament\Resources\StandupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListStandups extends ListRecords
{
    protected static string $resource = StandupResource::class;

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
}
