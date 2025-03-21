<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow manager@example.com to bypass subscription check
        if ($request->user() && $request->user()->email === 'manager@example.com') {
            return $next($request);
        }
        
        if (!$request->user() || !$request->user()->hasActiveSubscription()) {
            return redirect()->route('subscription.plans')
                ->with('error', 'You need an active subscription to access this feature.');
        }

        return $next($request);
    }
} 