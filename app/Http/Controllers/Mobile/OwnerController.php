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
        $owners = $user->managedOwners()->latest()->paginate(10);
        
        // Get additional stats for the navigation grid
        $propertiesCount = $user->managedProperties()->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { 
            $q->where('slug', 'technician'); 
        })->where('invited_by', $user->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', $user->managedProperties()->pluck('id'))->count();
        
        return view('mobile.owners.index', compact('owners', 'propertiesCount', 'techniciansCount', 'requestsCount'));
    }

    /**
     * Show the form for creating a new owner.
     */
    public function create()
    {
        return view('mobile.owners.create');
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

        Auth::user()->managedOwners()->create($request->all());

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
        
        return view('mobile.owners.show', compact('owner', 'properties'));
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
        
        $owner->delete();

        return redirect('/m/ao')->with('success', 'Owner deleted successfully.');
    }
}
