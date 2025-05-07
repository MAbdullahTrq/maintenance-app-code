<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of maintenance requests.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $pendingCount = MaintenanceRequest::where('status', 'pending')->count();
        $assignedCount = MaintenanceRequest::whereIn('status', ['assigned', 'acknowledged', 'started'])->count();
        $completedCount = MaintenanceRequest::whereIn('status', ['completed', 'closed'])->count();
        
        $requests = MaintenanceRequest::with('property')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('mobile.maintenance.index', compact(
            'requests',
            'pendingCount',
            'assignedCount',
            'completedCount'
        ));
    }
    
    /**
     * Display a listing of pending maintenance requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function pending(Request $request)
    {
        $requestId = $request->query('id');
        
        if ($requestId) {
            $maintenanceRequest = MaintenanceRequest::with(['property', 'technician', 'comments.user', 'images'])
                ->findOrFail($requestId);
                
            return view('mobile.maintenance.show', [
                'request' => $maintenanceRequest
            ]);
        }
        
        $pendingCount = MaintenanceRequest::where('status', 'pending')->count();
        $assignedCount = MaintenanceRequest::whereIn('status', ['assigned', 'acknowledged', 'started'])->count();
        $completedCount = MaintenanceRequest::whereIn('status', ['completed', 'closed'])->count();
        
        $requests = MaintenanceRequest::with('property')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('mobile.maintenance.index', compact(
            'requests',
            'pendingCount',
            'assignedCount',
            'completedCount'
        ));
    }
    
    /**
     * Display a listing of assigned maintenance requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function assigned(Request $request)
    {
        $requestId = $request->query('id');
        
        if ($requestId) {
            $maintenanceRequest = MaintenanceRequest::with(['property', 'technician', 'comments.user', 'images'])
                ->findOrFail($requestId);
                
            return view('mobile.maintenance.show', [
                'request' => $maintenanceRequest
            ]);
        }
        
        $pendingCount = MaintenanceRequest::where('status', 'pending')->count();
        $assignedCount = MaintenanceRequest::whereIn('status', ['assigned', 'acknowledged', 'started'])->count();
        $completedCount = MaintenanceRequest::whereIn('status', ['completed', 'closed'])->count();
        
        $requests = MaintenanceRequest::with('property')
            ->whereIn('status', ['assigned', 'acknowledged', 'started'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('mobile.maintenance.index', compact(
            'requests',
            'pendingCount',
            'assignedCount',
            'completedCount'
        ));
    }
    
    /**
     * Display a listing of completed maintenance requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function completed(Request $request)
    {
        $requestId = $request->query('id');
        
        if ($requestId) {
            $maintenanceRequest = MaintenanceRequest::with(['property', 'technician', 'comments.user', 'images'])
                ->findOrFail($requestId);
                
            return view('mobile.maintenance.show', [
                'request' => $maintenanceRequest
            ]);
        }
        
        $pendingCount = MaintenanceRequest::where('status', 'pending')->count();
        $assignedCount = MaintenanceRequest::whereIn('status', ['assigned', 'acknowledged', 'started'])->count();
        $completedCount = MaintenanceRequest::whereIn('status', ['completed', 'closed'])->count();
        
        $requests = MaintenanceRequest::with('property')
            ->whereIn('status', ['completed', 'closed'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('mobile.maintenance.index', compact(
            'requests',
            'pendingCount',
            'assignedCount',
            'completedCount'
        ));
    }
    
    /**
     * Approve a maintenance request.
     *
     * @param  \App\Models\MaintenanceRequest  $maintenance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(MaintenanceRequest $maintenance)
    {
        // Call the existing approve method from the base controller
        app(\App\Http\Controllers\MaintenanceRequestController::class)->approve($maintenance);
        
        return redirect()->route('mobile.maintenance.pending')
            ->with('success', 'Maintenance request approved successfully');
    }
    
    /**
     * Decline a maintenance request.
     *
     * @param  \App\Models\MaintenanceRequest  $maintenance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function decline(MaintenanceRequest $maintenance)
    {
        // Call the existing decline method from the base controller
        app(\App\Http\Controllers\MaintenanceRequestController::class)->decline($maintenance);
        
        return redirect()->route('mobile.maintenance.pending')
            ->with('success', 'Maintenance request declined successfully');
    }
    
    /**
     * Mark a maintenance request as complete.
     *
     * @param  \App\Models\MaintenanceRequest  $maintenance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function complete(MaintenanceRequest $maintenance)
    {
        // Call the existing complete method from the base controller
        app(\App\Http\Controllers\MaintenanceRequestController::class)->complete($maintenance);
        
        return redirect()->route('mobile.maintenance.completed')
            ->with('success', 'Maintenance request marked as closed successfully');
    }
} 