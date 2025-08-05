<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\MaintenanceRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View composer for mobile layout stats
        View::composer('mobile.layout', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                
                // For team members, get the workspace owner's data
                $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
                
                // Calculate stats
                $ownersCount = $workspaceOwner->managedOwners()->count();
                $propertiesCount = $workspaceOwner->managedProperties()->count();
                $techniciansCount = $workspaceOwner->technicians()->count();
                $requestsCount = MaintenanceRequest::whereIn('property_id', $workspaceOwner->managedProperties()->pluck('id'))->count();
                
                // Pass stats to the view
                $view->with([
                    'ownersCount' => $ownersCount,
                    'propertiesCount' => $propertiesCount,
                    'techniciansCount' => $techniciansCount,
                    'requestsCount' => $requestsCount,
                ]);
            }
        });
    }
}
