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
        $checklists = Checklist::where('manager_id', Auth::id())
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get counts for the top bar stats
        $ownersCount = \App\Models\Owner::where('manager_id', Auth::id())->count();
        $propertiesCount = \App\Models\Property::where('manager_id', Auth::id())->count();
        $techniciansCount = \App\Models\User::where('role_id', 3)->count(); // Count all technicians
        $requestsCount = \App\Models\MaintenanceRequest::whereHas('property', function($query) {
            $query->where('manager_id', Auth::id());
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
        $checklist = Checklist::with('items')->findOrFail($id);
        $this->authorize('view', $checklist);

        // Get counts for the top bar stats
        $ownersCount = \App\Models\Owner::where('manager_id', Auth::id())->count();
        $propertiesCount = \App\Models\Property::where('manager_id', Auth::id())->count();
        $techniciansCount = \App\Models\User::where('role_id', 3)->count(); // Count all technicians
        $requestsCount = \App\Models\MaintenanceRequest::whereHas('property', function($query) {
            $query->where('manager_id', Auth::id());
        })->count();

        return view('mobile.checklists.show', compact('checklist', 'ownersCount', 'propertiesCount', 'techniciansCount', 'requestsCount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $checklist = Checklist::with('items')->findOrFail($id);
        $this->authorize('update', $checklist);

        // Get counts for the top bar stats
        $ownersCount = \App\Models\Owner::where('manager_id', Auth::id())->count();
        $propertiesCount = \App\Models\Property::where('manager_id', Auth::id())->count();
        $techniciansCount = \App\Models\User::where('role_id', 3)->count(); // Count all technicians
        $requestsCount = \App\Models\MaintenanceRequest::whereHas('property', function($query) {
            $query->where('manager_id', Auth::id());
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
