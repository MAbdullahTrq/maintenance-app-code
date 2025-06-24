<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && !$user->is_active) {
            Auth::logout();
            
            // Check if it's a newly registered user who hasn't verified email
            if ($user->verification_token && $user->verification_token_expires_at && $user->verification_token_expires_at->isFuture()) {
                return redirect()->route('verification.notice')
                    ->with('error', 'Please verify your email address to access this feature.')
                    ->with('email', $user->email);
            } else {
                return redirect()->route('login')
                    ->with('error', 'Your account has been deactivated. Please contact support.');
            }
        }

        return $next($request);
    }
} 