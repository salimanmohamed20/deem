<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Employee;
use App\Models\Project;
use App\Traits\HasRoleBasedAccess;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use BackedEnum;

class ProjectResource extends Resource
{
    use HasRoleBasedAccess;

    protected static ?string $model = Project::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Project Details')
                    ->description('Basic project information')
                    ->icon('heroicon-o-folder')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter project name')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->rows(3)
                            ->placeholder('Describe the project goals and scope')
                            ->columnSpanFull(),
                    ]),
                Section::make('Project Settings')
                    ->description('Manager, status, and timeline')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('project_manager_id')
                                    ->label('Project Manager')
                                    ->options(Employee::with('user')->get()->pluck('user.name', 'id'))
                                    ->searchable()
                                    ->placeholder('Select manager'),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'planned' => 'Planned',
                                        'active' => 'Active',
                                        'completed' => 'Completed',
                                        'archived' => 'Archived',
                                    ])
                                    ->default('planned')
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('M d, Y'),
                                Forms\Components\DatePicker::make('end_date')
                                    ->native(false)
                                    ->displayFormat('M d, Y')
                                    ->afterOrEqual('start_date'),
                            ]),
                    ]),
                Section::make('Team Assignment')
                    ->description('Assign teams to this project')
                    ->icon('heroicon-o-user-group')
                    ->collapsible()
                    ->schema([
                        Forms\Components\CheckboxList::make('teams')
                            ->relationship('teams', 'name')
                            ->columns(3)
                            ->gridDirection('row')
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('manager.user.name')
                    ->label('Project Manager'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'planned' => 'gray',
                        'active' => 'info',
                        'completed' => 'success',
                        'archived' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('teams.name')
                    ->badge()
                    ->separator(','),
                Tables\Columns\TextColumn::make('tasks_count')
                    ->counts('tasks')
                    ->label('Tasks'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'planned' => 'Planned',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'archived' => 'Archived',
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make()
                    ->visible(fn () => self::isSuperAdmin()),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn () => self::isSuperAdmin()),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TasksRelationManager::class,
            RelationManagers\TeamsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return self::isSuperAdmin();
    }
}
