<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestRequestController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\Mobile\DashboardController as MobileDashboardController;
use App\Http\Controllers\Mobile\RequestController;
use App\Http\Controllers\Mobile\TechnicianController as MobileTechnicianController;
use App\Http\Controllers\Mobile\PropertyController as MobilePropertyController;
use App\Http\Controllers\Mobile\OwnerController as MobileOwnerController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Mobile\SubscriptionController as MobileSubscriptionController;
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
// Email verification routes (accessible to both guests and authenticated users)
Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
Route::get('/email/verify/{token}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::get('/email/resend-form', [EmailVerificationController::class, 'showResendForm'])->name('verification.resend.form');
Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');

// Debug route to test verification
Route::get('/debug/verify/{token}', function($token) {
    \Log::info('Debug verification route hit', [
        'token' => $token,
        'email' => request('email'),
        'all_request' => request()->all()
    ]);
    return response()->json([
        'token' => $token,
        'email' => request('email'),
        'query_params' => request()->all()
    ]);
});

// Debug route for Turnstile testing
Route::get('/debug/turnstile', function () {
    return view('debug.turnstile');
});

// PWA Manifest route (optional - static file should work)
Route::get('/manifest.json', function () {
    return response()->file(public_path('manifest.json'), [
        'Content-Type' => 'application/manifest+json'
    ]);
});

// Report routes
Route::middleware(['auth'])->group(function () {
    // Desktop report routes
    Route::get('/reports/create', [App\Http\Controllers\ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports/generate', [App\Http\Controllers\ReportController::class, 'generate'])->name('reports.generate');
    Route::post('/reports/csv', [App\Http\Controllers\ReportController::class, 'generateCSVReport'])->name('reports.csv');
    Route::post('/reports/pdf', [App\Http\Controllers\ReportController::class, 'generatePDFReport'])->name('reports.pdf');
    Route::post('/reports/docx', [App\Http\Controllers\ReportController::class, 'generateDOCXReport'])->name('reports.docx');
    Route::post('/reports/ai-summary', [App\Http\Controllers\ReportController::class, 'generateAISummary'])->name('reports.ai-summary');
    
    // AJAX endpoints for dynamic filtering
    Route::get('/api/properties-by-owner', [App\Http\Controllers\ReportController::class, 'getPropertiesByOwner'])->name('api.properties-by-owner');
    Route::get('/api/technicians-by-properties', [App\Http\Controllers\ReportController::class, 'getTechniciansByProperties'])->name('api.technicians-by-properties');
});

Route::middleware('guest')->group(function () {
    Route::get('/web/login', [LoginController::class, 'showLoginForm'])->name('web.login');
    Route::post('/web/login', [LoginController::class, 'login']);
    Route::get('/web/register', [RegisterController::class, 'showRegistrationForm'])->name('web.register');
    Route::post('/web/register', [RegisterController::class, 'register'])->name('web.register.submit');
    
    // Team invitation routes (no auth required)
    Route::get('/team/invitation/{token}', [App\Http\Controllers\TeamController::class, 'acceptInvitation'])->name('team.accept');
    Route::post('/team/invitation/{token}', [App\Http\Controllers\TeamController::class, 'processInvitation'])->name('team.process-invitation');
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
                return redirect()->route('mobile.subscription.plans');
            }
            return redirect()->route('mobile.manager.dashboard');
        } else {
            return redirect()->route('mobile.technician.dashboard');
        }
    })->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/password', [UserController::class, 'showChangePasswordForm'])->name('password.change');
    Route::put('/profile/password', [UserController::class, 'changePassword'])->name('profile.password.update');

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
        
        // Property manager routes with active subscription
        Route::middleware(['subscription'])->group(function () {
            // Owner management routes
            Route::get('/owners', [App\Http\Controllers\OwnerController::class, 'index'])->name('owners.index');
            Route::get('/owners/create', [App\Http\Controllers\OwnerController::class, 'create'])->name('owners.create');
            Route::post('/owners', [App\Http\Controllers\OwnerController::class, 'store'])->name('owners.store');
            Route::get('/owners/{owner}', [App\Http\Controllers\OwnerController::class, 'show'])->name('owners.show');
            Route::get('/owners/{owner}/edit', [App\Http\Controllers\OwnerController::class, 'edit'])->name('owners.edit');
            Route::put('/owners/{owner}', [App\Http\Controllers\OwnerController::class, 'update'])->name('owners.update');
            Route::delete('/owners/{owner}', [App\Http\Controllers\OwnerController::class, 'destroy'])->name('owners.destroy');
            
            // Team management routes
            Route::get('/team', [App\Http\Controllers\TeamController::class, 'index'])->name('team.index');
            Route::get('/team/create', [App\Http\Controllers\TeamController::class, 'create'])->name('team.create');
            Route::post('/team', [App\Http\Controllers\TeamController::class, 'store'])->name('team.store');
            Route::delete('/team/member/{memberId}', [App\Http\Controllers\TeamController::class, 'removeMember'])->name('team.remove-member');
            Route::delete('/team/invitation/{invitationId}', [App\Http\Controllers\TeamController::class, 'cancelInvitation'])->name('team.cancel-invitation');
            Route::put('/team/member/{memberId}/role', [App\Http\Controllers\TeamController::class, 'updateRole'])->name('team.update-role');
            
            // Technician management routes
            Route::get('/technicians', [App\Http\Controllers\TechnicianController::class, 'index'])->name('technicians.index');
            Route::get('/technicians/create', [App\Http\Controllers\TechnicianController::class, 'create'])->name('technicians.create');
            Route::post('/technicians', [App\Http\Controllers\TechnicianController::class, 'store'])->name('technicians.store');
            Route::get('/technicians/{user}/edit', [App\Http\Controllers\TechnicianController::class, 'edit'])->name('technicians.edit');
            Route::put('/technicians/{user}', [App\Http\Controllers\TechnicianController::class, 'update'])->name('technicians.update');
            Route::delete('/technicians/{user}', [App\Http\Controllers\TechnicianController::class, 'destroy'])->name('technicians.destroy');
            Route::post('/technicians/{user}/toggle-active', [App\Http\Controllers\TechnicianController::class, 'toggleActive'])->name('technicians.toggle-active');
            Route::post('/technicians/{user}/reset-password', [App\Http\Controllers\TechnicianController::class, 'resetPassword'])->name('technicians.reset-password');
            
            // Property management routes
            Route::get('/properties', [App\Http\Controllers\PropertyController::class, 'index'])->name('properties.index');
            Route::get('/properties/create', [App\Http\Controllers\PropertyController::class, 'create'])->name('properties.create');
            Route::post('/properties', [App\Http\Controllers\PropertyController::class, 'store'])->name('properties.store');
            Route::get('/properties/{property}', [App\Http\Controllers\PropertyController::class, 'show'])->name('properties.show');
            Route::get('/properties/{property}/edit', [App\Http\Controllers\PropertyController::class, 'edit'])->name('properties.edit');
            Route::put('/properties/{property}', [App\Http\Controllers\PropertyController::class, 'update'])->name('properties.update');
            Route::delete('/properties/{property}', [App\Http\Controllers\PropertyController::class, 'destroy'])->name('properties.destroy');
            Route::get('/properties/{property}/qrcode', [App\Http\Controllers\PropertyController::class, 'downloadQrCode'])->name('properties.qrcode');
            
            // Checklist management routes
            Route::resource('checklists', App\Http\Controllers\ChecklistController::class);
            Route::post('/checklists/{checklist}/items', [App\Http\Controllers\ChecklistItemController::class, 'store'])->name('checklists.items.store');
            Route::put('/checklists/{checklist}/items/{item}', [App\Http\Controllers\ChecklistItemController::class, 'update'])->name('checklists.items.update');
            Route::delete('/checklists/{checklist}/items/{item}', [App\Http\Controllers\ChecklistItemController::class, 'destroy'])->name('checklists.items.destroy');
            Route::post('/checklists/{checklist}/items/order', [App\Http\Controllers\ChecklistItemController::class, 'updateOrder'])->name('checklists.items.order');
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
    
    // Checklist response routes
    Route::post('/maintenance/{maintenance}/checklist/{checklistItem}/response', [App\Http\Controllers\ChecklistResponseController::class, 'store'])->name('checklist.responses.store');
    Route::put('/maintenance/{maintenance}/checklist/response/{response}', [App\Http\Controllers\ChecklistResponseController::class, 'update'])->name('checklist.responses.update');
    Route::delete('/maintenance/{maintenance}/checklist/response/{response}', [App\Http\Controllers\ChecklistResponseController::class, 'destroy'])->name('checklist.responses.destroy');

    // Debug routes
    if (config('app.debug')) {
        Route::get('/debug/roles', function () {
            return ['status' => 'success', 'roles' => \App\Models\Role::all()];
        });
    }
    
    // Simple test route (outside debug block)
    Route::post('/debug/simple-test', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Simple test route working',
            'data' => $request->all()
        ]);
    })->name('debug.simple.test');
    Route::get('/debug/user', function () {
        $user = auth()->user();
        return [
            'user' => $user,
            'role' => $user?->role,
            'is_property_manager' => $user?->isPropertyManager(),
            'gate_check' => \Illuminate\Support\Facades\Gate::allows('property_manager'),
        ];
    });
});

