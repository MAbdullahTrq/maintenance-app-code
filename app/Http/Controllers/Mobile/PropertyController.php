<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyAssignment;
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
        
        // Get all properties with owner, assigned team members, and owner's assigned team members relationships
        $propertiesQuery = $workspaceOwner->managedProperties()->with('owner.assignedTeamMembers.user', 'assignedTeamMembers.user');
        
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
        
        // Get editor team members for assignment (only for managers)
        $editorTeamMembers = null;
        if ($user->isPropertyManager()) {
            $editorTeamMembers = $workspaceOwner->teamMembers()
                ->whereHas('role', function ($query) {
                    $query->where('slug', 'editor');
                })
                ->get();
        }
        
        return view('mobile.property_create', compact('owners', 'propertiesCount', 'ownersCount', 'techniciansCount', 'requestsCount', 'teamMembersCount', 'editorTeamMembers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'owner_id' => 'required|exists:owners,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'assigned_team_members' => 'nullable|array',
            'assigned_team_members.*' => 'exists:users,id',
        ]);
        
        $user = auth()->user();
        
        // Check if user can create a new property
        if (!$user->canCreateProperty()) {
            $limits = $user->getSubscriptionLimits();
            $currentCount = $user->getCurrentPropertyCount();
            
            return back()->withErrors([
                'limit' => "You have reached your property limit ({$currentCount}/{$limits['property_limit']}). Please upgrade your plan to add more properties."
            ])->withInput();
        }
        
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
        
        // Handle team member assignments (only for managers)
        if ($user->isPropertyManager() && $request->has('assigned_team_members')) {
            foreach ($request->assigned_team_members as $userId) {
                PropertyAssignment::create([
                    'property_id' => $property->id,
                    'user_id' => $userId,
                ]);
            }
        }
        
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
            'assigned_team_members' => 'nullable|array',
            'assigned_team_members.*' => 'exists:users,id',
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
        
        // Handle team member assignments (only for managers)
        if (Auth::user()->isPropertyManager()) {
            // Remove existing assignments
            $property->assignedTeamMembers()->delete();
            
            // Add new assignments
            if ($request->has('assigned_team_members')) {
                foreach ($request->assigned_team_members as $userId) {
                    PropertyAssignment::create([
                        'property_id' => $property->id,
                        'user_id' => $userId,
                    ]);
                }
            }
        }
        
        return redirect()->route('mobile.properties.show', $property->id)
            ->with('success', 'Property updated successfully.');
    }

    /**
     * Show the form for assigning team members to a property
     */
    public function assign($id)
    {
        $property = Property::findOrFail($id);
        $user = Auth::user();
        
        // Check if user can manage this property
        if (!$user->isPropertyManager() || $property->manager_id !== $user->id) {
            abort(403, 'Unauthorized to assign team members to this property');
        }
        
        // Get workspace owner
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        // Get editor team members
        $editorTeamMembers = $workspaceOwner->teamMembers()
            ->whereHas('role', function ($query) {
                $query->where('slug', 'editor');
            })
            ->get();
        
        // Load current assignments
        $property->load('assignedTeamMembers.user');
        
        // Get stats for mobile layout
        $propertiesCount = $workspaceOwner->managedProperties()->count();
        $ownersCount = $workspaceOwner->managedOwners()->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $workspaceOwner->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', $workspaceOwner->managedProperties()->pluck('id'))->count();
        $teamMembersCount = \App\Models\User::where('invited_by', $workspaceOwner->id)
            ->whereHas('role', function ($query) {
                $query->whereIn('slug', ['editor', 'viewer']);
            })
            ->count();
        
        return view('mobile.property_assign', compact('property', 'editorTeamMembers', 'propertiesCount', 'ownersCount', 'techniciansCount', 'requestsCount', 'teamMembersCount'));
    }

    /**
     * Update team member assignments for a property
     */
    public function updateAssignments(Request $request, $id)
    {
        $property = Property::findOrFail($id);
        $user = Auth::user();
        
        // Check if user can manage this property
        if (!$user->isPropertyManager() || $property->manager_id !== $user->id) {
            abort(403, 'Unauthorized to assign team members to this property');
        }
        
        $request->validate([
            'assigned_team_members' => 'nullable|array',
            'assigned_team_members.*' => 'exists:users,id',
        ]);
        
        // Remove existing assignments
        $property->assignedTeamMembers()->delete();
        
        // Add new assignments
        if ($request->has('assigned_team_members')) {
            foreach ($request->assigned_team_members as $userId) {
                PropertyAssignment::create([
                    'property_id' => $property->id,
                    'user_id' => $userId,
                ]);
            }
        }
        
        return redirect()->route('mobile.properties.show', $property->id)
            ->with('success', 'Team member assignments updated successfully.');
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();
        return redirect()->route('mobile.properties.index')->with('success', 'Property deleted successfully.');
    }

    public function show($id)
    {
        $property = Property::with('maintenanceRequests', 'owner', 'assignedTeamMembers.user')->findOrFail($id);
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
                \Log::info('Generating QR code for property ' . $id);
                $property->generateQrCode();
                $property->refresh(); // Refresh to get the updated qr_code
            }
            
            // Check if the QR code file exists
            $qrCodePath = storage_path('app/public/' . $property->qr_code);
            if (!file_exists($qrCodePath)) {
                \Log::info('QR code file not found, regenerating for property ' . $id);
                // Regenerate QR code if file doesn't exist
                $property->generateQrCode();
                $property->refresh();
                $qrCodePath = storage_path('app/public/' . $property->qr_code);
            }
            
            // Return the QR code file with proper content type
            $content = file_get_contents($qrCodePath);
            $extension = pathinfo($property->qr_code, PATHINFO_EXTENSION);
            
            if ($extension === 'svg') {
                return response($content, 200, [
                    'Content-Type' => 'image/svg+xml',
                    'Content-Disposition' => 'inline; filename="' . basename($property->qr_code) . '"'
                ]);
            } else {
                return response()->file($qrCodePath);
            }
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('QR Code generation failed for property ' . $id . ': ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return a 404 or error response
            return response()->json([
                'error' => 'QR Code could not be generated',
                'message' => 'Please try again later',
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
