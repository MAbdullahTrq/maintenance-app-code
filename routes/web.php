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
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        
        // User management routes
        Route::resource('users', UserController::class);
        Route::put('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        
        // Subscription management routes
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscription.index');
        Route::get('/subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
        Route::get('/subscription/plans/create', [SubscriptionController::class, 'createPlan'])->name('subscription.plans.create');
        Route::post('/subscription/plans', [SubscriptionController::class, 'storePlan'])->name('subscription.plans.store');
        Route::get('/subscription/plans/{plan}/edit', [SubscriptionController::class, 'editPlan'])->name('subscription.plans.edit');
        Route::put('/subscription/plans/{plan}', [SubscriptionController::class, 'updatePlan'])->name('subscription.plans.update');
        Route::delete('/subscription/plans/{plan}', [SubscriptionController::class, 'destroyPlan'])->name('subscription.plans.destroy');
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
    Route::post('/maintenance/{maintenance}/in-progress', [MaintenanceRequestController::class, 'inProgress'])->name('maintenance.in-progress');
    Route::post('/maintenance/{maintenance}/complete', [MaintenanceRequestController::class, 'complete'])->name('maintenance.complete');
    Route::post('/maintenance/{maintenance}/assign', [MaintenanceRequestController::class, 'assign'])->name('maintenance.assign');
    Route::post('/maintenance/{maintenance}/accept', [MaintenanceRequestController::class, 'accept'])->name('maintenance.accept');
    Route::post('/maintenance/{maintenance}/reject', [MaintenanceRequestController::class, 'reject'])->name('maintenance.reject');
    Route::post('/maintenance/{maintenance}/comment', [MaintenanceRequestController::class, 'addComment'])->name('maintenance.comment');
    Route::delete('/maintenance/image/{image}', [MaintenanceRequestController::class, 'deleteImage'])->name('maintenance.image.delete');
    Route::delete('/maintenance/comment/{comment}', [MaintenanceRequestController::class, 'deleteComment'])->name('maintenance.comment.delete');

    // Debug routes
    if (config('app.debug')) {
        Route::get('/debug/roles', function () {
            return ['status' => 'success', 'roles' => \App\Models\Role::all()];
        });
    }
});
