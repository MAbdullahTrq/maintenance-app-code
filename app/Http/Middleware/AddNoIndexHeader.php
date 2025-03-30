<?php

namespace App\Http\Middleware;

use Closure;

class AddNoIndexHeader
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        $response->header('X-Robots-Tag', 'noindex, nofollow');
        
        return $response;
    }
} 