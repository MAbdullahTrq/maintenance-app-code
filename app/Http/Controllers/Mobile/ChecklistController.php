<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Checklist;
use App\Models\Owner;
use App\Models\Property;
use App\Models\User;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChecklistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // For team members, get the workspace owner's data
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        // Get all checklists from the workspace (manager + team members)
        $workspaceUserIds = [$workspaceOwner->id];
        $workspaceUserIds = array_merge($workspaceUserIds, $workspaceOwner->teamMembers()->pluck('users.id')->toArray());
        
        $checklists = Checklist::whereIn('manager_id', $workspaceUserIds)
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get counts for the top bar stats
        $ownersCount = \App\Models\Owner::where('manager_id', $workspaceOwner->id)->count();
        $propertiesCount = \App\Models\Property::where('manager_id', $workspaceOwner->id)->count();
        $techniciansCount = \App\Models\User::where('role_id', 3)->where('invited_by', $workspaceOwner->id)->count(); // Count workspace technicians
        $requestsCount = \App\Models\MaintenanceRequest::whereHas('property', function($query) use ($workspaceOwner) {
            $query->where('manager_id', $workspaceOwner->id);
        })->count();

        return view('mobile.checklists.index', compact('checklists', 'ownersCount', 'propertiesCount', 'techniciansCount', 'requestsCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('mobile.checklists.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $checklist = Checklist::create([
            'name' => $request->name,
            'description' => $request->description,
            'manager_id' => Auth::id(),
        ]);

        return redirect()->route('mobile.checklists.edit', $checklist->id)
            ->with('success', 'Checklist created successfully. Now add your checklist items.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        $checklist = Checklist::with('items')->findOrFail($id);
        $this->authorize('view', $checklist);

        // Get counts for the top bar stats
        $ownersCount = \App\Models\Owner::where('manager_id', $workspaceOwner->id)->count();
        $propertiesCount = \App\Models\Property::where('manager_id', $workspaceOwner->id)->count();
        $techniciansCount = \App\Models\User::where('role_id', 3)->where('invited_by', $workspaceOwner->id)->count(); // Count workspace technicians
        $requestsCount = \App\Models\MaintenanceRequest::whereHas('property', function($query) use ($workspaceOwner) {
            $query->where('manager_id', $workspaceOwner->id);
        })->count();

        return view('mobile.checklists.show', compact('checklist', 'ownersCount', 'propertiesCount', 'techniciansCount', 'requestsCount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        $checklist = Checklist::with('items')->findOrFail($id);
        $this->authorize('update', $checklist);

        // Get counts for the top bar stats
        $ownersCount = \App\Models\Owner::where('manager_id', $workspaceOwner->id)->count();
        $propertiesCount = \App\Models\Property::where('manager_id', $workspaceOwner->id)->count();
        $techniciansCount = \App\Models\User::where('role_id', 3)->where('invited_by', $workspaceOwner->id)->count(); // Count workspace technicians
        $requestsCount = \App\Models\MaintenanceRequest::whereHas('property', function($query) use ($workspaceOwner) {
            $query->where('manager_id', $workspaceOwner->id);
        })->count();

        return view('mobile.checklists.edit', compact('checklist', 'ownersCount', 'propertiesCount', 'techniciansCount', 'requestsCount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $checklist = Checklist::findOrFail($id);
        $this->authorize('update', $checklist);

        // Handle AJAX requests for inline editing
        if ($request->ajax()) {
            // Determine which field is being updated
            $field = null;
            $value = null;
            
            if ($request->has('name')) {
                $field = 'name';
                $value = $request->input('name');
                $request->validate([
                    'name' => 'required|string|max:255',
                ]);
            } elseif ($request->has('description')) {
                $field = 'description';
                $value = $request->input('description');
                $request->validate([
                    'description' => 'nullable|string',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No field specified for update.'
                ], 400);
            }

            $checklist->update([
                $field => $value,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Checklist updated successfully.',
                'data' => [
                    $field => $value
                ]
            ]);
        }

        // Handle regular form submissions
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $checklist->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('mobile.checklists.edit', $checklist->id)
            ->with('success', 'Checklist updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $checklist = Checklist::findOrFail($id);
        $this->authorize('delete', $checklist);

        // Check if checklist is being used in any maintenance requests
        if ($checklist->maintenanceRequests()->count() > 0) {
            return redirect()->back()->with('error', 
                'Cannot delete checklist that is being used in maintenance requests. Please reassign or complete those requests first.'
            );
        }

        $checklist->delete();

        return redirect()->route('mobile.checklists.index')
            ->with('success', 'Checklist deleted successfully.');
    }
}
