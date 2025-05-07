<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    /**
     * Show the manager mobile dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get request counts
        $pendingRequests = MaintenanceRequest::where('status', 'pending')->count();
        $assignedRequests = MaintenanceRequest::whereIn('status', ['assigned', 'acknowledged'])->count();
        $startedRequests = MaintenanceRequest::where('status', 'started')->count();
        $completedRequests = MaintenanceRequest::where('status', 'completed')->count();
        $closedRequests = MaintenanceRequest::where('status', 'closed')->count();
        $totalRequests = MaintenanceRequest::count();
        
        // Get properties
        $properties = Property::take(2)->get();
        
        // Get recent requests
        $recentRequests = MaintenanceRequest::with('property')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('mobile.manager.dashboard', compact(
            'pendingRequests',
            'assignedRequests',
            'startedRequests',
            'completedRequests',
            'closedRequests',
            'totalRequests',
            'properties',
            'recentRequests'
        ));
    }
} 