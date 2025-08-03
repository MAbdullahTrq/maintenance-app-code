<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
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

        return view('checklists.index', compact('checklists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('checklists.create');
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

        return redirect()->route('checklists.edit', $checklist)
            ->with('success', 'Checklist created successfully. Now add your checklist items.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Checklist $checklist)
    {
        $this->authorize('view', $checklist);

        $checklist->load('items');

        return view('checklists.show', compact('checklist'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Checklist $checklist)
    {
        $this->authorize('update', $checklist);

        $checklist->load('items');

        return view('checklists.edit', compact('checklist'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Checklist $checklist)
    {
        $this->authorize('update', $checklist);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $checklist->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('checklists.edit', $checklist)
            ->with('success', 'Checklist updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Checklist $checklist)
    {
        $this->authorize('delete', $checklist);

        // Check if checklist is being used in any maintenance requests
        if ($checklist->maintenanceRequests()->count() > 0) {
            return redirect()->back()->with('error', 
                'Cannot delete checklist that is being used in maintenance requests. Please reassign or complete those requests first.'
            );
        }

        $checklist->delete();

        return redirect()->route('checklists.index')
            ->with('success', 'Checklist deleted successfully.');
    }
}
