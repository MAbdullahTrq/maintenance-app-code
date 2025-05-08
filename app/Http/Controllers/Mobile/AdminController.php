<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Property;
use App\Models\MaintenanceRequest;
use App\Models\Subscription;

class AdminController extends Controller
{
    /**
     * Display the mobile admin dashboard
     */
    public function dashboard()
    {
        // Set page title and URL for mobile layout
        $pageTitle = 'Admin Dashboard';
        $pageUrl = url()->current();
        
        // Get counts for dashboard stats
        $propertyManagersCount = User::whereHas('role', function($query) {
            $query->where('slug', 'property_manager');
        })->count();
        
        $techniciansCount = User::whereHas('role', function($query) {
            $query->where('slug', 'technician');
        })->count();
        
        $propertiesCount = Property::count();
        
        $totalRequestsCount = MaintenanceRequest::count();
        
        // Request status counts
        $pendingRequestsCount = MaintenanceRequest::where('status', 'pending')->count();
        $inProgressRequestsCount = MaintenanceRequest::whereIn('status', ['approved', 'assigned', 'in_progress'])->count();
        $completedRequestsCount = MaintenanceRequest::where('status', 'completed')->count();
        
        // Subscription counts
        $activeSubscriptionsCount = Subscription::where('ends_at', '>', now())->count();
        $expiredSubscriptionsCount = Subscription::where('ends_at', '<=', now())->count();
        
        // Get recent users
        $recentUsers = User::with('role')->latest()->take(5)->get();
        
        return view('mobile.admin.dashboard', compact(
            'pageTitle',
            'pageUrl',
            'propertyManagersCount',
            'techniciansCount',
            'propertiesCount',
            'totalRequestsCount',
            'pendingRequestsCount',
            'inProgressRequestsCount',
            'completedRequestsCount',
            'activeSubscriptionsCount',
            'expiredSubscriptionsCount',
            'recentUsers'
        ));
    }
} 