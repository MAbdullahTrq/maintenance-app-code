<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    /**
     * Display a listing of properties.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $properties = Property::orderBy('name')->get();
        
        return view('mobile.properties.index', compact('properties'));
    }
    
    /**
     * Display the specified property.
     *
     * @param  \App\Models\Property  $property
     * @return \Illuminate\View\View
     */
    public function show(Property $property)
    {
        $maintenanceRequests = MaintenanceRequest::where('property_id', $property->id)
            ->with('technician')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('mobile.properties.show', compact('property', 'maintenanceRequests'));
    }
    
    /**
     * Show the form for editing the specified property.
     *
     * @param  \App\Models\Property  $property
     * @return \Illuminate\View\View
     */
    public function edit(Property $property)
    {
        return view('mobile.properties.edit', compact('property'));
    }
    
    /**
     * Update the specified property in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'special_instructions' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $property->name = $validated['name'];
        $property->address = $validated['address'];
        $property->special_instructions = $validated['special_instructions'];
        
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($property->image && Storage::exists('public/' . $property->image)) {
                Storage::delete('public/' . $property->image);
            }
            
            // Store new image
            $imagePath = $request->file('image')->store('properties', 'public');
            $property->image = $imagePath;
        }
        
        $property->save();
        
        return redirect()->route('mobile.properties.show', $property)
            ->with('success', 'Property updated successfully');
    }
} 