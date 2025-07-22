<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Redirect based on user role
                if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                    return redirect()->route('admin.dashboard');
                } elseif ((method_exists($user, 'isPropertyManager') && $user->isPropertyManager()) || 
                         (method_exists($user, 'hasTeamMemberRole') && $user->hasTeamMemberRole())) {
                    // Check if property manager or team member has an active subscription
                    if (method_exists($user, 'hasActiveSubscription') && !$user->hasActiveSubscription()) {
                        return redirect()->route('subscription.plans');
                    }
                    return redirect()->route('mobile.manager.dashboard');
                } elseif (method_exists($user, 'isTechnician') && $user->isTechnician()) {
                    return redirect()->route('mobile.technician.dashboard');
                } else {
                    return redirect('/dashboard');
                }
            }
        }

        return $next($request);
    }
} 