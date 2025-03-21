<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SuperManagerMiddleware
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
        if (!Auth::check()) {
            Log::warning('SuperManagerMiddleware: User not authenticated');
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Log detailed information for debugging
        Log::info('SuperManagerMiddleware check', [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role_name' => $user->role ? $user->role->name : 'No role',
            'role_slug' => $user->role ? $user->role->slug : 'No role',
            'is_super_manager' => $user->role && $user->role->slug === 'super_manager',
            'hasRole_super_manager' => $user->hasRole('super_manager'),
            'isSuperManager()' => $user->isSuperManager(),
            'path' => $request->path(),
            'route' => $request->route()->getName(),
            'middleware' => $request->route()->middleware(),
        ]);

        // Check if user has super_manager role
        if ($user->role && $user->role->slug === 'super_manager') {
            Log::info('SuperManagerMiddleware: Access granted to ' . $user->email);
            return $next($request);
        }

        // If not a super manager, redirect with error message
        Log::warning('SuperManagerMiddleware: Access denied for ' . $user->email . ' with role ' . ($user->role ? $user->role->slug : 'none'));
        return redirect('/')->with('error', 'You do not have permission to access this area.');
    }
}
