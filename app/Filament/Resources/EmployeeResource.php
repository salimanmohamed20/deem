<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use App\Models\JobTitle;
use App\Models\User;
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

class EmployeeResource extends Resource
{
    use HasRoleBasedAccess;

    protected static ?string $model = Employee::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationGroup(): ?string
    {
        return 'Administration';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::isSuperAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Employee Details')
                    ->description('Link user account and assign job role')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('User Account')
                                    ->options(User::whereDoesntHave('employee')->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->hiddenOn('edit')
                                    ->placeholder('Select a user'),
                                Forms\Components\Select::make('job_title_id')
                                    ->label('Job Title')
                                    ->options(JobTitle::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->placeholder('Select job title'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255)
                                    ->placeholder('+1 (555) 000-0000'),
                                Forms\Components\DatePicker::make('hire_date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('M d, Y'),
                            ]),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active Employee')
                            ->helperText('Inactive employees will not appear in assignments')
                            ->default(true),
                    ]),
                Section::make('Team Assignments')
                    ->description('Assign employee to teams')
                    ->icon('heroicon-o-user-group')
                    ->collapsible()
                    ->schema([
                        Forms\Components\CheckboxList::make('teams')
                            ->relationship('teams', 'name')
                            ->columns(3)
                            ->gridDirection('row')
                            ->noSearchResultsMessage('No teams found')
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jobTitle.name')
                    ->label('Job Title')
                    ->sortable(),
                Tables\Columns\TextColumn::make('teams.name')
                    ->badge()
                    ->separator(','),
                Tables\Columns\TextColumn::make('hire_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('job_title_id')
                    ->label('Job Title')
                    ->options(JobTitle::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('teams')
                    ->relationship('teams', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
