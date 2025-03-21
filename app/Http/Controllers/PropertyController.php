<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        return redirect()->route('properties.index')
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
        
        $qrCodePath = 'qrcodes/property_' . $property->id . '.png';
        $fullPath = storage_path('app/public/' . $qrCodePath);
        
        // Ensure directory exists
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }
        
        // Instead of using QrCode package, create a placeholder image
        // This is a temporary solution until the QrCode package is properly installed
        $placeholderImage = imagecreate(300, 300);
        $background = imagecolorallocate($placeholderImage, 255, 255, 255);
        $textColor = imagecolorallocate($placeholderImage, 0, 0, 0);
        
        // Add text to the image
        $text = "QR Code Placeholder";
        $font = 5; // Built-in font
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);
        
        // Center the text
        $x = (300 - $textWidth) / 2;
        $y = (300 - $textHeight) / 2;
        
        imagestring($placeholderImage, $font, $x, $y, $text, $textColor);
        
        // Add URL text
        $urlText = $url;
        $urlTextWidth = imagefontwidth($font) * strlen($urlText);
        $x = (300 - $urlTextWidth) / 2;
        $y = $y + $textHeight + 10;
        
        imagestring($placeholderImage, $font, $x, $y, $urlText, $textColor);
        
        // Save the image
        imagepng($placeholderImage, $fullPath);
        imagedestroy($placeholderImage);
        
        // Update property with QR code path
        $property->update([
            'qr_code' => $qrCodePath,
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
        
        return Storage::download('public/' . $property->qr_code, $property->name . '_qrcode.png');
    }
} 