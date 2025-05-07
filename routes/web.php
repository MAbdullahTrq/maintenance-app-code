<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestRequestController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TechnicianController;
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
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard route that redirects to the appropriate dashboard based on user role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isPropertyManager()) {
            // Check if property manager has an active subscription
            if (!$user->hasActiveSubscription()) {
                return redirect()->route('subscription.plans');
            }
            return redirect()->route('manager.dashboard');
        } else {
            return redirect()->route('technician.dashboard');
        }
    })->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/password', [UserController::class, 'showChangePasswordForm'])->name('password.change');
    Route::put('/profile/password', [UserController::class, 'changePassword'])->name('password.update');

    // Admin routes
    Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        
        // User management routes
        Route::resource('users', UserController::class);
        Route::put('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        
        // Subscription management routes
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscription.index');
        Route::get('/subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans.index');
        Route::get('/subscription/plans/create', [SubscriptionController::class, 'create'])->name('subscription.plans.create');
        Route::post('/subscription/plans', [SubscriptionController::class, 'store'])->name('subscription.plans.store');
        Route::get('/subscription/plans/{plan}/edit', [SubscriptionController::class, 'edit'])->name('subscription.plans.edit');
        Route::put('/subscription/plans/{plan}', [SubscriptionController::class, 'update'])->name('subscription.plans.update');
        Route::delete('/subscription/plans/{plan}', [SubscriptionController::class, 'destroy'])->name('subscription.plans.destroy');
        Route::get('/users/{user}/grant-subscription', [SubscriptionController::class, 'showGrantForm'])->name('users.grant-subscription.create');
        Route::post('/users/{user}/grant-subscription', [SubscriptionController::class, 'grantSubscription'])->name('users.grant-subscription.store');
        
        // Property routes
        Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
        Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
        Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
        Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
        Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
        Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
        Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
        Route::get('/properties/{property}/qrcode', [PropertyController::class, 'downloadQrCode'])->name('properties.qrcode');
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
        
        // Technician management routes
        Route::get('/technicians', [TechnicianController::class, 'index'])->name('technicians.index');
        Route::get('/technicians/create', [TechnicianController::class, 'create'])->name('technicians.create');
        Route::post('/technicians', [TechnicianController::class, 'store'])->name('technicians.store');
        Route::get('/technicians/{user}/edit', [TechnicianController::class, 'edit'])->name('technicians.edit');
        Route::put('/technicians/{user}', [TechnicianController::class, 'update'])->name('technicians.update');
        Route::delete('/technicians/{user}', [TechnicianController::class, 'destroy'])->name('technicians.destroy');
        Route::put('/technicians/{user}/toggle-active', [TechnicianController::class, 'toggleActive'])->name('technicians.toggle-active');
        Route::post('/technicians/{user}/reset-password', [TechnicianController::class, 'resetPassword'])->name('technicians.reset-password');
        
        // Property manager routes with active subscription
        Route::middleware(['subscription'])->group(function () {
            // Property routes for managers
            Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
            Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
            Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
            Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
            Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
            Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
            Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
            Route::get('/properties/{property}/qrcode', [PropertyController::class, 'downloadQrCode'])->name('properties.qrcode');
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
    Route::post('/maintenance/{maintenance}/assign', [MaintenanceRequestController::class, 'assign'])->name('maintenance.assign');
    Route::post('/maintenance/{maintenance}/accept', [MaintenanceRequestController::class, 'accept'])->name('maintenance.accept');
    Route::post('/maintenance/{maintenance}/reject', [MaintenanceRequestController::class, 'reject'])->name('maintenance.reject');
    Route::post('/maintenance/{maintenance}/start-task', [MaintenanceRequestController::class, 'startTask'])->name('maintenance.start-task');
    Route::post('/maintenance/{maintenance}/finish-task', [MaintenanceRequestController::class, 'finishTask'])->name('maintenance.finish-task');
    Route::post('/maintenance/{maintenance}/complete', [MaintenanceRequestController::class, 'complete'])->name('maintenance.complete');
    Route::post('/maintenance/{maintenance}/comment', [MaintenanceRequestController::class, 'addComment'])->name('maintenance.comment');
    Route::post('/maintenance/{maintenance}/close', [MaintenanceRequestController::class, 'close'])->name('maintenance.close');
    Route::delete('/maintenance/image/{image}', [MaintenanceRequestController::class, 'deleteImage'])->name('maintenance.image.delete');
    Route::delete('/maintenance/comment/{comment}', [MaintenanceRequestController::class, 'deleteComment'])->name('maintenance.comment.delete');

    // Debug routes
    if (config('app.debug')) {
        Route::get('/debug/roles', function () {
            return ['status' => 'success', 'roles' => \App\Models\Role::all()];
        });
    }
});

// Mobile routes
Route::prefix('m')->middleware(['auth'])->group(function () {
    // Manager mobile routes
    Route::middleware(['property_manager'])->group(function () {
        // Dashboard
        Route::get('/dash', [App\Http\Controllers\Mobile\ManagerController::class, 'dashboard'])->name('mobile.manager.dashboard');
        
        // Properties
        Route::get('/ap', [App\Http\Controllers\Mobile\PropertyController::class, 'index'])->name('mobile.properties.index');
        Route::get('/p/{property}', [App\Http\Controllers\Mobile\PropertyController::class, 'show'])->name('mobile.properties.show');
        Route::get('/ep/{property}', [App\Http\Controllers\Mobile\PropertyController::class, 'edit'])->name('mobile.properties.edit');
        Route::post('/ep/{property}', [App\Http\Controllers\Mobile\PropertyController::class, 'update'])->name('mobile.properties.update');
        
        // Technicians
        Route::get('/at', [App\Http\Controllers\Mobile\TechnicianController::class, 'index'])->name('mobile.technicians.index');
        Route::get('/t/{user}', [App\Http\Controllers\Mobile\TechnicianController::class, 'show'])->name('mobile.technicians.show');
        Route::get('/et/{user}', [App\Http\Controllers\Mobile\TechnicianController::class, 'edit'])->name('mobile.technicians.edit');
        Route::post('/et/{user}', [App\Http\Controllers\Mobile\TechnicianController::class, 'update'])->name('mobile.technicians.update');
        
        // Maintenance Requests
        Route::get('/ar', [App\Http\Controllers\Mobile\MaintenanceController::class, 'index'])->name('mobile.maintenance.index');
        Route::get('/r/pending', [App\Http\Controllers\Mobile\MaintenanceController::class, 'pending'])->name('mobile.maintenance.pending');
        Route::get('/r/assigned', [App\Http\Controllers\Mobile\MaintenanceController::class, 'assigned'])->name('mobile.maintenance.assigned');
        Route::get('/r/completed', [App\Http\Controllers\Mobile\MaintenanceController::class, 'completed'])->name('mobile.maintenance.completed');
        
        // Maintenance actions
        Route::post('/r/{maintenance}/approve', [App\Http\Controllers\Mobile\MaintenanceController::class, 'approve'])->name('mobile.maintenance.approve');
        Route::post('/r/{maintenance}/decline', [App\Http\Controllers\Mobile\MaintenanceController::class, 'decline'])->name('mobile.maintenance.decline');
        Route::post('/r/{maintenance}/complete', [App\Http\Controllers\Mobile\MaintenanceController::class, 'complete'])->name('mobile.maintenance.complete');
    });
    
    // Technician mobile routes
    Route::middleware(['technician'])->group(function () {
        // Dashboard
        Route::get('/t/dash', [App\Http\Controllers\Mobile\TechnicianDashboardController::class, 'dashboard'])->name('mobile.technician.dashboard');
        
        // Requests
        Route::get('/t/r/assigned', [App\Http\Controllers\Mobile\TechnicianRequestController::class, 'assigned'])->name('mobile.technician.assigned');
        Route::get('/t/r/accepted', [App\Http\Controllers\Mobile\TechnicianRequestController::class, 'accepted'])->name('mobile.technician.accepted');
        Route::get('/t/r/started', [App\Http\Controllers\Mobile\TechnicianRequestController::class, 'started'])->name('mobile.technician.started');
        Route::get('/t/r/completed', [App\Http\Controllers\Mobile\TechnicianRequestController::class, 'completed'])->name('mobile.technician.completed');
        
        // Actions
        Route::post('/t/r/{maintenance}/accept', [App\Http\Controllers\Mobile\TechnicianRequestController::class, 'accept'])->name('mobile.technician.accept');
        Route::post('/t/r/{maintenance}/decline', [App\Http\Controllers\Mobile\TechnicianRequestController::class, 'decline'])->name('mobile.technician.decline');
        Route::post('/t/r/{maintenance}/start', [App\Http\Controllers\Mobile\TechnicianRequestController::class, 'start'])->name('mobile.technician.start');
        Route::post('/t/r/{maintenance}/finish', [App\Http\Controllers\Mobile\TechnicianRequestController::class, 'finish'])->name('mobile.technician.finish');
    });
});
