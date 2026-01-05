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
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use BackedEnum;

class StandupResource extends Resource
{
    protected static ?string $model = Standup::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Standup Information')
                    ->description('Daily standup details')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('employee_id')
                                    ->label('Employee')
                                    ->options(Employee::with('user')->get()->pluck('user.name', 'id'))
                                    ->searchable()
                                    ->default(fn() => auth()->user()->employee?->id)
                                    ->required(),
                                Forms\Components\DatePicker::make('date')
                                    ->default(now())
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('M d, Y'),
                            ]),
                    ]),
                Section::make('Standup Entries')
                    ->description('Add your updates for each project/task')
                    ->icon('heroicon-o-list-bullet')
                    ->schema([
                        Forms\Components\Repeater::make('entries')
                            ->relationship()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('project_id')
                                            ->label('Project')
                                            ->options(Project::pluck('name', 'id'))
                                            ->searchable()
                                            ->required()
                                            ->reactive()
                                            ->placeholder('Select project'),
                                        Forms\Components\Select::make('task_id')
                                            ->label('Task (Optional)')
                                            ->options(function (callable $get) {
                                                $projectId = $get('project_id');
                                                if (!$projectId) {
                                                    return [];
                                                }
                                                return Task::where('project_id', $projectId)->pluck('title', 'id');
                                            })
                                            ->searchable()
                                            ->placeholder('Select task'),
                                    ]),
                                Forms\Components\Textarea::make('what_i_will_do')
                                    ->label('What I Will Do Today')
                                    ->required()
                                    ->rows(2)
                                    ->placeholder('Describe your plans for today'),
                                Forms\Components\Textarea::make('blockers')
                                    ->label('Blockers / Impediments')
                                    ->rows(2)
                                    ->placeholder('Any issues blocking your progress?'),
                            ])
                            ->columns(1)
                            ->columnSpanFull()
                            ->defaultItems(1)
                            ->addActionLabel('Add Another Entry')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['project_id'] ? Project::find($state['project_id'])?->name : 'New Entry'),
                    ]),
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
        return [];
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
