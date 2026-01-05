<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        $user = auth()->user();
        $jobTitle = $user->employee?->jobTitle?->name;

        return $schema
            ->components([
                Forms\Components\FileUpload::make('avatar')
                    ->label('Avatar')
                    ->image()
                    ->disk('public')
                    ->directory('avatars')
                    ->imageEditor()
                    ->maxSize(2048)
                    ->helperText('Upload a profile picture (max 2MB)'),
                
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                
                Forms\Components\Placeholder::make('job_title_display')
                    ->label('Job Title')
                    ->content($jobTitle ?? 'Not assigned')
                    ->visible((bool) $jobTitle),
                
                $this->getPasswordFormComponent()
                    ->columnSpanFull(),
                    
                $this->getPasswordConfirmationFormComponent()
                    ->columnSpanFull(),
            ]);
    }
}
