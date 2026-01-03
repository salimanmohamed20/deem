<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use App\Models\Employee;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class SubtasksRelationManager extends RelationManager
{
    protected static string $relationship = 'subtasks';
    protected static ?string $title = 'Checklist';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('title')
                    ->label('Item')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('What needs to be done?')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label('Notes')
                    ->placeholder('Additional details (optional)')
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\Select::make('assigned_to')
                    ->label('Assign to')
                    ->options(Employee::with('user')->get()->pluck('user.name', 'id'))
                    ->searchable()
                    ->placeholder('Unassigned'),
                Forms\Components\DatePicker::make('due_date')
                    ->label('Due date')
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->paginated(false)
            ->columns([
                Tables\Columns\CheckboxColumn::make('is_completed')
                    ->label('')
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state) {
                            $record->completed_by = auth()->user()?->employee?->id;
                            $record->completed_at = now();
                            $record->save();
                        } else {
                            $record->completed_by = null;
                            $record->completed_at = null;
                            $record->save();
                        }
                    }),
                Tables\Columns\TextColumn::make('title')
                    ->label('Item')
                    ->wrap()
                    ->description(fn ($record) => $record->description)
                    ->color(fn ($record) => $record->is_completed ? 'gray' : null)
                    ->extraAttributes(fn ($record) => $record->is_completed ? ['class' => 'line-through'] : []),
                Tables\Columns\TextColumn::make('assignedEmployee.user.name')
                    ->label('Assigned')
                    ->badge()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due')
                    ->date('M j')
                    ->placeholder('—'),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Add Item')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Add Checklist Item'),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->iconButton()
                    ->modalHeading('Edit Item'),
                Actions\DeleteAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No checklist items')
            ->emptyStateDescription('Add items to track progress.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }
}
