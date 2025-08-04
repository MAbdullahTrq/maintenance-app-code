<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceRequest;
use App\Mail\TechnicianAssignedNotification;
use App\Mail\TechnicianStartedNotification;
use App\Mail\TechnicianCompletedNotification;
use App\Mail\TechnicianCommentNotification;
use App\Mail\ManagerCommentNotification;
use App\Mail\TechnicianStartedRequesterNotification;
use App\Mail\TechnicianCompletedRequesterNotification;
use App\Mail\NewRequestNotification;
use App\Services\ImageOptimizationService;
use App\Models\RequestImage;
use Illuminate\Support\Facades\Mail;

class RequestController extends Controller
{
    protected $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Show the form for creating a new maintenance request
     */
    public function create()
    {
        $user = auth()->user();
        
        // For team members, get the workspace owner's data
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        $properties = \App\Models\Property::where('manager_id', $workspaceOwner->id)->get();
        
        // Calculate all stats for mobile layout
        $ownersCount = $workspaceOwner->managedOwners()->count();
        $propertiesCount = $properties->count();
        $techniciansCount = $workspaceOwner->technicians()->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', $properties->pluck('id'))->count();
        $teamMembersCount = $workspaceOwner->teamMembers()->count();
        
        return view('mobile.request_create', compact('properties', 'ownersCount', 'propertiesCount', 'techniciansCount', 'requestsCount', 'teamMembersCount'));
    }

