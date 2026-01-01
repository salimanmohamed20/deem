<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StandupResource\Pages;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Standup;
use App\Models\Task;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class StandupResource extends Resource
{
    protected static ?string $model = Standup::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('employee_id')
                    ->label('Employee')
                    ->options(Employee::with('user')->get()->pluck('user.name', 'id'))
                    ->searchable()
                    ->default(fn() => auth()->user()->employee?->id)
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->default(now())
                    ->required(),
                Forms\Components\Repeater::make('entries')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('project_id')
                            ->label('Project')
                            ->options(Project::pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive(),
                        Forms\Components\Select::make('task_id')
                            ->label('Task')
                            ->options(function (callable $get) {
                                $projectId = $get('project_id');
                                if (!$projectId) {
                                    return [];
                                }
                                return Task::where('project_id', $projectId)->pluck('title', 'id');
                            })
                            ->searchable(),
                        Forms\Components\Textarea::make('what_i_did')
                            ->label('What I Did')
                            ->required()
                            ->rows(3),
                        Forms\Components\Textarea::make('what_i_will_do')
                            ->label('What I Will Do')
                            ->required()
                            ->rows(3),
                        Forms\Components\Textarea::make('blockers')
                            ->label('Blockers')
                            ->rows(2),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.user.name')
                    ->label('Employee')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('entries_count')
                    ->counts('entries')
                    ->label('Entries'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label('Employee')
                    ->options(Employee::with('user')->get()->pluck('user.name', 'id')),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStandups::route('/'),
            'create' => Pages\CreateStandup::route('/create'),
            'edit' => Pages\EditStandup::route('/{record}/edit'),
        ];
    }
}
