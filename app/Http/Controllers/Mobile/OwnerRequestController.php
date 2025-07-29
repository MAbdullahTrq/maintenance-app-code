<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\RequestImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewRequestNotification;

class OwnerRequestController extends Controller
{
    /**
     * Show the form for creating a new maintenance request for an owner.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        
        // For team members, get the workspace owner's data
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        // Get owner_id from query parameter
        $ownerId = $request->query('owner_id');
        
        // Get properties based on owner_id if provided
        if ($ownerId) {
            // Verify the owner belongs to the workspace owner
            $owner = \App\Models\Owner::where('id', $ownerId)
                ->where('manager_id', $workspaceOwner->id)
                ->firstOrFail();
            
            // Get properties for this specific owner
            $properties = $workspaceOwner->managedProperties()
                ->where('owner_id', $ownerId)
                ->orderBy('name', 'asc')
                ->get();
        } else {
            // Get all properties managed by the workspace owner
            $properties = $workspaceOwner->managedProperties()
                ->orderBy('name', 'asc')
                ->get();
        }
        
        // Get stats for the mobile layout
        $propertiesCount = $workspaceOwner->managedProperties()->count();
        $ownersCount = $workspaceOwner->managedOwners()->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { 
            $q->where('slug', 'technician'); 
        })->where('invited_by', $workspaceOwner->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', $workspaceOwner->managedProperties()->pluck('id'))->count();
        
        return view('mobile.owner_request_create', compact('properties', 'propertiesCount', 'ownersCount', 'techniciansCount', 'requestsCount', 'ownerId'));
    }

    /**
     * Store a newly created maintenance request in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'images.*' => 'nullable|image|max:2048',
        ]);

        // Verify that the property belongs to the workspace owner
        $property = Property::where('id', $request->property_id)
            ->where('manager_id', $workspaceOwner->id)
            ->firstOrFail();

        $maintenanceRequest = MaintenanceRequest::create([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'priority' => $request->priority,
            'property_id' => $property->id,
            'requester_name' => $user->name,
            'requester_email' => $user->email,
            'requester_phone' => $user->phone,
            'status' => 'pending',
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

        // Send notification to property manager
        Mail::to($property->manager->email)->send(new NewRequestNotification($maintenanceRequest));

        return redirect()->route('mobile.requests.index')
            ->with('success', 'Maintenance request submitted successfully.');
    }
} 