    /**
     * Store a newly created maintenance request
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'property_id' => 'required|exists:properties,id',
            'checklist_id' => 'nullable|exists:checklists,id',
            'images.*' => 'nullable|image|max:10240', // Allow up to 10MB per image, will be resized
        ]);

        $user = auth()->user();
        // For team members, use the workspace owner's ID
        $managerId = $user->isTeamMember() ? $user->getWorkspaceOwner()->id : $user->id;

        // Check if user can create request for this property
        $property = \App\Models\Property::findOrFail($request->property_id);
        if ($property->manager_id !== $managerId) {
            abort(403, 'Unauthorized to create request for this property');
        }

        // If checklist is selected, enhance the description
        $description = $request->description;
        if ($request->checklist_id) {
            $checklist = \App\Models\Checklist::find($request->checklist_id);
            if ($checklist) {
                $description = $checklist->generateFormattedDescription();
            }
        }

        $maintenanceRequest = MaintenanceRequest::create([
            'title' => $request->title,
            'description' => $description,
            'location' => $request->location,
            'priority' => $request->priority,
            'property_id' => $request->property_id,
            'checklist_id' => $request->checklist_id,
            'requester_name' => $user->name,
            'requester_email' => $user->email,
            'requester_phone' => $user->phone,
            'status' => 'pending',
        ]);

        // Handle image uploads with resizing
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Use the image optimization service to resize and optimize
                $optimizedPath = $this->imageService->optimizeAndResize(
                    $image,
                    'maintenance_requests',
                    800, // width
                    600  // height
                );
                
                RequestImage::create([
                    'maintenance_request_id' => $maintenanceRequest->id,
                    'image_path' => $optimizedPath,
                    'type' => 'request',
                ]);
            }
        }

        // Send notification to property manager
        $propertyManager = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        Mail::to($propertyManager->email)->send(new NewRequestNotification($maintenanceRequest));

        return redirect()->route('mobile.manager.dashboard')
            ->with('success', 'Maintenance request created successfully.');
    }

    public function show($id)
    {
        $request = MaintenanceRequest::with(['property', 'assignedTechnician', 'images'])->findOrFail($id);
        $user = auth()->user();
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        $propertiesCount = \App\Models\Property::where('manager_id', $workspaceOwner->id)->count();
        $ownersCount = $workspaceOwner->managedOwners()->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { 
            $q->where('slug', 'technician'); 
        })->where('invited_by', $workspaceOwner->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', \App\Models\Property::where('manager_id', $workspaceOwner->id)->pluck('id'))->count();
        $teamMembersCount = $workspaceOwner->teamMembers()->count();
        
        return view('mobile.request', [
            'request' => $request,
            'propertiesCount' => $propertiesCount,
            'ownersCount' => $ownersCount,
            'techniciansCount' => $techniciansCount,
            'requestsCount' => $requestsCount,
            'teamMembersCount' => $teamMembersCount,
        ]);
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'technician_id' => 'required|exists:users,id',
        ]);
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        $technician = \App\Models\User::findOrFail($request->technician_id);
        
        // Security check: Property managers can only assign technicians they invited
        if (auth()->user()->isPropertyManager() && $technician->invited_by !== auth()->id()) {
            abort(403, 'You can only assign technicians you have invited.');
        }
        $maintenance->assigned_to = $technician->id;
        $maintenance->status = 'assigned';
        $maintenance->approved_by = auth()->id();
        $maintenance->save();
        // Send email notification to technician
        Mail::to($technician->email)->send(new TechnicianAssignedNotification($maintenance));
        
        // Send SMS notification to technician
        $sms_service = app(\App\Services\SmsService::class);
        $sms_service->sendTechnicianAssignmentNotification($maintenance, $technician);
        
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => 'Request approved and assigned to ' . $technician->name . '.',
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request approved and technician assigned.');
    }

    public function decline(Request $request, $id)
    {
        $request->validate([
            'decline_reason' => 'required|string',
        ]);
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'declined';
        $maintenance->save();
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => 'Request declined: ' . $request->decline_reason,
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request declined.');
    }

    public function complete(Request $request, $id)
    {
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'completed';
        $maintenance->completed_at = now();
        $maintenance->save();
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => 'Request marked as completed by manager.',
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request marked as completed.');
    }

    public function close(Request $request, $id)
    {
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'closed';
        $maintenance->save();
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => 'Request closed by manager.',
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request closed.');
    }

    public function accept(Request $request, $id)
    {
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'accepted';
        $maintenance->save();
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => 'Request accepted by technician.',
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request accepted.');
    }

    public function start(Request $request, $id)
    {
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'started';
        $maintenance->started_at = now();
        $maintenance->save();
        // Send notifications
        Mail::to($maintenance->property->manager->email)
            ->send(new TechnicianStartedNotification($maintenance));

        if ($maintenance->requester_email) {
            Mail::to($maintenance->requester_email)
                ->send(new TechnicianStartedRequesterNotification($maintenance));
        }
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => 'Request started by technician.',
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request started.');
    }

    public function finish(Request $request, $id)
    {
        $request->validate([
            'completion_comment' => 'nullable|string',
        ]);
        
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'completed';
        $maintenance->completed_at = now();
        $maintenance->save();
        
        // Send notifications
        Mail::to($maintenance->property->manager->email)
            ->send(new TechnicianCompletedNotification($maintenance));

        if ($maintenance->requester_email) {
            Mail::to($maintenance->requester_email)
                ->send(new TechnicianCompletedRequesterNotification($maintenance));
        }
        
        // Add automatic completion comment
        $commentText = $request->completion_comment 
            ? 'Request completed by technician: ' . $request->completion_comment
            : 'Request completed by technician.';
            
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $commentText,
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request completed.');
    }

    public function comment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,webm|max:51200', // 50MB max
        ]);
        
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        $comment = $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        // Handle media uploads
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $media) {
                $mediaType = $media->getMimeType();
                $isImage = strpos($mediaType, 'image/') === 0;
                $isVideo = strpos($mediaType, 'video/') === 0;
                
                if ($isImage) {
                    // Optimize images using the existing service
                    $optimizedPath = $this->imageService->optimizeAndResize(
                        $media,
                        'maintenance_requests/comments',
                        800, // width
                        600  // height
                    );
                    
                    RequestImage::create([
                        'maintenance_request_id' => $maintenance->id,
                        'comment_id' => $comment->id,
                        'image_path' => $optimizedPath,
                        'type' => 'comment_image',
                    ]);
                } elseif ($isVideo) {
                    // Store videos directly without optimization
                    $path = $media->store('maintenance_requests/comments/videos', 'public');
                    
                    RequestImage::create([
                        'maintenance_request_id' => $maintenance->id,
                        'comment_id' => $comment->id,
                        'image_path' => $path,
                        'type' => 'comment_video',
                    ]);
                }
            }
        }

        // Send notifications based on who is commenting
        if (auth()->user()->isTechnician()) {
            // Technician commenting - notify manager only (removed requester notification)
            Mail::to($maintenance->property->manager->email)
                ->send(new TechnicianCommentNotification($maintenance, $comment));
        } elseif (auth()->user()->isPropertyManager() || auth()->user()->isAdmin()) {
            // Manager commenting - notify technician
            if ($maintenance->assignedTechnician) {
                Mail::to($maintenance->assignedTechnician->email)
                    ->send(new ManagerCommentNotification($maintenance, $comment));
            }
        }

        return redirect()->route('mobile.request.show', $id)->with('success', 'Comment added.');
    }
}