Route::get('/mobile', [App\Http\Controllers\Mobile\HomeController::class, 'index'])->name('mobile.home');

// Protect mobile routes with auth middleware
Route::prefix('m')->middleware('auth')->group(function () {
    Route::get('/dash', [App\Http\Controllers\Mobile\DashboardController::class, 'index'])->name('mobile.manager.dashboard');
    Route::get('/r/{id}', [App\Http\Controllers\Mobile\RequestController::class, 'show'])->name('mobile.request.show');
    Route::post('/r/{id}/approve', [App\Http\Controllers\Mobile\RequestController::class, 'approve'])->name('mobile.request.approve');
    Route::post('/r/{id}/decline', [App\Http\Controllers\Mobile\RequestController::class, 'decline'])->name('mobile.request.decline');
    Route::post('/r/{id}/complete', [App\Http\Controllers\Mobile\RequestController::class, 'complete'])->name('mobile.request.complete');
    Route::post('/r/{id}/close', [App\Http\Controllers\Mobile\RequestController::class, 'close'])->name('mobile.request.close');
    Route::post('/r/{id}/accept', [App\Http\Controllers\Mobile\RequestController::class, 'accept'])->name('mobile.request.accept');
    Route::post('/r/{id}/start', [App\Http\Controllers\Mobile\RequestController::class, 'start'])->name('mobile.request.start');
    Route::post('/r/{id}/finish', [App\Http\Controllers\Mobile\RequestController::class, 'finish'])->name('mobile.request.finish');
    Route::post('/r/{id}/comment', [App\Http\Controllers\Mobile\RequestController::class, 'comment'])->name('mobile.request.comment');
    Route::get('/manager/all-requests', [App\Http\Controllers\Mobile\DashboardController::class, 'allRequests'])->name('mobile.manager.all-requests');
    Route::get('/profile', [App\Http\Controllers\Mobile\ProfileController::class, 'show'])->name('mobile.profile');
    Route::post('/profile/update-picture', [App\Http\Controllers\Mobile\ProfileController::class, 'updatePicture'])->name('mobile.profile.update-picture');
    Route::get('/profile/change-password', [App\Http\Controllers\Mobile\ProfileController::class, 'showChangePassword'])->name('mobile.profile.change-password');
    Route::post('/profile/change-password', [App\Http\Controllers\Mobile\ProfileController::class, 'changePassword'])->name('mobile.profile.change-password.submit');
    
    // Mobile report routes (permission check handled in controller)
    Route::get('/reports/create', [App\Http\Controllers\ReportController::class, 'createMobile'])->name('mobile.reports.create');
    Route::post('/reports/generate', [App\Http\Controllers\ReportController::class, 'generateMobile'])->name('mobile.reports.generate');
    Route::post('/reports/csv', [App\Http\Controllers\ReportController::class, 'generateCSVReport'])->name('mobile.reports.csv');
    Route::post('/reports/pdf', [App\Http\Controllers\ReportController::class, 'generatePDFReport'])->name('mobile.reports.pdf');
    Route::post('/reports/docx', [App\Http\Controllers\ReportController::class, 'generateDOCXReport'])->name('mobile.reports.docx');
    Route::post('/reports/ai-summary', [App\Http\Controllers\ReportController::class, 'generateAISummary'])->name('mobile.reports.ai-summary');
    
    // Property manager specific routes
    Route::middleware(['property_manager'])->group(function () {
        
        Route::get('/requests/create', [RequestController::class, 'create'])->name('mobile.requests.create');
        Route::post('/requests/add', [RequestController::class, 'store'])->name('mobile.requests.store');
        
        // Owner request routes
        Route::get('/owner/requests/create', [App\Http\Controllers\Mobile\OwnerRequestController::class, 'create'])->name('mobile.owner.requests.create');
        Route::post('/owner/requests/store', [App\Http\Controllers\Mobile\OwnerRequestController::class, 'store'])->name('mobile.owner.requests.store');
        
        // Property manager routes with active subscription (mobile)
        Route::middleware(['subscription'])->group(function () {
            // Mobile owner management routes
            Route::get('/ao', [MobileOwnerController::class, 'index'])->name('mobile.owners.index');
            Route::get('/ao/create', [MobileOwnerController::class, 'create'])->name('mobile.owners.create');
            Route::post('/ao/add', [MobileOwnerController::class, 'store'])->name('mobile.owners.store');
            Route::get('/ao/{id}', [MobileOwnerController::class, 'show'])->name('mobile.owners.show');
            Route::get('/ao/{id}/edit', [MobileOwnerController::class, 'edit'])->name('mobile.owners.edit');
            Route::post('/ao/{id}/edit', [MobileOwnerController::class, 'update'])->name('mobile.owners.update');
            Route::delete('/ao/{id}', [MobileOwnerController::class, 'destroy'])->name('mobile.owners.destroy');
            Route::get('/ao/{id}/qrcode', [MobileOwnerController::class, 'qrcode'])->name('mobile.owners.qrcode');
            
            // Mobile technician management routes
            Route::get('/at/create', [MobileTechnicianController::class, 'create'])->name('mobile.technicians.create');
            Route::get('/at', [MobileTechnicianController::class, 'index'])->name('mobile.technicians.index');
            Route::post('/at/add', [MobileTechnicianController::class, 'store'])->name('mobile.technicians.store');
            Route::get('/at/{id}/edit', [App\Http\Controllers\Mobile\TechnicianController::class, 'edit'])->name('mobile.technicians.edit');
            Route::post('/at/{id}/delete', [MobileTechnicianController::class, 'destroy'])->name('mobile.technicians.destroy');
            Route::post('/at/{id}/edit', [App\Http\Controllers\Mobile\TechnicianController::class, 'update'])->name('mobile.technicians.update');
            Route::get('/at/{id}', [App\Http\Controllers\Mobile\TechnicianController::class, 'show'])->name('mobile.technicians.show');
            Route::post('/at/{id}/deactivate', [App\Http\Controllers\Mobile\TechnicianController::class, 'deactivate'])->name('mobile.technicians.deactivate');
            Route::post('/at/{id}/activate', [App\Http\Controllers\Mobile\TechnicianController::class, 'activate'])->name('mobile.technicians.activate');
            Route::post('/technicians/{id}/reset-password', [App\Http\Controllers\Mobile\TechnicianController::class, 'resetPassword'])->name('mobile.technicians.reset-password');
            
            // Mobile property management routes
            Route::get('/ap/create', [MobilePropertyController::class, 'create'])->name('mobile.properties.create');
            Route::get('/ap', [MobilePropertyController::class, 'index'])->name('mobile.properties.index');
            Route::post('/ap/add', [MobilePropertyController::class, 'store'])->name('mobile.properties.store');
            Route::post('/ap/{id}/edit', [MobilePropertyController::class, 'update'])->name('mobile.properties.update');
            Route::delete('/ap/{id}', [MobilePropertyController::class, 'destroy'])->name('mobile.properties.destroy');
            Route::get('/ap/{id}', [MobilePropertyController::class, 'show'])->name('mobile.properties.show');
            Route::get('/ep/{id}', [MobilePropertyController::class, 'edit'])->name('mobile.properties.edit');
            Route::post('/ep/{id}', [MobilePropertyController::class, 'update'])->name('mobile.properties.ep.update');
            Route::get('/ap/{id}/qrcode', [MobilePropertyController::class, 'qrcode'])->name('mobile.properties.qrcode');
            
            // Mobile team management routes
            Route::get('/team', [App\Http\Controllers\TeamController::class, 'index'])->name('mobile.team.index');
            Route::get('/team/create', [App\Http\Controllers\TeamController::class, 'create'])->name('mobile.team.create');
            Route::post('/team', [App\Http\Controllers\TeamController::class, 'store'])->name('mobile.team.store');
            Route::put('/team/member/{member}/role', [App\Http\Controllers\TeamController::class, 'updateMemberRole'])->name('mobile.team.member.role');
            Route::delete('/team/member/{member}', [App\Http\Controllers\TeamController::class, 'removeMember'])->name('mobile.team.member.remove');
            Route::delete('/team/invitation/{invitation}', [App\Http\Controllers\TeamController::class, 'cancelInvitation'])->name('mobile.team.invitation.cancel');
            
            // Mobile checklist management routes
            Route::get('/cl', [App\Http\Controllers\Mobile\ChecklistController::class, 'index'])->name('mobile.checklists.index');
            Route::get('/cl/create', [App\Http\Controllers\Mobile\ChecklistController::class, 'create'])->name('mobile.checklists.create');
            Route::post('/cl/add', [App\Http\Controllers\Mobile\ChecklistController::class, 'store'])->name('mobile.checklists.store');
            Route::get('/cl/{id}', [App\Http\Controllers\Mobile\ChecklistController::class, 'show'])->name('mobile.checklists.show');
            Route::get('/cl/{id}/edit', [App\Http\Controllers\Mobile\ChecklistController::class, 'edit'])->name('mobile.checklists.edit');
            Route::post('/cl/{id}/edit', [App\Http\Controllers\Mobile\ChecklistController::class, 'update'])->name('mobile.checklists.update');
            Route::delete('/cl/{id}', [App\Http\Controllers\Mobile\ChecklistController::class, 'destroy'])->name('mobile.checklists.destroy');
            
            // Mobile checklist item routes
            Route::post('/cl/{checklist}/items', [App\Http\Controllers\Mobile\ChecklistItemController::class, 'store'])->name('mobile.checklists.items.store');
            Route::delete('/cl/{checklist}/items/{item}', [App\Http\Controllers\Mobile\ChecklistItemController::class, 'destroy'])->name('mobile.checklists.items.destroy');
        });
    });

    Route::get('/subscription/plans', [MobileSubscriptionController::class, 'plans'])->name('mobile.subscription.plans');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Mobile\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Mobile\LoginController::class, 'login'])->name('login.submit');
    Route::get('/register', [App\Http\Controllers\Mobile\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Mobile\RegisterController::class, 'register'])->name('register.submit');
});

// Technician mobile dashboard routes
Route::prefix('t')->middleware(['auth', 'technician'])->group(function () {
    Route::get('/', [App\Http\Controllers\Mobile\TechnicianController::class, 'dashboard'])->name('mobile.technician.dashboard');
    Route::get('/r/{id}', [App\Http\Controllers\Mobile\TechnicianController::class, 'showRequest'])->name('mobile.technician.request.show');
    Route::post('/r/{id}/accept', [App\Http\Controllers\Mobile\TechnicianController::class, 'acceptRequest'])->name('mobile.technician.request.accept');
    Route::post('/r/{id}/decline', [App\Http\Controllers\Mobile\TechnicianController::class, 'declineRequest'])->name('mobile.technician.request.decline');
    Route::post('/r/{id}/start', [App\Http\Controllers\Mobile\TechnicianController::class, 'startRequest'])->name('mobile.technician.request.start');
    Route::post('/r/{id}/finish', [App\Http\Controllers\Mobile\TechnicianController::class, 'finishRequest'])->name('mobile.technician.request.finish');
});

// Password Reset Routes
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');


