<?php

namespace App\Providers;

use App\Http\Middleware\SuperManagerMiddleware;
use App\Http\Middleware\PropertyManagerMiddleware;
use App\Http\Middleware\TechnicianMiddleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckSubscription;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class RoleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // No registrations needed
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $router = $this->app->make(Router::class);
        
        // Register middleware aliases
        $router->aliasMiddleware('role', CheckRole::class);
        $router->aliasMiddleware('super_manager', SuperManagerMiddleware::class);
        $router->aliasMiddleware('property_manager', PropertyManagerMiddleware::class);
        $router->aliasMiddleware('technician', TechnicianMiddleware::class);
        $router->aliasMiddleware('subscription', CheckSubscription::class);
    }
}
