<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Comment;
use App\Models\RequestImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TechnicianRequestController extends Controller
{
    /**
     * Display a listing of assigned maintenance requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function assigned(Request $request)
    {
        $user = Auth::user();
        
        $requestId = $request->query('id');
        
        if ($requestId) {
            $maintenanceRequest = MaintenanceRequest::with(['property', 'comments.user', 'images'])
                ->where('technician_id', $user->id)
                ->where('status', 'assigned')
                ->findOrFail($requestId);
                
            // Get count of requests in each status
            $assignedCount = MaintenanceRequest::where('technician_id', $user->id)
                ->where('status', 'assigned')
                ->count();
            
            $acceptedCount = MaintenanceRequest::where('technician_id', $user->id)
                ->where('status', 'acknowledged')
                ->count();
                
            $completedCount = MaintenanceRequest::where('technician_id', $user->id)
                ->whereIn('status', ['completed', 'closed'])
                ->count();
                
            return view('mobile.technician.request', [
                'request' => $maintenanceRequest,
                'assignedCount' => $assignedCount,
                'acceptedCount' => $acceptedCount,
                'completedCount' => $completedCount
            ]);
        }
        
        $requests = MaintenanceRequest::with('property')
            ->where('technician_id', $user->id)
            ->where('status', 'assigned')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('mobile.technician.assigned', compact('requests'));
    }
    
    /**
     * Display a listing of accepted maintenance requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function accepted(Request $request)
    {
        $user = Auth::user();
        
        $requestId = $request->query('id');
        
        if ($requestId) {
            $maintenanceRequest = MaintenanceRequest::with(['property', 'comments.user', 'images'])
                ->where('technician_id', $user->id)
                ->where('status', 'acknowledged')
                ->findOrFail($requestId);
                
            // Get count of requests in each status
            $assignedCount = MaintenanceRequest::where('technician_id', $user->id)
                ->where('status', 'assigned')
                ->count();
            
            $acceptedCount = MaintenanceRequest::where('technician_id', $user->id)
                ->where('status', 'acknowledged')
                ->count();
                
            $completedCount = MaintenanceRequest::where('technician_id', $user->id)
                ->whereIn('status', ['completed', 'closed'])
                ->count();
                
            return view('mobile.technician.request', [
                'request' => $maintenanceRequest,
                'assignedCount' => $assignedCount,
                'acceptedCount' => $acceptedCount,
                'completedCount' => $completedCount
            ]);
        }
        
        $requests = MaintenanceRequest::with('property')
            ->where('technician_id', $user->id)
            ->where('status', 'acknowledged')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('mobile.technician.accepted', compact('requests'));
    }
    
    /**
     * Display a listing of started maintenance requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function started(Request $request)
    {
        $user = Auth::user();
        
        $requestId = $request->query('id');
        
        if ($requestId) {
            $maintenanceRequest = MaintenanceRequest::with(['property', 'comments.user', 'images'])
                ->where('technician_id', $user->id)
                ->where('status', 'started')
                ->findOrFail($requestId);
                
            // Get count of requests in each status
            $assignedCount = MaintenanceRequest::where('technician_id', $user->id)
                ->where('status', 'assigned')
                ->count();
            
            $acceptedCount = MaintenanceRequest::where('technician_id', $user->id)
                ->where('status', 'acknowledged')
                ->count();
                
            $completedCount = MaintenanceRequest::where('technician_id', $user->id)
                ->whereIn('status', ['completed', 'closed'])
                ->count();
                
            return view('mobile.technician.request', [
                'request' => $maintenanceRequest,
                'assignedCount' => $assignedCount,
                'acceptedCount' => $acceptedCount,
                'completedCount' => $completedCount
            ]);
        }
        
        $requests = MaintenanceRequest::with('property')
            ->where('technician_id', $user->id)
            ->where('status', 'started')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('mobile.technician.started', compact('requests'));
    }
    
    /**
     * Display a listing of completed maintenance requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function completed(Request $request)
    {
        $user = Auth::user();
        
        $requestId = $request->query('id');
        
        if ($requestId) {
            $maintenanceRequest = MaintenanceRequest::with(['property', 'comments.user', 'images'])
                ->where('technician_id', $user->id)
                ->whereIn('status', ['completed', 'closed'])
                ->findOrFail($requestId);
                
            // Get count of requests in each status
            $assignedCount = MaintenanceRequest::where('technician_id', $user->id)
                ->where('status', 'assigned')
                ->count();
            
            $acceptedCount = MaintenanceRequest::where('technician_id', $user->id)
                ->where('status', 'acknowledged')
                ->count();
                
            $completedCount = MaintenanceRequest::where('technician_id', $user->id)
                ->whereIn('status', ['completed', 'closed'])
                ->count();
                
            return view('mobile.technician.request', [
                'request' => $maintenanceRequest,
                'assignedCount' => $assignedCount,
                'acceptedCount' => $acceptedCount,
                'completedCount' => $completedCount
            ]);
        }
        
        $requests = MaintenanceRequest::with('property')
            ->where('technician_id', $user->id)
            ->whereIn('status', ['completed', 'closed'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('mobile.technician.completed', compact('requests'));
    }
    
    /**
     * Accept a maintenance request.
     *
     * @param  \App\Models\MaintenanceRequest  $maintenance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accept(MaintenanceRequest $maintenance)
    {
        // Call the existing accept method from the base controller
        app(\App\Http\Controllers\MaintenanceRequestController::class)->accept($maintenance);
        
        return redirect()->route('mobile.technician.accepted')
            ->with('success', 'Maintenance request accepted successfully');
    }
    
    /**
     * Decline a maintenance request.
     *
     * @param  \App\Models\MaintenanceRequest  $maintenance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function decline(MaintenanceRequest $maintenance)
    {
        // Call the existing reject method from the base controller
        app(\App\Http\Controllers\MaintenanceRequestController::class)->reject($maintenance);
        
        return redirect()->route('mobile.technician.dashboard')
            ->with('success', 'Maintenance request declined successfully');
    }
    
    /**
     * Start a maintenance task.
     *
     * @param  \App\Models\MaintenanceRequest  $maintenance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function start(MaintenanceRequest $maintenance)
    {
        // Call the existing startTask method from the base controller
        app(\App\Http\Controllers\MaintenanceRequestController::class)->startTask($maintenance);
        
        return redirect()->route('mobile.technician.started')
            ->with('success', 'Maintenance task started successfully');
    }
    
    /**
     * Finish a maintenance task.
     *
     * @param  \App\Models\MaintenanceRequest  $maintenance
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finish(MaintenanceRequest $maintenance, Request $request)
    {
        $request->validate([
            'comment' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Update maintenance request status
        $maintenance->status = 'completed';
        $maintenance->completed_at = now();
        $maintenance->save();
        
        // Add comment
        $comment = new Comment();
        $comment->maintenance_request_id = $maintenance->id;
        $comment->user_id = Auth::id();
        $comment->comment = $request->comment;
        $comment->save();
        
        // Upload images if provided
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('maintenance/completion', 'public');
                
                $requestImage = new RequestImage();
                $requestImage->maintenance_request_id = $maintenance->id;
                $requestImage->path = $path;
                $requestImage->comment_id = $comment->id;
                $requestImage->save();
            }
        }
        
        return redirect()->route('mobile.technician.completed')
            ->with('success', 'Maintenance task completed successfully');
    }
} 