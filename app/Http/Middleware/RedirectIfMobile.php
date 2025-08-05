<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Jenssegers\Agent\Agent;

class RedirectIfMobile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $agent = new Agent();

        // If user is on mobile and not already on a mobile route
        if ($agent->isMobile() && !$request->is('mobile*') && !$request->is('m/*')) {
            return redirect('/mobile');
        }

        // If user is on desktop and tries to access mobile route, redirect to home
        if (!$agent->isMobile() && ($request->is('mobile*') || $request->is('m/*'))) {
            return redirect('/');
        }

        return $next($request);
    }
}
