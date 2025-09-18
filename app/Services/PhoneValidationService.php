<?php

namespace App\Services;

use Stevebauman\Location\Facades\Location;
use Propaganistas\LaravelPhone\PhoneNumber;

class PhoneValidationService
{
    /**
     * Get user's country based on IP address
     */
    public function getUserCountry($ip = null): string
    {
        try {
            $position = Location::get($ip);
            
            if ($position && $position->countryCode) {
                return strtoupper($position->countryCode);
            }
        } catch (\Exception $e) {
            // Fallback to US if detection fails
        }
        
        return 'US'; // Default fallback
    }

    /**
     * Format phone number to E164 format
     */
    public function formatPhoneNumber($phone, $country): ?string
    {
        try {
            $phoneNumber = new PhoneNumber($phone, $country);
            return $phoneNumber->formatE164();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get list of countries with their calling codes
     */
    public function getCountries(): array
    {
        $countries = [
            'US' => ['name' => 'United States', 'code' => '+1'],
            'CA' => ['name' => 'Canada', 'code' => '+1'],
            'GB' => ['name' => 'United Kingdom', 'code' => '+44'],
            'AU' => ['name' => 'Australia', 'code' => '+61'],
            'DE' => ['name' => 'Germany', 'code' => '+49'],
            'FR' => ['name' => 'France', 'code' => '+33'],
            'IT' => ['name' => 'Italy', 'code' => '+39'],
            'ES' => ['name' => 'Spain', 'code' => '+34'],
            'NL' => ['name' => 'Netherlands', 'code' => '+31'],
            'BE' => ['name' => 'Belgium', 'code' => '+32'],
            'CH' => ['name' => 'Switzerland', 'code' => '+41'],
            'AT' => ['name' => 'Austria', 'code' => '+43'],
            'SE' => ['name' => 'Sweden', 'code' => '+46'],
            'NO' => ['name' => 'Norway', 'code' => '+47'],
            'DK' => ['name' => 'Denmark', 'code' => '+45'],
            'FI' => ['name' => 'Finland', 'code' => '+358'],
            'IE' => ['name' => 'Ireland', 'code' => '+353'],
            'PT' => ['name' => 'Portugal', 'code' => '+351'],
            'GR' => ['name' => 'Greece', 'code' => '+30'],
            'PL' => ['name' => 'Poland', 'code' => '+48'],
            'CZ' => ['name' => 'Czech Republic', 'code' => '+420'],
            'HU' => ['name' => 'Hungary', 'code' => '+36'],
            'SK' => ['name' => 'Slovakia', 'code' => '+421'],
            'SI' => ['name' => 'Slovenia', 'code' => '+386'],
            'HR' => ['name' => 'Croatia', 'code' => '+385'],
            'BG' => ['name' => 'Bulgaria', 'code' => '+359'],
            'RO' => ['name' => 'Romania', 'code' => '+40'],
            'LT' => ['name' => 'Lithuania', 'code' => '+370'],
            'LV' => ['name' => 'Latvia', 'code' => '+371'],
            'EE' => ['name' => 'Estonia', 'code' => '+372'],
            'RU' => ['name' => 'Russia', 'code' => '+7'],
            'UA' => ['name' => 'Ukraine', 'code' => '+380'],
            'BY' => ['name' => 'Belarus', 'code' => '+375'],
            'TR' => ['name' => 'Turkey', 'code' => '+90'],
            'IL' => ['name' => 'Israel', 'code' => '+972'],
            'SA' => ['name' => 'Saudi Arabia', 'code' => '+966'],
            'AE' => ['name' => 'United Arab Emirates', 'code' => '+971'],
            'IN' => ['name' => 'India', 'code' => '+91'],
            'CN' => ['name' => 'China', 'code' => '+86'],
            'JP' => ['name' => 'Japan', 'code' => '+81'],
            'KR' => ['name' => 'South Korea', 'code' => '+82'],
            'SG' => ['name' => 'Singapore', 'code' => '+65'],
            'MY' => ['name' => 'Malaysia', 'code' => '+60'],
            'TH' => ['name' => 'Thailand', 'code' => '+66'],
            'PH' => ['name' => 'Philippines', 'code' => '+63'],
            'ID' => ['name' => 'Indonesia', 'code' => '+62'],
            'VN' => ['name' => 'Vietnam', 'code' => '+84'],
            'BR' => ['name' => 'Brazil', 'code' => '+55'],
            'MX' => ['name' => 'Mexico', 'code' => '+52'],
            'AR' => ['name' => 'Argentina', 'code' => '+54'],
            'CL' => ['name' => 'Chile', 'code' => '+56'],
            'CO' => ['name' => 'Colombia', 'code' => '+57'],
            'PE' => ['name' => 'Peru', 'code' => '+51'],
            'ZA' => ['name' => 'South Africa', 'code' => '+27'],
            'EG' => ['name' => 'Egypt', 'code' => '+20'],
            'NG' => ['name' => 'Nigeria', 'code' => '+234'],
            'KE' => ['name' => 'Kenya', 'code' => '+254'],
            'GH' => ['name' => 'Ghana', 'code' => '+233'],
            'MA' => ['name' => 'Morocco', 'code' => '+212'],
            'TN' => ['name' => 'Tunisia', 'code' => '+216'],
            'DZ' => ['name' => 'Algeria', 'code' => '+213'],
            'PK' => ['name' => 'Pakistan', 'code' => '+92'],
            'BD' => ['name' => 'Bangladesh', 'code' => '+880'],
            'LK' => ['name' => 'Sri Lanka', 'code' => '+94'],
            'NZ' => ['name' => 'New Zealand', 'code' => '+64'],
        ];

        // Sort countries alphabetically by name
        uasort($countries, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return $countries;
    }

    /**
     * Validate phone number format for a specific country
     */
    public function isValidPhoneNumber($phone, $country): bool
    {
        try {
            $phoneNumber = new PhoneNumber($phone, $country);
            return $phoneNumber->isValid();
        } catch (\Exception $e) {
            return false;
        }
    }
} 