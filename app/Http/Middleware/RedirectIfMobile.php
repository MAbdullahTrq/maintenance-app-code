<?php

namespace App\Http\Middleware;

use Closure;
use Jenssegers\Agent\Agent;

class RedirectIfMobile
{
    public function handle($request, Closure $next)
    {
        $agent = new Agent();

        // Allow force desktop mode
        if (session('force_desktop')) {
            return $next($request);
        }

        // Only redirect if not already on a mobile route
        if ($agent->isMobile() && !preg_match('#^/(mobile|m|t)#', $request->path())) {
            return redirect()->route('mobile.home');
        }

        return $next($request);
    }
} 