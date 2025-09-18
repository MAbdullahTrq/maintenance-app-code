<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceRequest;
use App\Models\RequestEmailUpdate;
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
        
        // Get all checklists from the workspace (manager + team members)
        $workspaceUserIds = [$workspaceOwner->id];
        $workspaceUserIds = array_merge($workspaceUserIds, $workspaceOwner->teamMembers()->pluck('users.id')->toArray());
        $checklists = \App\Models\Checklist::whereIn('manager_id', $workspaceUserIds)->get();
        
        // Calculate all stats for mobile layout
        $ownersCount = $workspaceOwner->managedOwners()->count();
        $propertiesCount = $properties->count();
        $techniciansCount = $workspaceOwner->technicians()->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', $properties->pluck('id'))->count();
        $teamMembersCount = $workspaceOwner->teamMembers()->count();
        
        // Get editor team members for email updates (only for managers)
        $editorTeamMembers = null;
        if ($user->isPropertyManager()) {
            $editorTeamMembers = $workspaceOwner->teamMembers()
                ->whereHas('role', function ($query) {
                    $query->where('slug', 'editor');
                })
                ->get();
        }
        
        return view('mobile.request_create', compact('properties', 'checklists', 'ownersCount', 'propertiesCount', 'techniciansCount', 'requestsCount', 'teamMembersCount', 'editorTeamMembers'));
    }

    /**
     * Store a newly created maintenance request
     */
    public function store(Request $request)
    {
        $request->validate([
            'priority' => 'required|in:low,medium,high',
            'property_id' => 'required|exists:properties,id',
            'checklist_id' => 'nullable|exists:checklists,id',
            'title' => $request->checklist_id ? 'nullable|string|max:255' : 'required|string|max:255',
            'description' => $request->checklist_id ? 'nullable|string' : 'required|string',
            'location' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|max:10240', // Allow up to 10MB per image, will be resized
            'email_updates' => 'nullable|array',
            'email_updates.*' => 'exists:users,id',
        ]);

        $user = auth()->user();
        // For team members, use the workspace owner's ID
        $managerId = $user->isTeamMember() ? $user->getWorkspaceOwner()->id : $user->id;

        // Check if user can create request for this property
        $property = \App\Models\Property::findOrFail($request->property_id);
        if ($property->manager_id !== $managerId) {
            abort(403, 'Unauthorized to create request for this property');
        }

        // Determine title, description, and location based on checklist selection
        if ($request->checklist_id) {
            // Use checklist data for title and description, but location from form
            $checklist = \App\Models\Checklist::find($request->checklist_id);
            $title = $checklist->name;
            $description = $checklist->generateFormattedDescription();
            $location = $request->location;
        } else {
            // Use manual form data
            $title = $request->title;
            $description = $request->description;
            $location = $request->location;
        }

        $maintenanceRequest = MaintenanceRequest::create([
            'title' => $title,
            'description' => $description,
            'location' => $location,
            'priority' => $request->priority,
            'property_id' => $request->property_id,
            'checklist_id' => $request->checklist_id,
            'requester_name' => null, // Manager creating request, not a requester
            'requester_email' => null, // Manager creating request, not a requester
            'requester_phone' => null, // Manager creating request, not a requester
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

        // Handle email updates (only for managers)
        if ($user->isPropertyManager() && $request->has('email_updates')) {
            // Only add team members who are assigned to this property
            $assignedTeamMemberIds = $property->assignedTeamMembers()->pluck('user_id')->toArray();
            
            foreach ($request->email_updates as $userId) {
                // Only add if the team member is assigned to this property
                if (in_array($userId, $assignedTeamMemberIds)) {
                    RequestEmailUpdate::create([
                        'maintenance_request_id' => $maintenanceRequest->id,
                        'user_id' => $userId,
                    ]);
                }
            }
        } elseif ($user->hasTeamMemberRole()) {
            // For team members, automatically add the team member and manager to email updates
            $workspaceOwner = $user->getWorkspaceOwner();
            
            // Add the team member
            RequestEmailUpdate::create([
                'maintenance_request_id' => $maintenanceRequest->id,
                'user_id' => $user->id,
            ]);
            
            // Add the manager
            RequestEmailUpdate::create([
                'maintenance_request_id' => $maintenanceRequest->id,
                'user_id' => $workspaceOwner->id,
            ]);
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
        
        // Check if user can approve this request
        $user = auth()->user();
        if (!$user->can('approve', $maintenance)) {
            abort(403, 'You are not authorized to approve this request.');
        }
        
        $technician = \App\Models\User::findOrFail($request->technician_id);
        
        // Security check: Users can only assign technicians invited by their workspace owner
        $user = auth()->user();
        $managerId = $user->isPropertyManager() ? $user->id : $user->getWorkspaceOwner()->id;
        
        if ($technician->invited_by !== $managerId) {
            abort(403, 'You can only assign technicians invited by your workspace owner.');
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
            'comment' => 'required|string',
        ]);
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        
        // Check if user can decline this request
        $user = auth()->user();
        if (!$user->can('approve', $maintenance)) {
            abort(403, 'You are not authorized to decline this request.');
        }
        $maintenance->status = 'declined';
        $maintenance->save();
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => 'Request declined: ' . $request->comment,
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request declined.');
    }

    public function complete(Request $request, $id)
    {
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        
        // Check if user can complete this request
        $user = auth()->user();
        if (!$user->can('updateStatus', $maintenance)) {
            abort(403, 'You are not authorized to complete this request.');
        }
        
        // Check if all required checklist items are completed
        if ($maintenance->checklist && !$maintenance->areRequiredChecklistItemsCompleted()) {
            return redirect()->route('mobile.request.show', $id)
                ->with('error', 'Cannot mark as completed. All required checklist items must be checked first.');
        }
        
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
        
        // Check if user can close this request
        $user = auth()->user();
        if (!$user->can('close', $maintenance)) {
            abort(403, 'You are not authorized to close this request.');
        }
        $maintenance->status = 'closed';
        $maintenance->save();
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => 'Request closed by manager.',
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request closed.');
    }

    public function reopen(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);
        
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        
        // Check if user can reopen this request
        $user = auth()->user();
        if (!$user->can('reopen', $maintenance)) {
            abort(403, 'You are not authorized to reopen this request.');
        }
        
        $maintenance->reopen();
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => 'Request reopened: ' . $request->comment,
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request reopened successfully.');
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
        
        // Check if all required checklist items are completed
        if ($maintenance->checklist && !$maintenance->areRequiredChecklistItemsCompleted()) {
            return redirect()->route('mobile.request.show', $id)
                ->with('error', 'Cannot mark as completed. All required checklist items must be checked first.');
        }
        
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

    public function destroy($id)
    {
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        
        // Check authorization
        $this->authorize('delete', $maintenance);
        
        // Delete all images
        foreach ($maintenance->images as $image) {
            \Illuminate\Support\Facades\Storage::delete('public/' . $image->image_path);
        }
        
        $maintenance->delete();

        return redirect()->route('mobile.manager.all-requests')
            ->with('success', 'Maintenance request deleted successfully.');
    }
}
