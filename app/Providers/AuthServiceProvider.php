<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use App\Models\MaintenanceRequest;
use App\Policies\MaintenanceRequestPolicy;
use App\Models\Property;
use App\Policies\PropertyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        MaintenanceRequest::class => MaintenanceRequestPolicy::class,
        Property::class => PropertyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        
        // Register gates for role-based access
        Gate::define('super_manager', function ($user) {
            return $user->isSuperManager();
        });

        Gate::define('property_manager', function ($user) {
            return $user->isPropertyManager();
        });

        Gate::define('technician', function ($user) {
            return $user->isTechnician();
        });
    }
} 