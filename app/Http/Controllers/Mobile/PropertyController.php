<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    protected $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // For team members, get the workspace owner's data
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        // Get all properties with owner relationship
        $propertiesQuery = $workspaceOwner->managedProperties()->with('owner');
        
        // Apply owner filter if selected
        if ($request->filled('owner_id')) {
            $propertiesQuery->where('owner_id', $request->owner_id);
        }
        
        // Sort properties alphabetically by name
        $properties = $propertiesQuery->orderBy('name', 'asc')->get();
        
        // Get all owners for the filter dropdown
        $owners = $workspaceOwner->managedOwners()->orderBy('name', 'asc')->get();
        
        $ownersCount = $workspaceOwner->managedOwners()->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $workspaceOwner->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', $properties->pluck('id'))->count();
        
        // Get team members count (excluding technicians)
        $teamMembersCount = \App\Models\User::where('invited_by', $workspaceOwner->id)
            ->whereHas('role', function ($query) {
                $query->whereIn('slug', ['editor', 'viewer']);
            })
            ->count();
        
        return view('mobile.properties', [
            'properties' => $properties,
            'owners' => $owners,
            'selectedOwnerId' => $request->owner_id,
            'propertiesCount' => $properties->count(),
            'ownersCount' => $ownersCount,
            'techniciansCount' => $techniciansCount,
            'requestsCount' => $requestsCount,
            'teamMembersCount' => $teamMembersCount,
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        
        // For team members, get the workspace owner's data
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        $owners = $workspaceOwner->managedOwners()->get();
        $propertiesCount = $workspaceOwner->managedProperties()->count();
        $ownersCount = $owners->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $workspaceOwner->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', $workspaceOwner->managedProperties()->pluck('id'))->count();
        
        // Get team members count (excluding technicians)
        $teamMembersCount = \App\Models\User::where('invited_by', $workspaceOwner->id)
            ->whereHas('role', function ($query) {
                $query->whereIn('slug', ['editor', 'viewer']);
            })
            ->count();
        
        return view('mobile.property_create', compact('owners', 'propertiesCount', 'ownersCount', 'techniciansCount', 'requestsCount', 'teamMembersCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'owner_id' => 'required|exists:owners,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);
        
        $user = auth()->user();
        // For team members, use the workspace owner's ID
        $managerId = $user->isTeamMember() ? $user->getWorkspaceOwner()->id : $user->id;
        
        $property = new \App\Models\Property();
        $property->name = $request->name;
        $property->address = $request->address;
        $property->owner_id = $request->owner_id;
        $property->special_instructions = $request->special_instructions;
        $property->manager_id = $managerId;
        
        if ($request->hasFile('image')) {
            $property->image = $this->imageService->optimizeAndResize(
                $request->file('image'),
                'property-images',
                800,
                600
            );
        }
        
        $property->save();
        return redirect()->route('mobile.properties.index')->with('success', 'Property added!');
    }

    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'owner_id' => 'required|exists:owners,id',
            'special_instructions' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $property->name = $request->name;
        $property->address = $request->address;
        $property->owner_id = $request->owner_id;
        $property->special_instructions = $request->special_instructions;
        
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($property->image) {
                Storage::delete('public/' . $property->image);
            }
            
            // Optimize and store new image
            $property->image = $this->imageService->optimizeAndResize(
                $request->file('image'),
                'property-images'
            );
        }
        
        $property->save();
        return redirect()->route('mobile.properties.show', $property->id)
            ->with('success', 'Property updated successfully.');
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();
        return redirect()->route('mobile.properties.index')->with('success', 'Property deleted successfully.');
    }

    public function show($id)
    {
        $property = Property::with('maintenanceRequests', 'owner')->findOrFail($id);
        $user = auth()->user();
        
        // For team members, get the workspace owner's data
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        $propertiesCount = $workspaceOwner->managedProperties()->count();
        $ownersCount = $workspaceOwner->managedOwners()->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $workspaceOwner->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', $workspaceOwner->managedProperties()->pluck('id'))->count();
        $owners = $workspaceOwner->managedOwners()->get();
        
        return view('mobile.property_show', [
            'property' => $property,
            'propertiesCount' => $propertiesCount,
            'ownersCount' => $ownersCount,
            'techniciansCount' => $techniciansCount,
            'requestsCount' => $requestsCount,
            'owners' => $owners,
        ]);
    }

    public function edit($id)
    {
        $property = Property::findOrFail($id);
        $user = auth()->user();
        
        // For team members, get the workspace owner's data
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        $owners = $workspaceOwner->managedOwners()->get();
        $propertiesCount = $workspaceOwner->managedProperties()->count();
        $ownersCount = $owners->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $workspaceOwner->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', $workspaceOwner->managedProperties()->pluck('id'))->count();
        
        return view('mobile.edit_property', [
            'property' => $property,
            'owners' => $owners,
            'propertiesCount' => $propertiesCount,
            'ownersCount' => $ownersCount,
            'techniciansCount' => $techniciansCount,
            'requestsCount' => $requestsCount,
        ]);
    }

    public function qrcode($id)
    {
        try {
            $property = Property::findOrFail($id);
            
            // Generate QR code if not exists
            if (!$property->qr_code) {
                $property->generateQrCode();
                $property->refresh(); // Refresh to get the updated qr_code
            }
            
            // Check if the QR code file exists
            $qrCodePath = storage_path('app/public/' . $property->qr_code);
            if (!file_exists($qrCodePath)) {
                // Regenerate QR code if file doesn't exist
                $property->generateQrCode();
                $property->refresh();
                $qrCodePath = storage_path('app/public/' . $property->qr_code);
            }
            
            // Return the QR code file
            return response()->file($qrCodePath);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('QR Code generation failed for property ' . $id . ': ' . $e->getMessage());
            
            // Return a 404 or error response
            return response()->json([
                'error' => 'QR Code could not be generated',
                'message' => 'Please try again later'
            ], 500);
        }
    }
}
