<?php

namespace App\Http\Controllers;

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
        $owners = Auth::user()->managedOwners()->latest()->paginate(10);
        
        return view('owners.index', compact('owners'));
    }

    /**
     * Show the form for creating a new owner.
     */
    public function create()
    {
        return view('owners.create');
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

        return redirect()->route('owners.index')
            ->with('success', 'Owner created successfully.');
    }

    /**
     * Display the specified owner.
     */
    public function show(Owner $owner)
    {
        $this->authorize('view', $owner);
        
        $properties = $owner->properties()->latest()->paginate(10);
        
        return view('owners.show', compact('owner', 'properties'));
    }

    /**
     * Show the form for editing the specified owner.
     */
    public function edit(Owner $owner)
    {
        $this->authorize('update', $owner);
        
        return view('owners.edit', compact('owner'));
    }

    /**
     * Update the specified owner in storage.
     */
    public function update(Request $request, Owner $owner)
    {
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

        return redirect()->route('owners.index')
            ->with('success', 'Owner updated successfully.');
    }

    /**
     * Remove the specified owner from storage.
     */
    public function destroy(Owner $owner)
    {
        $this->authorize('delete', $owner);
        
        // Check if owner has any properties
        $properties = $owner->properties;
        
        if ($properties->count() > 0) {
            $propertyNames = $properties->pluck('name')->implode(', ');
            
            return redirect()->back()->with('error', 
                'This owner cannot be deleted because the following properties are owned by them: ' . $propertyNames . 
                '. Please reassign these properties to another owner before deleting this owner.'
            );
        }
        
        $owner->delete();

        return redirect()->route('owners.index')
            ->with('success', 'Owner deleted successfully.');
    }
}
