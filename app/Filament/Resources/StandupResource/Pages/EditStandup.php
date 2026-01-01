<?php

namespace App\Filament\Resources\StandupResource\Pages;

use App\Filament\Resources\StandupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStandup extends EditRecord
{
    protected static string $resource = StandupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
