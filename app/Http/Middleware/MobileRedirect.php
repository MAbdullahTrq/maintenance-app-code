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
        // Generate a log file for debugging purposes
        $debugLog = storage_path('logs/mobile_debug.log');
        file_put_contents($debugLog, "Request path: " . $request->path() . "\n", FILE_APPEND);
        file_put_contents($debugLog, "User Agent: " . $request->header('User-Agent') . "\n", FILE_APPEND);
        
        $agent = new Agent();
        
        // Log agent detection results
        $agentIsMobile = $agent->isMobile();
        $agentIsTablet = $agent->isTablet();
        file_put_contents($debugLog, "Agent detects mobile: " . ($agentIsMobile ? 'true' : 'false') . "\n", FILE_APPEND);
        file_put_contents($debugLog, "Agent detects tablet: " . ($agentIsTablet ? 'true' : 'false') . "\n", FILE_APPEND);
        
        // Skip redirect if already on mobile routes
        if (str_starts_with($request->path(), 'm/')) {
            file_put_contents($debugLog, "Already on mobile route, skipping\n\n", FILE_APPEND);
            return $next($request);
        }
        
        // Skip redirect for certain paths
        $excludedPaths = [
            'api/', 'password', 'logout', 
            'email', 'csrf-cookie', 'sanctum', 'livewire'
        ];
        
        foreach ($excludedPaths as $path) {
            if (str_starts_with($request->path(), $path)) {
                file_put_contents($debugLog, "Excluded path: {$path}, skipping\n\n", FILE_APPEND);
                return $next($request);
            }
        }
        
        // Enhanced mobile detection to work with Chrome's device emulation
        $isMobile = $agent->isMobile() || $agent->isTablet();
        
        // Check for common mobile user agent strings
        $userAgent = strtolower($request->header('User-Agent'));
        
        // More aggressive mobile detection
        $mobileKeywords = [
            'mobile', 'android', 'iphone', 'ipad', 'ipod', 
            'blackberry', 'windows phone', 'opera mini', 
            'opera mobi', 'palm', 'webos', 'nokia', 'samsung',
            'chrome mobile', 'safari mobile', 'firefox mobile',
            'mobi', 'tablet', 'touch', 'phone', 'wap', 'pda'
        ];
        
        foreach ($mobileKeywords as $keyword) {
            if (strpos($userAgent, $keyword) !== false) {
                $isMobile = true;
                file_put_contents($debugLog, "Detected mobile keyword: {$keyword}\n", FILE_APPEND);
                break;
            }
        }
        
        // Use screen width as a fallback detection method
        if (!$isMobile && isset($_COOKIE['screen_width']) && $_COOKIE['screen_width'] <= 1024) {
            $isMobile = true;
            file_put_contents($debugLog, "Detected mobile from screen width: {$_COOKIE['screen_width']}\n", FILE_APPEND);
        }
        
        // Add a debug query parameter to force mobile view for testing
        if ($request->query('view') === 'mobile') {
            $isMobile = true;
            file_put_contents($debugLog, "Forced mobile view via query parameter\n", FILE_APPEND);
        }
        
        // Log final detection result
        file_put_contents($debugLog, "Final mobile detection: " . ($isMobile ? 'true' : 'false') . "\n", FILE_APPEND);
        
        // Redirect mobile devices to mobile version
        if ($isMobile) {
            $path = $request->path();
            file_put_contents($debugLog, "Attempting to redirect mobile path: {$path}\n", FILE_APPEND);
            
            // Special handling for home page (/)
            if ($path === '/' || $path === '') {
                file_put_contents($debugLog, "Redirecting to /m\n\n", FILE_APPEND);
                // Redirect to mobile welcome page
                return redirect('m');
            }
            
            // Special handling for auth pages
            if ($path === 'login') {
                file_put_contents($debugLog, "Redirecting to /m/login\n\n", FILE_APPEND);
                return redirect('m/login');
            }
            
            if ($path === 'register') {
                file_put_contents($debugLog, "Redirecting to /m/register\n\n", FILE_APPEND);
                return redirect('m/register');
            }
            
            // Map certain paths to their mobile equivalents
            $pathMap = [
                'dashboard' => 'm/dash',
                'properties' => 'm/ap',
                'technicians' => 'm/at',
                'maintenance' => 'm/ar',
            ];
            
            foreach ($pathMap as $webPath => $mobilePath) {
                if (str_starts_with($path, $webPath)) {
                    file_put_contents($debugLog, "Redirecting from {$path} to {$mobilePath}\n\n", FILE_APPEND);
                    return redirect($mobilePath);
                }
            }
            
            // Default mobile redirect
            file_put_contents($debugLog, "Default redirect to m/dash\n\n", FILE_APPEND);
            return redirect('m/dash');
        }
        
        file_put_contents($debugLog, "Not a mobile device, continuing\n\n", FILE_APPEND);
        return $next($request);
    }
} 