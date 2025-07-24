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
    
    // Debug: Log the received data
    \Log::info('API Phone validation request', [
        'phone' => $phone,
        'country' => $country,
        'request_all' => $request->all()
    ]);
    
    if (empty($phone) || empty($country)) {
        return response()->json([
            'valid' => false,
            'message' => 'Phone number and country are required'
        ]);
    }
    
    try {
        $isValid = $phoneService->isValidPhoneNumber($phone, $country);
        
        // Debug: Log the validation result
        \Log::info('API Phone validation result', [
            'phone' => $phone,
            'country' => $country,
            'isValid' => $isValid
        ]);
        
        if ($isValid) {
            // Check for uniqueness
            $formattedPhone = $phoneService->formatPhoneNumber($phone, $country);
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
        \Log::error('API Phone validation exception', [
            'phone' => $phone,
            'country' => $country,
            'exception' => $e->getMessage()
        ]);
        return response()->json([
            'valid' => false,
            'message' => 'Unable to validate phone number'
        ]);
    }
}); 