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
use App\Http\Controllers\Mobile\DashboardController as MobileDashboardController;
use App\Http\Controllers\Mobile\RequestController;
use App\Http\Controllers\Mobile\TechnicianController as MobileTechnicianController;
use App\Http\Controllers\Mobile\PropertyController as MobilePropertyController;
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

Route::get('/mobile', [App\Http\Controllers\Mobile\HomeController::class, 'index'])->name('mobile.home');

// Protect mobile routes with auth middleware
Route::prefix('m')->middleware('auth')->group(function () {
    Route::get('/dash', [App\Http\Controllers\Mobile\DashboardController::class, 'index'])->name('mobile.manager.dashboard');
    Route::get('/r/{id}', [App\Http\Controllers\Mobile\RequestController::class, 'show'])->name('mobile.request.show');
    Route::get('/ap/create', [MobilePropertyController::class, 'create'])->name('mobile.properties.create');
    Route::get('/at/create', [MobileTechnicianController::class, 'create'])->name('mobile.technicians.create');
    Route::get('/at', [MobileTechnicianController::class, 'index'])->name('mobile.technicians.index');
    Route::post('/at/add', [MobileTechnicianController::class, 'store'])->name('mobile.technicians.store');
    Route::get('/at/{id}/edit', [App\Http\Controllers\Mobile\TechnicianController::class, 'edit'])->name('mobile.technicians.edit');
    Route::post('/at/{id}/delete', [MobileTechnicianController::class, 'destroy'])->name('mobile.technicians.destroy');
    Route::post('/at/{id}/edit', [App\Http\Controllers\Mobile\TechnicianController::class, 'update'])->name('mobile.technicians.update');
    Route::get('/at/{id}', [App\Http\Controllers\Mobile\TechnicianController::class, 'show'])->name('mobile.technicians.show');
    Route::get('/ap', [MobilePropertyController::class, 'index'])->name('mobile.properties.index');
    Route::post('/ap/add', [MobilePropertyController::class, 'store'])->name('mobile.properties.store');
    Route::post('/ap/{id}/edit', [MobilePropertyController::class, 'update'])->name('mobile.properties.update');
    Route::post('/ap/{id}/delete', [MobilePropertyController::class, 'destroy'])->name('mobile.properties.destroy');
    Route::get('/ap/{id}', [MobilePropertyController::class, 'show'])->name('mobile.properties.show');
    Route::get('/ep/{id}', [MobilePropertyController::class, 'edit'])->name('mobile.properties.edit');
    Route::post('/ep/{id}', [MobilePropertyController::class, 'update'])->name('mobile.properties.update');
    Route::get('/ap/{id}/qrcode', [MobilePropertyController::class, 'qrcode'])->name('mobile.properties.qrcode');
    Route::get('/manager/all-requests', [App\Http\Controllers\Mobile\DashboardController::class, 'allRequests'])->name('mobile.manager.all-requests');
    Route::get('/profile', [App\Http\Controllers\Mobile\ProfileController::class, 'show'])->name('mobile.profile');
    Route::post('/profile/update-picture', [App\Http\Controllers\Mobile\ProfileController::class, 'updatePicture'])->name('mobile.profile.update-picture');
    Route::get('/profile/change-password', [App\Http\Controllers\Mobile\ProfileController::class, 'showChangePassword'])->name('mobile.profile.change-password');
    Route::post('/profile/change-password', [App\Http\Controllers\Mobile\ProfileController::class, 'changePassword'])->name('mobile.profile.change-password.submit');
    Route::middleware('can:property_manager')->group(function () {
        Route::get('/requests/create', [RequestController::class, 'create'])->name('mobile.requests.create');
        Route::post('/requests/add', [RequestController::class, 'store'])->name('mobile.requests.store');
    });
    // More mobile routes will be added here
});

Route::get('/m/login', [App\Http\Controllers\Mobile\LoginController::class, 'showLoginForm'])->name('mobile.login');
Route::post('/m/login', [App\Http\Controllers\Mobile\LoginController::class, 'login'])->name('mobile.login.submit');
Route::get('/m/register', [App\Http\Controllers\Mobile\RegisterController::class, 'showRegistrationForm'])->name('mobile.register');
Route::post('/m/register', [App\Http\Controllers\Mobile\RegisterController::class, 'register'])->name('mobile.register.submit');

// Technician mobile dashboard routes
Route::prefix('t')->middleware(['auth', 'technician'])->group(function () {
    Route::get('/', [App\Http\Controllers\Mobile\TechnicianController::class, 'dashboard'])->name('mobile.technician.dashboard');
    Route::get('/r/{id}', [App\Http\Controllers\Mobile\TechnicianController::class, 'showRequest'])->name('mobile.technician.request.show');
    Route::post('/r/{id}/accept', [App\Http\Controllers\Mobile\TechnicianController::class, 'acceptRequest'])->name('mobile.technician.request.accept');
    Route::post('/r/{id}/decline', [App\Http\Controllers\Mobile\TechnicianController::class, 'declineRequest'])->name('mobile.technician.request.decline');
    Route::post('/r/{id}/start', [App\Http\Controllers\Mobile\TechnicianController::class, 'startRequest'])->name('mobile.technician.request.start');
    Route::post('/r/{id}/finish', [App\Http\Controllers\Mobile\TechnicianController::class, 'finishRequest'])->name('mobile.technician.request.finish');
});
