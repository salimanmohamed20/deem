<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();
        
        // Check if user exists and is active before attempting login
        $user = User::where('email', $data['email'])->first();
        
        if ($user && !$user->is_active) {
            Notification::make()
                ->title('Account Deactivated')
                ->body('Your account has been deactivated. Please contact administrator.')
                ->danger()
                ->persistent()
                ->send();
            
            throw ValidationException::withMessages([
                'data.email' => 'Your account has been deactivated.',
            ]);
        }
        
        return parent::authenticate();
    }
}
