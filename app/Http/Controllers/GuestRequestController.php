<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\RequestImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewRequestNotification;

class GuestRequestController extends Controller
{
    /**
     * Show the form for creating a new maintenance request.
     */
    public function showRequestForm($accessLink)
    {
        $property = Property::where('access_link', $accessLink)->firstOrFail();
        
        return view('guest.request-form', compact('property'));
    }

    /**
     * Store a newly created maintenance request in storage.
     */
    public function submitRequest(Request $request, $accessLink)
    {
        $property = Property::where('access_link', $accessLink)->firstOrFail();
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'images.*' => 'nullable|image|max:2048',
        ]);

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

        return redirect()->route('guest.request.success', $accessLink);
    }

    /**
     * Show the success page after submitting a maintenance request.
     */
    public function showSuccessPage($accessLink)
    {
        $property = Property::where('access_link', $accessLink)->firstOrFail();
        
        return view('guest.request-success', compact('property'));
    }

    /**
     * Show the status of a maintenance request.
     */
    public function showRequestStatus($accessLink, $requestId)
    {
        $property = Property::where('access_link', $accessLink)->firstOrFail();
        $maintenanceRequest = MaintenanceRequest::where('id', $requestId)
            ->where('property_id', $property->id)
            ->firstOrFail();
        
        return view('guest.request-status', compact('property', 'maintenanceRequest'));
    }
} 