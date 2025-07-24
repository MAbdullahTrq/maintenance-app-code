<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller
{
    /**
     * Display a listing of the owners.
     */
    public function index()
    {
        $user = Auth::user();
        
        // For team members, get the workspace owner's data
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        $owners = $workspaceOwner->managedOwners()->latest()->paginate(10);
        
        // Get additional stats for the navigation grid
        $propertiesCount = $workspaceOwner->managedProperties()->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { 
            $q->where('slug', 'technician'); 
        })->where('invited_by', $workspaceOwner->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', $workspaceOwner->managedProperties()->pluck('id'))->count();
        
        // Get team members count (excluding technicians)
        $teamMembersCount = \App\Models\User::where('invited_by', $workspaceOwner->id)
            ->whereHas('role', function ($query) {
                $query->whereIn('slug', ['editor', 'viewer']);
            })
            ->count();
        
        return view('mobile.owners.index', compact('owners', 'propertiesCount', 'techniciansCount', 'requestsCount', 'teamMembersCount'));
    }

    /**
     * Show the form for creating a new owner.
     */
    public function create()
    {
        $user = Auth::user();
        
        // For team members, get the workspace owner's data
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        // Calculate stats for mobile layout
        $ownersCount = $workspaceOwner->managedOwners()->count();
        $propertiesCount = $workspaceOwner->managedProperties()->count();
        $techniciansCount = $workspaceOwner->technicians()->count();
        $requestsCount = $workspaceOwner->managedMaintenanceRequests()->count();
        $teamMembersCount = $workspaceOwner->teamMembers()->count();
        
        return view('mobile.owners.create', compact('ownersCount', 'propertiesCount', 'techniciansCount', 'requestsCount', 'teamMembersCount'));
    }

    /**
     * Store a newly created owner in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:owners,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        // For team members, use the workspace owner's ID
        $managerId = $user->isTeamMember() ? $user->getWorkspaceOwner()->id : $user->id;
        
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        $workspaceOwner->managedOwners()->create($request->all());

        return redirect('/m/ao')->with('success', 'Owner created successfully.');
    }

    /**
     * Display the specified owner.
     */
    public function show($id)
    {
        $owner = Owner::findOrFail($id);
        $this->authorize('view', $owner);
        
        $properties = $owner->properties()->latest()->paginate(10);
        
        // Get additional stats for the navigation grid
        $user = Auth::user();
        
        // For team members, get the workspace owner's data
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        $propertiesCount = $workspaceOwner->managedProperties()->count();
        $ownersCount = $workspaceOwner->managedOwners()->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { 
            $q->where('slug', 'technician'); 
        })->where('invited_by', $workspaceOwner->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', $workspaceOwner->managedProperties()->pluck('id'))->count();
        
        return view('mobile.owners.show', compact('owner', 'properties', 'propertiesCount', 'ownersCount', 'techniciansCount', 'requestsCount'));
    }

    /**
     * Show the form for editing the specified owner.
     */
    public function edit($id)
    {
        $owner = Owner::findOrFail($id);
        $this->authorize('update', $owner);
        
        return view('mobile.owners.edit', compact('owner'));
    }

    /**
     * Update the specified owner in storage.
     */
    public function update(Request $request, $id)
    {
        $owner = Owner::findOrFail($id);
        $this->authorize('update', $owner);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:owners,email,' . $owner->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $owner->update($request->all());

        return redirect('/m/ao')->with('success', 'Owner updated successfully.');
    }

    /**
     * Remove the specified owner from storage.
     */
    public function destroy($id)
    {
        $owner = Owner::findOrFail($id);
        $this->authorize('delete', $owner);
        
        // Check if owner has any properties
        $properties = $owner->properties;
        
        if ($properties->count() > 0) {
            $propertyNames = $properties->pluck('name')->implode(', ');
            
            return redirect()->back()->with('error', 
                'This owner cannot be deleted because the following properties are owned by them: ' . $propertyNames
            );
        }
        
        $owner->delete();

        return redirect('/m/ao')->with('success', 'Owner deleted successfully.');
    }
}
