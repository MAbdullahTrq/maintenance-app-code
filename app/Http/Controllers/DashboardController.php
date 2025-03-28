<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\User;
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
        $inProgressRequests = MaintenanceRequest::where('status', 'in_progress')->count();
        $completedRequests = MaintenanceRequest::where('status', 'completed')->count();
        
        $recentRequests = MaintenanceRequest::with(['property', 'assignedTechnician'])
            ->latest()
            ->take(5)
            ->get();
        
        return view('dashboards.admin', compact(
            'totalPropertyManagers',
            'totalTechnicians',
            'totalProperties',
            'totalRequests',
            'pendingRequests',
            'inProgressRequests',
            'completedRequests',
            'recentRequests'
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
        
        $inProgressRequests = MaintenanceRequest::whereIn('property_id', $propertyIds)
            ->where('status', 'in_progress')
            ->count();
        
        $completedRequests = MaintenanceRequest::whereIn('property_id', $propertyIds)
            ->where('status', 'completed')
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
                $query->where('status', 'pending');
            }, 'assignedRequests as in_progress_count' => function ($query) {
                $query->where('status', 'in_progress');
            }])
            ->get();
        
        return view('dashboards.manager', compact(
            'totalProperties',
            'totalTechnicians',
            'totalRequests',
            'pendingRequests',
            'inProgressRequests',
            'completedRequests',
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
        
        $pendingRequests = $user->assignedRequests()
            ->where('status', 'approved')
            ->count();
        
        $inProgressRequests = $user->assignedRequests()
            ->where('status', 'in_progress')
            ->count();
        
        $completedRequests = $user->assignedRequests()
            ->where('status', 'completed')
            ->count();
        
        $upcomingRequests = $user->assignedRequests()
            ->with('property')
            ->where('status', 'approved')
            ->orderBy('due_date')
            ->take(5)
            ->get();
        
        $inProgressTasks = $user->assignedRequests()
            ->with('property')
            ->where('status', 'in_progress')
            ->latest()
            ->take(5)
            ->get();
        
        return view('dashboards.technician', compact(
            'totalAssignedRequests',
            'pendingRequests',
            'inProgressRequests',
            'completedRequests',
            'upcomingRequests',
            'inProgressTasks'
        ));
    }
} 