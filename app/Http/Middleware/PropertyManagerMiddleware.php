<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class PropertyManagerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            Log::debug('PropertyManagerMiddleware: No user found');
            abort(403, 'Unauthorized action.');
        }

        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        $isPropertyManager = $user->hasRole('property_manager');
        $isTeamMember = $user->hasTeamMemberRole();
        
        Log::debug('PropertyManagerMiddleware check', [
            'user_id' => $user->id,
            'role_id' => $user->role_id,
            'role_slug' => $user->role->slug ?? null,
            'is_property_manager' => $isPropertyManager,
            'is_team_member' => $isTeamMember
        ]);

        if (!$isPropertyManager && !$isTeamMember) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
