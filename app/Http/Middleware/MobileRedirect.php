<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class MobileRedirect
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
        $agent = new Agent();
        
        // Skip redirect if already on mobile routes
        if (str_starts_with($request->path(), 'm/')) {
            return $next($request);
        }
        
        // Skip redirect for certain paths
        $excludedPaths = [
            'api/', 'login', 'register', 'password', 'logout', 
            'email', 'csrf-cookie', 'sanctum', 'livewire'
        ];
        
        foreach ($excludedPaths as $path) {
            if (str_starts_with($request->path(), $path)) {
                return $next($request);
            }
        }
        
        // Enhanced mobile detection to work with Chrome's device emulation
        $isMobile = $agent->isMobile() || $agent->isTablet();
        
        // Check for common mobile user agent strings
        $userAgent = strtolower($request->header('User-Agent'));
        if (!$isMobile && (
            strpos($userAgent, 'mobile') !== false ||
            strpos($userAgent, 'android') !== false ||
            strpos($userAgent, 'iphone') !== false ||
            strpos($userAgent, 'ipad') !== false ||
            // Chrome emulation often includes these
            strpos($userAgent, 'chrome mobile') !== false
        )) {
            $isMobile = true;
        }
        
        // Add a debug query parameter to force mobile view for testing
        if ($request->query('view') === 'mobile') {
            $isMobile = true;
        }
        
        // Redirect mobile devices to mobile version
        if ($isMobile) {
            $path = $request->path();
            
            // Map certain paths to their mobile equivalents
            $pathMap = [
                'dashboard' => 'm/dash',
                'properties' => 'm/ap',
                'technicians' => 'm/at',
                'maintenance' => 'm/ar',
            ];
            
            foreach ($pathMap as $webPath => $mobilePath) {
                if (str_starts_with($path, $webPath)) {
                    return redirect($mobilePath);
                }
            }
            
            // Default mobile redirect
            return redirect('m/dash');
        }
        
        return $next($request);
    }
} 