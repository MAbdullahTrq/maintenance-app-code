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

    public function index()
    {
        $user = Auth::user();
        $properties = Property::where('manager_id', $user->id)->get();
        $ownersCount = $user->managedOwners()->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $user->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', $properties->pluck('id'))->count();
        return view('mobile.properties', [
            'properties' => $properties,
            'propertiesCount' => $properties->count(),
            'ownersCount' => $ownersCount,
            'techniciansCount' => $techniciansCount,
            'requestsCount' => $requestsCount,
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        $owners = $user->managedOwners()->get();
        $propertiesCount = \App\Models\Property::where('manager_id', $user->id)->count();
        $ownersCount = $owners->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $user->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', \App\Models\Property::where('manager_id', $user->id)->pluck('id'))->count();
        return view('mobile.property_create', compact('owners', 'propertiesCount', 'ownersCount', 'techniciansCount', 'requestsCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'owner_id' => 'required|exists:owners,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);
        $property = new \App\Models\Property();
        $property->name = $request->name;
        $property->address = $request->address;
        $property->owner_id = $request->owner_id;
        $property->special_instructions = $request->special_instructions;
        $property->manager_id = auth()->id();
        
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
        $property = Property::with('maintenanceRequests')->findOrFail($id);
        $user = auth()->user();
        $propertiesCount = \App\Models\Property::where('manager_id', $user->id)->count();
        $ownersCount = $user->managedOwners()->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $user->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', \App\Models\Property::where('manager_id', $user->id)->pluck('id'))->count();
        return view('mobile.property_show', [
            'property' => $property,
            'propertiesCount' => $propertiesCount,
            'ownersCount' => $ownersCount,
            'techniciansCount' => $techniciansCount,
            'requestsCount' => $requestsCount,
        ]);
    }

    public function edit($id)
    {
        $property = Property::findOrFail($id);
        $user = auth()->user();
        $owners = $user->managedOwners()->get();
        $propertiesCount = \App\Models\Property::where('manager_id', $user->id)->count();
        $ownersCount = $owners->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $user->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', \App\Models\Property::where('manager_id', $user->id)->pluck('id'))->count();
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
        $property = Property::findOrFail($id);
        if (!$property->qr_code) {
            // Generate QR code if not exists
            $property->generateQrCode();
        }
        return response()->file(storage_path('app/public/' . $property->qr_code));
    }
}
