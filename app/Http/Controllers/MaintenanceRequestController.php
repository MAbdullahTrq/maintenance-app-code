<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\RequestComment;
use App\Models\RequestImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaintenanceRequestController extends Controller
{
    /**
     * Display a listing of the maintenance requests.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $requests = MaintenanceRequest::with(['property', 'assignedTechnician', 'approvedBy'])->latest()->get();
        } elseif ($user->isPropertyManager()) {
            $propertyIds = $user->managedProperties()->pluck('id');
            $requests = MaintenanceRequest::with(['property', 'assignedTechnician', 'approvedBy'])
                ->whereIn('property_id', $propertyIds)
                ->latest()
                ->paginate(10);
        } else {
            $requests = $user->assignedRequests()
                ->with(['property'])
                ->latest()
                ->paginate(10);
        }
        
        return view('maintenance.index', compact('requests'));
    }

    /**
     * Show the form for creating a new maintenance request.
     */
    public function create()
    {
        $properties = Auth::user()->managedProperties()->get();
        
        return view('maintenance.create', compact('properties'));
    }

    /**
     * Store a newly created maintenance request in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'property_id' => 'required|exists:properties,id',
            'images.*' => 'nullable|image|max:2048',
        ]);

        // Check if user can create request for this property
        $property = Property::findOrFail($request->property_id);
        $this->authorize('createRequest', $property);

        $maintenanceRequest = MaintenanceRequest::create([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'property_id' => $request->property_id,
            'requester_name' => Auth::user()->name,
            'requester_email' => Auth::user()->email,
            'requester_phone' => Auth::user()->phone,
            'status' => 'pending', // Set initial status to pending
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('maintenance_requests', 'public');
                
                RequestImage::create([
                    'maintenance_request_id' => $maintenanceRequest->id,
                    'image_path' => $path,
                    'type' => 'request',
                ]);
            }
        }

        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance request created successfully.');
    }

    /**
     * Display the specified maintenance request.
     */
    public function show(MaintenanceRequest $maintenance)
    {
        $this->authorize('view', $maintenance);
        
        $maintenance->load(['property', 'assignedTechnician', 'approvedBy', 'images', 'comments.user']);
        
        $technicians = [];
        if (Auth::user()->isPropertyManager() || Auth::user()->isAdmin()) {
            $technicians = User::whereHas('role', function ($query) {
                $query->where('slug', 'technician');
            })->get();
        }
        
        return view('maintenance.show', compact('maintenance', 'technicians'));
    }

    /**
     * Show the form for editing the specified maintenance request.
     */
    public function edit(MaintenanceRequest $maintenance)
    {
        $this->authorize('update', $maintenance);
        
        $properties = Auth::user()->managedProperties()->get();
        
        return view('maintenance.edit', compact('maintenance', 'properties'));
    }

    /**
     * Update the specified maintenance request in storage.
     */
    public function update(Request $request, MaintenanceRequest $maintenance)
    {
        $this->authorize('update', $maintenance);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'property_id' => 'required|exists:properties,id',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $maintenance->update([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'property_id' => $request->property_id,
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('maintenance_requests', 'public');
                
                RequestImage::create([
                    'maintenance_request_id' => $maintenance->id,
                    'image_path' => $path,
                    'type' => 'request',
                ]);
            }
        }

        return redirect()->route('maintenance.show', $maintenance)
            ->with('success', 'Maintenance request updated successfully.');
    }

    /**
     * Remove the specified maintenance request from storage.
     */
    public function destroy(MaintenanceRequest $maintenance)
    {
        $this->authorize('delete', $maintenance);
        
        // Delete all images
        foreach ($maintenance->images as $image) {
            Storage::delete('public/' . $image->image_path);
        }
        
        $maintenance->delete();

        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance request deleted successfully.');
    }

    /**
     * Approve the maintenance request.
     */
    public function approve(Request $request, MaintenanceRequest $maintenance)
    {
        $this->authorize('approve', $maintenance);
        
        $request->validate([
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        $maintenance->markAsAccepted(Auth::user(), $request->due_date);

        RequestComment::create([
            'maintenance_request_id' => $maintenance->id,
            'user_id' => Auth::id(),
            'comment' => 'Request accepted' . ($request->due_date ? ' with due date ' . $request->due_date : '') . '.',
        ]);

        return redirect()->route('maintenance.show', $maintenance)
            ->with('success', 'Maintenance request accepted successfully.');
    }

    /**
     * Decline the maintenance request.
     */
    public function decline(Request $request, MaintenanceRequest $maintenance)
    {
        $this->authorize('approve', $maintenance);
        
        $request->validate([
            'comment' => 'required|string',
        ]);

        $maintenance->markAsDeclined();

        // Add comment
        RequestComment::create([
            'maintenance_request_id' => $maintenance->id,
            'user_id' => Auth::id(),
            'comment' => 'Request declined: ' . $request->comment,
        ]);

        return redirect()->route('maintenance.show', $maintenance)
            ->with('success', 'Maintenance request declined successfully.');
    }

    /**
     * Mark the maintenance request as in progress.
     */
    public function inProgress(MaintenanceRequest $maintenance)
    {
        $this->authorize('updateStatus', $maintenance);
        
        $maintenance->markAsStarted();

        RequestComment::create([
            'maintenance_request_id' => $maintenance->id,
            'user_id' => Auth::id(),
            'comment' => 'Work has started on this request.',
        ]);

        return redirect()->route('maintenance.show', $maintenance)
            ->with('success', 'Maintenance request marked as started.');
    }

    /**
     * Mark the maintenance request as completed.
     */
    public function complete(Request $request, MaintenanceRequest $maintenance)
    {
        $this->authorize('updateStatus', $maintenance);
        
        // Debug: Log the request data
        \Log::info('Complete request data:', [
            'request_data' => $request->all(),
            'maintenance_id' => $maintenance->id,
            'maintenance_status' => $maintenance->status,
            'user_id' => Auth::id(),
            'assigned_to' => $maintenance->assigned_to
        ]);
        
        $request->validate([
            'comment' => 'required|string',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $maintenance->markAsCompleted();

        // Add comment
        $comment = RequestComment::create([
            'maintenance_request_id' => $maintenance->id,
            'user_id' => Auth::id(),
            'comment' => 'Request completed: ' . $request->comment,
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('maintenance_requests', 'public');
                
                RequestImage::create([
                    'maintenance_request_id' => $maintenance->id,
                    'comment_id' => $comment->id,
                    'image_path' => $path,
                    'type' => 'completion',
                ]);
            }
        }

        // Debug: Log the updated maintenance request
        \Log::info('Maintenance request completed:', [
            'maintenance_id' => $maintenance->id,
            'status' => $maintenance->status,
            'completed_at' => $maintenance->completed_at
        ]);

        // TODO: Send notification to property manager and requester

        return redirect()->route('maintenance.show', $maintenance)
            ->with('success', 'Maintenance request marked as completed.');
    }

    /**
     * Add a comment to the maintenance request.
     */
    public function addComment(Request $request, MaintenanceRequest $maintenance)
    {
        $this->authorize('view', $maintenance);
        
        $request->validate([
            'comment' => 'required|string',
            'images.*' => 'nullable|image|max:2048',
        ]);

        // Add comment
        $comment = RequestComment::create([
            'maintenance_request_id' => $maintenance->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('maintenance_requests', 'public');
                
                RequestImage::create([
                    'maintenance_request_id' => $maintenance->id,
                    'comment_id' => $comment->id,
                    'image_path' => $path,
                    'type' => 'progress',
                ]);
            }
        }

        return redirect()->route('maintenance.show', $maintenance)
            ->with('success', 'Comment added successfully.');
    }

    /**
     * Assign a technician to the maintenance request.
     */
    public function assign(Request $request, MaintenanceRequest $maintenance)
    {
        $this->authorize('assign', $maintenance);
        
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $technician = User::findOrFail($request->assigned_to);
        $maintenance->assignTo($technician);
        
        // Mark the request as assigned when a technician is assigned
        $maintenance->markAsAssigned();

        // Add comment
        RequestComment::create([
            'maintenance_request_id' => $maintenance->id,
            'user_id' => Auth::id(),
            'comment' => 'Request assigned to ' . $technician->name . '.',
        ]);

        return redirect()->route('maintenance.show', $maintenance)
            ->with('success', 'Maintenance request assigned successfully.');
    }

    /**
     * Accept the maintenance request by the assigned technician.
     */
    public function accept(MaintenanceRequest $maintenance)
    {
        $this->authorize('accept', $maintenance);
        
        // Update status to acknowledged
        $maintenance->markAsAcknowledged();
        
        RequestComment::create([
            'maintenance_request_id' => $maintenance->id,
            'user_id' => Auth::id(),
            'comment' => 'Task accepted by technician.',
        ]);

        return redirect()->route('maintenance.show', $maintenance)
            ->with('success', 'Task accepted successfully.');
    }

    /**
     * Reject the maintenance request by the assigned technician.
     */
    public function reject(Request $request, MaintenanceRequest $maintenance)
    {
        $this->authorize('reject', $maintenance);
        
        $request->validate([
            'comment' => 'nullable|string',
        ]);

        // Set status back to accepted and remove technician
        $maintenance->update([
            'status' => 'accepted',
            'assigned_to' => null
        ]);

        // Add comment
        RequestComment::create([
            'maintenance_request_id' => $maintenance->id,
            'user_id' => Auth::id(),
            'comment' => 'Task rejected by technician.' . ($request->comment ? ' Reason: ' . $request->comment : ''),
        ]);

        return redirect()->route('maintenance.show', $maintenance)
            ->with('success', 'Task has been rejected and returned to manager.');
    }

    /**
     * Delete an image from the maintenance request.
     */
    public function deleteImage(RequestImage $image)
    {
        $this->authorize('deleteImage', $image->maintenanceRequest);
        
        Storage::delete('public/' . $image->image_path);
        $image->delete();

        return back()->with('success', 'Image deleted successfully.');
    }

    /**
     * Delete a comment from the maintenance request.
     */
    public function deleteComment(RequestComment $comment)
    {
        $this->authorize('view', $comment->maintenanceRequest);
        
        // Only allow users to delete their own comments
        if ($comment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return back()->with('error', 'You do not have permission to delete this comment.');
        }
        
        $comment->delete();

        return back()->with('success', 'Comment deleted successfully.');
    }

    public function close(MaintenanceRequest $maintenance)
    {
        $this->authorize('close', $maintenance);
        
        $maintenance->update(['status' => 'closed']);

        RequestComment::create([
            'maintenance_request_id' => $maintenance->id,
            'user_id' => Auth::id(),
            'comment' => 'Task closed by manager.',
        ]);

        return redirect()->route('maintenance.show', $maintenance)
            ->with('success', 'Task closed successfully.');
    }

    /**
     * Start working on the maintenance request.
     */
    public function startTask(MaintenanceRequest $maintenance)
    {
        $this->authorize('start', $maintenance);

        $maintenance->markAsStarted();
        RequestComment::create([
            'maintenance_request_id' => $maintenance->id,
            'user_id' => Auth::id(),
            'comment' => 'Task started by technician.',
        ]);

        return redirect()->back()->with('success', 'Task has been started.');
    }

    public function finishTask(MaintenanceRequest $maintenance)
    {
        $this->authorize('finish', $maintenance);

        $maintenance->markAsCompleted();
        RequestComment::create([
            'maintenance_request_id' => $maintenance->id,
            'user_id' => Auth::id(),
            'comment' => 'Task completed by technician.',
        ]);

        return redirect()->back()->with('success', 'Task has been completed.');
    }
} 