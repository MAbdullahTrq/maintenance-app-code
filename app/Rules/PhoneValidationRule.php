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

        // Convert dialing code to ISO country code
        $isoCountryCode = $this->getIsoCountryCode($countryCode);
        
        if (!$isoCountryCode) {
            \Log::error('PhoneValidationRule invalid country code', [
                'country_code' => $countryCode
            ]);
            $fail('Invalid country code.');
            return;
        }

        // Validate phone number format
        try {
            // Construct the full phone number with country code
            $fullPhone = $countryCode . $value;
            
            \Log::info('PhoneValidationRule constructed phone', [
                'original_phone' => $value,
                'country_code' => $countryCode,
                'iso_country_code' => $isoCountryCode,
                'full_phone' => $fullPhone
            ]);
            
            // Try to create phone number with the full phone number and ISO country code
            $phoneNumber = new PhoneNumber($fullPhone, $isoCountryCode);
            
            if (!$phoneNumber->isValid()) {
                \Log::info('PhoneValidationRule first attempt failed, trying original value');
                
                // If that fails, try with just the phone number (in case it already includes country code)
                $phoneNumber = new PhoneNumber($value, $isoCountryCode);
                
                if (!$phoneNumber->isValid()) {
                    \Log::error('PhoneValidationRule both attempts failed', [
                        'phone' => $value,
                        'full_phone' => $fullPhone,
                        'country_code' => $countryCode,
                        'iso_country_code' => $isoCountryCode
                    ]);
                    $fail('The phone number format is invalid for the selected country.');
                    return;
                }
            }

            // Format the phone number to E164 format for consistency
            $formattedPhone = $phoneNumber->formatE164();
            
            \Log::info('PhoneValidationRule success', [
                'formatted_phone' => $formattedPhone
            ]);

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
                'iso_country_code' => $isoCountryCode,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $fail('The phone number format is invalid.');
        }
    }

    /**
     * Convert dialing code to ISO country code
     */
    private function getIsoCountryCode($dialingCode): ?string
    {
        $countryMap = [
            '+1' => 'US',
            '+44' => 'GB',
            '+61' => 'AU',
            '+49' => 'DE',
            '+33' => 'FR',
            '+39' => 'IT',
            '+34' => 'ES',
            '+31' => 'NL',
            '+32' => 'BE',
            '+41' => 'CH',
            '+43' => 'AT',
            '+46' => 'SE',
            '+47' => 'NO',
            '+45' => 'DK',
            '+358' => 'FI',
            '+353' => 'IE',
            '+351' => 'PT',
            '+30' => 'GR',
            '+48' => 'PL',
            '+420' => 'CZ',
            '+36' => 'HU',
            '+421' => 'SK',
            '+386' => 'SI',
            '+385' => 'HR',
            '+359' => 'BG',
            '+40' => 'RO',
            '+370' => 'LT',
            '+371' => 'LV',
            '+372' => 'EE',
            '+7' => 'RU',
            '+380' => 'UA',
            '+375' => 'BY',
            '+90' => 'TR',
            '+972' => 'IL',
            '+966' => 'SA',
            '+971' => 'AE',
            '+91' => 'IN',
            '+86' => 'CN',
            '+81' => 'JP',
            '+82' => 'KR',
            '+65' => 'SG',
            '+60' => 'MY',
            '+66' => 'TH',
            '+63' => 'PH',
            '+62' => 'ID',
            '+84' => 'VN',
            '+55' => 'BR',
            '+52' => 'MX',
            '+54' => 'AR',
            '+56' => 'CL',
            '+57' => 'CO',
            '+51' => 'PE',
            '+27' => 'ZA',
            '+20' => 'EG',
            '+234' => 'NG',
            '+254' => 'KE',
            '+233' => 'GH',
            '+212' => 'MA',
            '+216' => 'TN',
            '+213' => 'DZ',
            '+92' => 'PK',
            '+880' => 'BD',
            '+94' => 'LK',
            '+64' => 'NZ',
        ];

        return $countryMap[$dialingCode] ?? null;
    }
} 