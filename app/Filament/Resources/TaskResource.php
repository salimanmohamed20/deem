<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
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

class TaskResource extends Resource
{
    use HasRoleBasedAccess;

    protected static ?string $model = Task::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    // Enable global search
    protected static ?string $recordTitleAttribute = 'title';
    
    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->title;
    }
    
    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Project' => $record->project?->name ?? 'No Project',
            'Status' => ucfirst(str_replace('_', ' ', $record->status)),
            'Priority' => ucfirst($record->priority),
        ];
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'description', 'project.name'];
    }
    
    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()->forCurrentEmployee()->with(['project']);
    }
       public static function getNavigationBadge(): ?string
    {
        return (string) Task::forCurrentEmployee()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
    public static function getGlobalSearchResultUrl($record): string
    {
        return TaskResource::getUrl('edit', ['record' => $record]);
    }

    public static function form(Schema $schema): Schema
    {
        $user = auth()->user();
        $isEmployee = self::isEmployee($user) && !self::isProjectManager($user) && !self::isSuperAdmin($user);

        // Get accessible projects for the dropdown
        $projectOptions = Project::forCurrentEmployee()->pluck('name', 'id');

        return $schema
            ->components([
                Section::make('Task Details')
                    ->description('Basic task information')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Select::make('project_id')
                            ->label('Project')
                            ->options($projectOptions)
                            ->searchable()
                            ->required()
                            ->disabled($isEmployee)
                            ->placeholder('Select project')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->disabled($isEmployee)
                            ->placeholder('Enter task title')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description')
                            ->maxLength(65535)
                            ->disabled($isEmployee)
                            ->placeholder('Describe the task requirements and details')
                            ->columnSpanFull(),
                    ]),
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Status & Priority')
                            ->description('Track progress and importance')
                            ->icon('heroicon-o-signal')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'to_do' => 'To Do',
                                        'in_progress' => 'In Progress',
                                        'done' => 'Done',
                                    ])
                                    ->default('to_do')
                                    ->required(),
                                Forms\Components\Select::make('priority')
                                    ->options([
                                        'low' => 'Low',
                                        'medium' => 'Medium',
                                        'high' => 'High',
                                    ])
                                    ->default('medium')
                                    ->required()
                                    ->disabled($isEmployee),
                                Forms\Components\DatePicker::make('deadline')
                                    ->disabled($isEmployee)
                                    ->native(false)
                                    ->displayFormat('M d, Y'),
                            ]),
                        Section::make('Assignees')
                            ->description('Assign team members to this task')
                            ->icon('heroicon-o-users')
                            ->columnSpan(1)
                            ->visible(!$isEmployee)
                            ->schema([
                                Forms\Components\CheckboxList::make('assignees')
                                    ->relationship('assignees', 'id')
                                    ->options(Employee::with('user')->get()->pluck('user.name', 'id'))
                                    ->columns(2)
                                    ->gridDirection('row')
                                    ->searchable(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignees.user.name')
                    ->badge()
                    ->separator(','),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'to_do' => 'gray',
                        'in_progress' => 'info',
                        'done' => 'success',
                    }),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'warning',
                        'high' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('deadline')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project_id')
                    ->label('Project')
                    ->options(fn () => Project::forCurrentEmployee()->pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'to_do' => 'To Do',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ]),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->visible(fn () => self::isSuperAdmin() || self::isProjectManager()),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn () => self::isSuperAdmin() || self::isProjectManager()),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SubtasksRelationManager::class,
            RelationManagers\AttachmentsRelationManager::class,
            RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return self::isSuperAdmin() || self::isProjectManager();
    }
}
