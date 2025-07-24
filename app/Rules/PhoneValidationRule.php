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

        // Debug: Log the values being validated
        \Log::info('PhoneValidationRule validation', [
            'phone' => $value,
            'country_code' => $countryCode,
            'request_all' => request()->all()
        ]);

        // Validate phone number format
        try {
            // Try to create phone number directly with the provided value
            $phoneNumber = new PhoneNumber($value, $countryCode);
            
            if (!$phoneNumber->isValid()) {
                // If that fails, try cleaning the phone number
                $cleanPhone = $value;
                
                // Remove country code if it's included
                if (str_starts_with($cleanPhone, $countryCode)) {
                    $cleanPhone = substr($cleanPhone, strlen($countryCode));
                }
                
                if (str_starts_with($cleanPhone, '++' . $countryCode)) {
                    $cleanPhone = substr($cleanPhone, strlen('++' . $countryCode));
                }
                
                if (str_starts_with($cleanPhone, '+' . $countryCode)) {
                    $cleanPhone = substr($cleanPhone, strlen('+' . $countryCode));
                }
                
                // Try again with cleaned phone number
                $phoneNumber = new PhoneNumber($cleanPhone, $countryCode);
                
                if (!$phoneNumber->isValid()) {
                    \Log::error('PhoneValidationRule failed after cleaning', [
                        'original_phone' => $value,
                        'cleaned_phone' => $cleanPhone,
                        'country_code' => $countryCode
                    ]);
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
            \Log::error('PhoneValidationRule exception', [
                'phone' => $value,
                'country_code' => $countryCode,
                'exception' => $e->getMessage()
            ]);
            $fail('The phone number format is invalid.');
        }
    }
} 