<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
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
        
        // Debug information
        if (config('app.debug')) {
            \Log::debug('Admin Middleware Check', [
                'user_id' => $user->id,
                'role' => $user->role ? $user->role->slug : 'no role',
                'is_admin' => $user->role && $user->role->slug === 'admin',
                'hasRole_admin' => $user->hasRole('admin'),
            ]);
        }

        // Check if user has admin role
        if ($user->role && $user->role->slug === 'admin') {
            return $next($request);
        }

        // If not an admin, redirect with error message
        return redirect()->route('dashboard')->with('error', 'You do not have permission to access this area.');
    }
}
