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
        $jobTitle = $user->employee?->jobTitle?->name ?? 'Not assigned';

        return $schema
            ->components([
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
               Forms\Components\TextInput::make('job_title')
    ->label('Job Title')
    ->formatStateUsing(fn () => $jobTitle ?? 'Not assigned')
    ->disabled()
    ->dehydrated(false),

   

                $this->getPasswordFormComponent()
                    ->columnSpanFull(),
                    
                $this->getPasswordConfirmationFormComponent()
                    ->columnSpanFull(),
            ]);
    }
}
