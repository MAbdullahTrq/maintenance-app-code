<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Propaganistas\LaravelPhone\PhoneNumber;
use App\Models\User;

class PhoneValidationRule implements ValidationRule
{
    protected $country;
    protected $userId;

    public function __construct($country = null, $userId = null)
    {
        $this->country = $country;
        $this->userId = $userId;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // Allow empty values if not required
        }

        // Get the country code from the request
        $countryCode = request()->input('country_code');
        
        if (empty($countryCode)) {
            $fail('Country code is required.');
            return;
        }

        // Validate phone number format
        try {
            // Construct the full phone number with country code
            $fullPhone = $countryCode . $value;
            
            // Try to create phone number with the full phone number
            $phoneNumber = new PhoneNumber($fullPhone, $countryCode);
            
            if (!$phoneNumber->isValid()) {
                // If that fails, try with just the phone number (in case it already includes country code)
                $phoneNumber = new PhoneNumber($value, $countryCode);
                
                if (!$phoneNumber->isValid()) {
                    $fail('The phone number format is invalid for the selected country.');
                    return;
                }
            }

            // Format the phone number to E164 format for consistency
            $formattedPhone = $phoneNumber->formatE164();

            // Check for uniqueness
            $query = User::where('phone', $formattedPhone);
            
            if ($this->userId) {
                $query->where('id', '!=', $this->userId);
            }

            if ($query->exists()) {
                $fail('This phone number is already registered.');
                return;
            }

        } catch (\Exception $e) {
            $fail('The phone number format is invalid.');
        }
    }
} 