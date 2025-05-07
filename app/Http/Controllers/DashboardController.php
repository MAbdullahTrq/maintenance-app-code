<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function adminDashboard()
    {
        $totalPropertyManagers = User::whereHas('role', function ($query) {
            $query->where('slug', 'property_manager');
        })->count();
        
        $totalTechnicians = User::whereHas('role', function ($query) {
            $query->where('slug', 'technician');
        })->count();
        
        $totalProperties = Property::count();
        
        $totalRequests = MaintenanceRequest::count();
        
        $pendingRequests = MaintenanceRequest::where('status', 'pending')->count();
        $inProgressRequests = MaintenanceRequest::whereIn('status', ['assigned', 'acknowledged', 'started'])->count();
        $completedRequests = MaintenanceRequest::where('status', 'completed')->count();
        
        // Get active users
        $activeUsers = User::with('role')
            ->where('is_active', true)
            ->latest()
            ->take(5)
            ->get();

        // Get subscription statistics
        $activeSubscriptions = Subscription::where('status', 'active')
            ->where('ends_at', '>', now())
            ->count();

        $expiredSubscriptions = Subscription::where(function($query) {
            $query->where('status', 'expired')
                ->orWhere('ends_at', '<=', now());
        })->count();
        
        return view('dashboards.admin', compact(
            'totalPropertyManagers',
            'totalTechnicians',
            'totalProperties',
            'totalRequests',
            'pendingRequests',
            'inProgressRequests',
            'completedRequests',
            'activeUsers',
            'activeSubscriptions',
            'expiredSubscriptions'
        ));
    }

    /**
     * Show the property manager dashboard.
     */
    public function managerDashboard()
    {
        $user = Auth::user();
        
        $propertyIds = $user->managedProperties()->pluck('id');
        
        $totalProperties = $user->managedProperties()->count();
        
        $totalTechnicians = User::where('invited_by', $user->id)
            ->whereHas('role', function ($query) {
                $query->where('slug', 'technician');
            })
            ->count();
        
        $totalRequests = MaintenanceRequest::whereIn('property_id', $propertyIds)->count();
        
        $pendingRequests = MaintenanceRequest::whereIn('property_id', $propertyIds)
            ->where('status', 'pending')
            ->count();
        
        // Split the in-progress into assigned and started
        $assignedRequests = MaintenanceRequest::whereIn('property_id', $propertyIds)
            ->whereIn('status', ['assigned', 'acknowledged'])
            ->count();
            
        $startedRequests = MaintenanceRequest::whereIn('property_id', $propertyIds)
            ->where('status', 'started')
            ->count();
            
        $inProgressRequests = $assignedRequests + $startedRequests;
        
        $completedRequests = MaintenanceRequest::whereIn('property_id', $propertyIds)
            ->where('status', 'completed')
            ->count();
        
        $closedRequests = MaintenanceRequest::whereIn('property_id', $propertyIds)
            ->where('status', 'closed')
            ->count();
        
        $recentRequests = MaintenanceRequest::whereIn('property_id', $propertyIds)
            ->with(['property', 'assignedTechnician'])
            ->latest()
            ->take(5)
            ->get();
        
        $technicians = User::where('invited_by', $user->id)
            ->whereHas('role', function ($query) {
                $query->where('slug', 'technician');
            })
            ->withCount(['assignedRequests as pending_count' => function ($query) {
                $query->where('status', 'assigned');
            }, 'assignedRequests as in_progress_count' => function ($query) {
                $query->where('status', 'started');
            }])
            ->get();
        
        return view('dashboards.manager', compact(
            'totalProperties',
            'totalTechnicians',
            'totalRequests',
            'pendingRequests',
            'assignedRequests',
            'startedRequests',
            'inProgressRequests',
            'completedRequests',
            'closedRequests',
            'recentRequests',
            'technicians'
        ));
    }

    /**
     * Show the technician dashboard.
     */
    public function technicianDashboard()
    {
        $user = Auth::user();
        
        $totalAssignedRequests = $user->assignedRequests()->count();
        
        $assignedRequests = $user->assignedRequests()
            ->where('status', 'assigned')
            ->count();
        
        $acknowledgedRequests = $user->assignedRequests()
            ->where('status', 'acknowledged')
            ->count();
        
        $pendingRequests = $acknowledgedRequests;
        
        $inProgressRequests = $user->assignedRequests()
            ->where('status', 'started')
            ->count();
        
        $completedRequests = $user->assignedRequests()
            ->where('status', 'completed')
            ->count();
        
        $upcomingRequests = $user->assignedRequests()
            ->with('property')
            ->whereIn('status', ['assigned', 'acknowledged'])
            ->orderBy('due_date')
            ->take(5)
            ->get();
        
        $inProgressTasks = $user->assignedRequests()
            ->with('property')
            ->where('status', 'started')
            ->latest()
            ->take(5)
            ->get();
        
        return view('dashboards.technician', compact(
            'totalAssignedRequests',
            'assignedRequests',
            'acknowledgedRequests',
            'pendingRequests',
            'inProgressRequests',
            'completedRequests',
            'upcomingRequests',
            'inProgressTasks'
        ));
    }
} 