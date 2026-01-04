<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Only check if user is logged in and has is_active set to false explicitly
        if ($user && $user->is_active === false) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('filament.admin.auth.login')
                ->with('error', 'Your account has been deactivated.');
        }

        return $next($request);
    }
}
