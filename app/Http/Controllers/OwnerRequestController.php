<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Owner;
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
    public function showRequestForm($ownerId)
    {
        $owner = Owner::findOrFail($ownerId);
        
        // Check if owner has properties
        if ($owner->properties->count() === 0) {
            abort(404, 'No properties found for this owner.');
        }
        
        return view('owner.request-form', compact('owner'));
    }

    /**
     * Store a newly created maintenance request in storage for an owner.
     */
    public function submitRequest(Request $request, $ownerId)
    {
        $owner = Owner::findOrFail($ownerId);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'property_id' => 'required|exists:properties,id',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'images.*' => 'nullable|image|max:2048',
        ]);

        // Verify that the property belongs to this owner
        $property = $owner->properties()->findOrFail($request->property_id);

        $maintenanceRequest = MaintenanceRequest::create([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'priority' => $request->priority,
            'property_id' => $property->id,
            'requester_name' => $request->name,
            'requester_email' => $request->email,
            'requester_phone' => $request->phone,
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

        // Send notification to property manager
        Mail::to($property->manager->email)->send(new NewRequestNotification($maintenanceRequest));

        return redirect()->route('owner.request.success', $ownerId);
    }

    /**
     * Show the success page after submitting a maintenance request.
     */
    public function showSuccessPage($ownerId)
    {
        $owner = Owner::findOrFail($ownerId);
        
        return view('owner.request-success', compact('owner'));
    }

    /**
     * Show the status of a maintenance request.
     */
    public function showRequestStatus($ownerId, $requestId)
    {
        $owner = Owner::findOrFail($ownerId);
        $maintenanceRequest = MaintenanceRequest::where('id', $requestId)
            ->whereHas('property', function ($query) use ($owner) {
                $query->where('owner_id', $owner->id);
            })
            ->firstOrFail();
        
        return view('owner.request-status', compact('owner', 'maintenanceRequest'));
    }

    /**
     * Show owner information page (for QR code).
     */
    public function showOwnerInfo($ownerId)
    {
        $owner = Owner::findOrFail($ownerId);
        
        return view('owner.info', compact('owner'));
    }
} 