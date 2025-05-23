<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $properties = Property::where('manager_id', $user->id)->get();
        return view('mobile.properties', ['properties' => $properties]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'special_instructions' => 'nullable|string|max:255',
        ]);
        $property = new Property();
        $property->name = $request->name;
        $property->address = $request->address;
        $property->special_instructions = $request->special_instructions;
        $property->manager_id = $user->id;
        $property->save();
        return redirect()->route('mobile.properties.index')->with('success', 'Property added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'special_instructions' => 'nullable|string|max:255',
        ]);
        $property = Property::findOrFail($id);
        $property->name = $request->name;
        $property->address = $request->address;
        $property->special_instructions = $request->special_instructions;
        $property->save();
        return redirect()->route('mobile.properties.index')->with('success', 'Property updated successfully.');
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();
        return redirect()->route('mobile.properties.index')->with('success', 'Property deleted successfully.');
    }
}
