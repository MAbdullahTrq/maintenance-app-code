<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestRequestController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Guest maintenance request routes
Route::get('/request/{accessLink}', [GuestRequestController::class, 'showRequestForm'])->name('guest.request.form');
Route::post('/request/{accessLink}', [GuestRequestController::class, 'submitRequest'])->name('guest.request.submit');
Route::get('/request/{accessLink}/success', [GuestRequestController::class, 'showSuccessPage'])->name('guest.request.success');
Route::get('/request/{accessLink}/status/{requestId}', [GuestRequestController::class, 'showRequestStatus'])->name('guest.request.status');

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Test view route
    Route::get('/test', function () {
        return view('test');
    });

    // Debug route to check user role
    Route::get('/debug/user-role', function () {
        $user = auth()->user();
        return [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role' => $user->role ? [
                'id' => $user->role->id,
                'name' => $user->role->name,
                'slug' => $user->role->slug,
            ] : null,
            'is_super_manager' => $user->isSuperManager(),
            'is_property_manager' => $user->isPropertyManager(),
            'is_technician' => $user->isTechnician(),
        ];
    });

    // Route to update the current user to super manager role
    Route::get('/debug/make-super-manager', function () {
        $user = auth()->user();
        $superManagerRole = \App\Models\Role::where('slug', 'super_manager')->first();
        
        if ($superManagerRole) {
            $user->role_id = $superManagerRole->id;
            $user->save();
            
            return [
                'success' => true,
                'message' => 'User updated to Super Manager role',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_id' => $user->role_id,
                    'is_super_manager' => $user->isSuperManager(),
                ]
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Super Manager role not found',
            'available_roles' => \App\Models\Role::all(),
        ];
    });

    // Direct access routes for testing (bypassing middleware)
    Route::get('/direct/properties/create', [PropertyController::class, 'create'])->name('direct.properties.create');
    Route::post('/direct/properties', [PropertyController::class, 'store'])->name('direct.properties.store');
    Route::get('/direct/users/create', [UserController::class, 'create'])->name('direct.users.create');
    Route::post('/direct/users', [UserController::class, 'store'])->name('direct.users.store');
    
    // Debug route to check user permissions
    Route::get('/debug/check-permissions', function () {
        $user = auth()->user();
        $userPolicy = app(\App\Policies\UserPolicy::class);
        
        return [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role' => $user->role ? [
                'id' => $user->role->id,
                'name' => $user->role->name,
                'slug' => $user->role->slug,
            ] : null,
            'is_super_manager' => $user->isSuperManager(),
            'is_property_manager' => $user->isPropertyManager(),
            'is_technician' => $user->isTechnician(),
            'can_create_user' => $userPolicy->create($user),
            'middleware_groups' => [
                'super_manager' => $user->isSuperManager(),
                'property_manager' => $user->isPropertyManager(),
                'technician' => $user->isTechnician(),
            ]
        ];
    });

    // Profile routes
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/password', [UserController::class, 'showChangePasswordForm'])->name('password.change');
    Route::put('/profile/password', [UserController::class, 'changePassword'])->name('password.update');

    // Super manager routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
        
        // Give super managers access to all features without subscription requirement
        // Property routes
        Route::resource('properties', PropertyController::class);
        Route::get('/properties/{property}/qrcode', [PropertyController::class, 'downloadQrCode'])->name('properties.qrcode');
        
        // User management routes for super managers
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::put('/admin/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('admin.users.toggle-active');
        Route::post('/admin/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset-password');
    });

    // Property manager routes
    Route::middleware(['property_manager'])->group(function () {
        Route::get('/manager/dashboard', [DashboardController::class, 'managerDashboard'])->name('manager.dashboard');
        
        // Subscription routes
        Route::get('/subscription/plans', [SubscriptionController::class, 'index'])->name('subscription.plans');
        Route::get('/subscription/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
        Route::post('/subscription/paypal/create-order/{plan}', [SubscriptionController::class, 'createPayPalOrder'])->name('subscription.paypal.create');
        Route::get('/subscription/paypal/capture/{plan}', [SubscriptionController::class, 'capturePayPalOrder'])->name('subscription.capture');
        Route::get('/subscription/history', [SubscriptionController::class, 'history'])->name('subscription.history');
        Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
        
        // Property manager routes with active subscription
        Route::middleware(['subscription'])->group(function () {
            // Property routes
            Route::resource('properties', PropertyController::class)->except(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
            Route::get('/properties/{property}/qrcode', [PropertyController::class, 'downloadQrCode'])->name('properties.qrcode');
            
            // User management routes for property managers (technicians only)
            // Use except to avoid conflicts with super manager routes
            Route::resource('users', UserController::class);
            Route::put('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
            Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        });
    });

    // Technician routes
    Route::middleware(['technician'])->group(function () {
        Route::get('/technician/dashboard', [DashboardController::class, 'technicianDashboard'])->name('technician.dashboard');
    });

    // Maintenance request routes (accessible by all authenticated users)
    Route::resource('maintenance', MaintenanceRequestController::class);
    Route::post('/maintenance/{maintenance}/approve', [MaintenanceRequestController::class, 'approve'])->name('maintenance.approve');
    Route::post('/maintenance/{maintenance}/decline', [MaintenanceRequestController::class, 'decline'])->name('maintenance.decline');
    Route::post('/maintenance/{maintenance}/in-progress', [MaintenanceRequestController::class, 'inProgress'])->name('maintenance.in-progress');
    Route::post('/maintenance/{maintenance}/complete', [MaintenanceRequestController::class, 'complete'])->name('maintenance.complete');
    Route::post('/maintenance/{maintenance}/assign', [MaintenanceRequestController::class, 'assign'])->name('maintenance.assign');
    Route::post('/maintenance/{maintenance}/comment', [MaintenanceRequestController::class, 'addComment'])->name('maintenance.comment');
    Route::delete('/maintenance/image/{image}', [MaintenanceRequestController::class, 'deleteImage'])->name('maintenance.image.delete');
    Route::delete('/maintenance/comment/{comment}', [MaintenanceRequestController::class, 'deleteComment'])->name('maintenance.comment.delete');

    // Debug route to check all roles
    Route::get('/debug/roles', function () {
        return [
            'status' => 'success',
            'roles' => \App\Models\Role::all()
        ];
    });
    
    // Debug route to add subscription for manager@example.com
    Route::get('/debug/add-subscription-for-manager', function () {
        $user = \App\Models\User::where('email', 'manager@example.com')->first();

        if (!$user) {
            return [
                'status' => 'error',
                'message' => 'User with email manager@example.com not found!'
            ];
        }

        // Get the highest tier subscription plan
        $plan = \App\Models\SubscriptionPlan::orderBy('price', 'desc')->first();

        if (!$plan) {
            return [
                'status' => 'error',
                'message' => 'No subscription plans found!'
            ];
        }

        // Check if user already has an active subscription
        $existingSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();

        if ($existingSubscription) {
            return [
                'status' => 'info',
                'message' => 'User already has an active subscription until ' . $existingSubscription->ends_at->format('Y-m-d')
            ];
        }

        // Create a new subscription
        $subscription = \App\Models\Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addYear(),
            'payment_method' => 'manual',
            'status' => 'active',
        ]);

        return [
            'status' => 'success',
            'message' => 'Subscription added successfully!',
            'plan' => $plan->name,
            'valid_until' => $subscription->ends_at->format('Y-m-d')
        ];
    });
});
