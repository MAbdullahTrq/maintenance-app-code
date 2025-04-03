<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PropertyController extends Controller
{
    /**
     * Display a listing of the properties.
     */
    public function index()
    {
        $properties = Auth::user()->managedProperties()->latest()->paginate(10);
        
        return view('properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new property.
     */
    public function create()
    {
        return view('properties.create');
    }

    /**
     * Store a newly created property in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $property = Auth::user()->managedProperties()->create([
            'name' => $request->name,
            'address' => $request->address,
        ]);

        // Generate QR code
        $this->generateQrCode($property);

        // Check if the request is coming from admin routes
        $isAdminRoute = str_starts_with($request->route()->getName(), 'admin.');
        $redirectRoute = $isAdminRoute ? 'admin.properties.index' : 'properties.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Property created successfully.');
    }

    /**
     * Display the specified property.
     */
    public function show(Property $property)
    {
        $this->authorize('view', $property);
        
        return view('properties.show', compact('property'));
    }

    /**
     * Show the form for editing the specified property.
     */
    public function edit(Property $property)
    {
        $this->authorize('update', $property);
        
        return view('properties.edit', compact('property'));
    }

    /**
     * Update the specified property in storage.
     */
    public function update(Request $request, Property $property)
    {
        $this->authorize('update', $property);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $property->update([
            'name' => $request->name,
            'address' => $request->address,
        ]);

        return redirect()->route('properties.index')
            ->with('success', 'Property updated successfully.');
    }

    /**
     * Remove the specified property from storage.
     */
    public function destroy(Property $property)
    {
        $this->authorize('delete', $property);
        
        // Delete QR code if exists
        if ($property->qr_code) {
            Storage::delete('public/' . $property->qr_code);
        }
        
        $property->delete();

        return redirect()->route('properties.index')
            ->with('success', 'Property deleted successfully.');
    }

    /**
     * Generate QR code for the property.
     */
    private function generateQrCode(Property $property)
    {
        $url = $property->getRequestUrl();
        
        // Create qrcodes directory if it doesn't exist
        $qrCodeDirectory = storage_path('app/public/qrcodes');
        if (!file_exists($qrCodeDirectory)) {
            mkdir($qrCodeDirectory, 0755, true);
        }
        
        $qrCodePath = 'qrcodes/property_' . $property->id . '.svg';
        $fullPath = storage_path('app/public/' . $qrCodePath);
        
        // Generate QR code with property information
        QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->margin(1)
            ->generate($url, $fullPath);
        
        // Update property with QR code path
        $property->update([
            'qr_code' => $qrCodePath
        ]);
    }

    /**
     * Download QR code for the property.
     */
    public function downloadQrCode(Property $property)
    {
        $this->authorize('view', $property);
        
        if (!$property->qr_code || !Storage::exists('public/' . $property->qr_code)) {
            $this->generateQrCode($property);
        }
        
        return response()->download(
            storage_path('app/public/' . $property->qr_code),
            $property->name . '_qrcode.svg',
            ['Content-Type' => 'image/svg+xml']
        );
    }
} 