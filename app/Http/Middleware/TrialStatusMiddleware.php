<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class TrialStatusMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Admin users always have access
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if user can access the system
        if (!$user->canAccessSystem()) {
            // Account is locked, redirect to trial expired page
            return redirect()->route('trial.expired');
        }

        // If user is in grace period, redirect to subscription page
        if ($user->isInGracePeriod()) {
            // Don't redirect if already on subscription or payment pages
            if (!$request->is('subscription*') && !$request->is('payment*') && !$request->is('trial*') && !$request->is('m/subscription*')) {
                $redirectRoute = $request->is('m/*') ? 'mobile.subscription.plans' : 'subscription.plans';
                return redirect()->route($redirectRoute)->with('warning', 'Your free trial has ended. Subscribe now to keep your data.');
            }
        }

        return $next($request);
    }
}
