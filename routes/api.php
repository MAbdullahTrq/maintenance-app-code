<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\PhoneValidationService;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Phone validation endpoint
Route::post('/validate-phone', function (Request $request) {
    $phoneService = new PhoneValidationService();
    
    $phone = $request->input('phone');
    $country = $request->input('country');
    
    if (empty($phone) || empty($country)) {
        return response()->json([
            'valid' => false,
            'message' => 'Phone number and country are required'
        ]);
    }
    
    try {
        // Convert dialing code to ISO country code
        $isoCountryCode = null;
        $countryMap = [
            '+1' => 'US', '+44' => 'GB', '+61' => 'AU', '+49' => 'DE', '+33' => 'FR',
            '+39' => 'IT', '+34' => 'ES', '+31' => 'NL', '+32' => 'BE', '+41' => 'CH',
            '+43' => 'AT', '+46' => 'SE', '+47' => 'NO', '+45' => 'DK', '+358' => 'FI',
            '+353' => 'IE', '+351' => 'PT', '+30' => 'GR', '+48' => 'PL', '+420' => 'CZ',
            '+36' => 'HU', '+421' => 'SK', '+386' => 'SI', '+385' => 'HR', '+359' => 'BG',
            '+40' => 'RO', '+370' => 'LT', '+371' => 'LV', '+372' => 'EE', '+7' => 'RU',
            '+380' => 'UA', '+375' => 'BY', '+90' => 'TR', '+972' => 'IL', '+966' => 'SA',
            '+971' => 'AE', '+91' => 'IN', '+86' => 'CN', '+81' => 'JP', '+82' => 'KR',
            '+65' => 'SG', '+60' => 'MY', '+66' => 'TH', '+63' => 'PH', '+62' => 'ID',
            '+84' => 'VN', '+55' => 'BR', '+52' => 'MX', '+54' => 'AR', '+56' => 'CL',
            '+57' => 'CO', '+51' => 'PE', '+27' => 'ZA', '+20' => 'EG', '+234' => 'NG',
            '+254' => 'KE', '+233' => 'GH', '+212' => 'MA', '+216' => 'TN', '+213' => 'DZ',
            '+92' => 'PK', '+880' => 'BD', '+94' => 'LK', '+64' => 'NZ',
        ];
        
        $isoCountryCode = $countryMap[$country] ?? null;
        
        if (!$isoCountryCode) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid country code'
            ]);
        }
        
        $isValid = $phoneService->isValidPhoneNumber($phone, $isoCountryCode);
        
        if ($isValid) {
            // Check for uniqueness
            $formattedPhone = $phoneService->formatPhoneNumber($phone, $isoCountryCode);
            $exists = \App\Models\User::where('phone', $formattedPhone)->exists();
            
            if ($exists) {
                return response()->json([
                    'valid' => false,
                    'message' => 'This phone number is already registered'
                ]);
            }
            
            return response()->json([
                'valid' => true,
                'message' => 'Valid phone number'
            ]);
        } else {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid phone number format for the selected country'
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'valid' => false,
            'message' => 'Unable to validate phone number'
        ]);
    }
}); 