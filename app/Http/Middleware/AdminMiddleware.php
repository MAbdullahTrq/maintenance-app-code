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

        // Detailed debug information
        Log::debug('Admin Middleware Check - Full Details', [
            'user_id' => $user->id ?? 'no user id',
            'user_email' => $user->email ?? 'no email',
            'role_id' => $user->role_id ?? 'no role id',
            'role' => $user->role ? [
                'id' => $user->role->id,
                'name' => $user->role->name,
                'slug' => $user->role->slug,
            ] : 'no role',
            'is_admin' => $user->isAdmin(),
            'hasRole_admin' => $user->hasRole('admin'),
            'request_path' => $request->path(),
            'intended_url' => $request->session()->get('url.intended'),
        ]);
        
        // Check if user has admin role
        if ($user->isAdmin()) {
            Log::debug('Admin access granted', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            return $next($request);
        }

        // Get the user's role slug safely
        $roleSlug = $user->role ? $user->role->slug : null;

        // Log the redirect
        Log::debug('Admin access denied - redirecting', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role_slug' => $roleSlug,
            'redirect_to' => $roleSlug ? route($roleSlug === 'property_manager' ? 'manager.dashboard' : 'technician.dashboard') : route('login')
        ]);

        // Determine the appropriate dashboard route based on user's role
        $dashboardRoute = match($roleSlug) {
            'property_manager' => 'manager.dashboard',
            'technician' => 'technician.dashboard',
            default => 'login'
        };

        // If not an admin, redirect with error message
        return redirect()->route($dashboardRoute)->with('error', 'You do not have permission to access this area.');
    }
}
