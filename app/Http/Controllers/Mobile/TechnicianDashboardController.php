<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\Auth;

class TechnicianDashboardController extends Controller
{
    /**
     * Show the technician mobile dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get request counts
        $assignedCount = MaintenanceRequest::where('technician_id', $user->id)
            ->where('status', 'assigned')
            ->count();
            
        $acceptedCount = MaintenanceRequest::where('technician_id', $user->id)
            ->where('status', 'acknowledged')
            ->count();
            
        $startedCount = MaintenanceRequest::where('technician_id', $user->id)
            ->where('status', 'started')
            ->count();
            
        $completedCount = MaintenanceRequest::where('technician_id', $user->id)
            ->where('status', 'completed')
            ->count();
        
        // Get assigned requests
        $assignedRequests = MaintenanceRequest::with('property')
            ->where('technician_id', $user->id)
            ->where('status', 'assigned')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('mobile.technician.dashboard', compact(
            'assignedCount',
            'acceptedCount',
            'completedCount',
            'assignedRequests'
        ));
    }
} 