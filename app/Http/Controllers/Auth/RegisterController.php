<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Mail\EmailVerificationMail;
use App\Rules\TurnstileRule;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        $phoneService = new \App\Services\PhoneValidationService();
        $userCountry = 'US'; // Default to US (+1) instead of detecting location
        $countries = $phoneService->getCountries();
        
        return view('auth.register', compact('userCountry', 'countries'));
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        \Log::info('=== REGISTRATION CONTROLLER CALLED ===');
        \Log::info('Registration attempt started', [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country_code' => $request->country_code,
            'method' => $request->method(),
            'url' => $request->url(),
            'all_data' => $request->all()
        ]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'country_code' => ['required', 'string', 'size:2'],
            'cf-turnstile-response' => ['required', new TurnstileRule],
        ]);

        \Log::info('Validation passed, proceeding with user creation');

        // Get the property manager role
        $role = Role::where('slug', 'property_manager')->first();
        
        if (!$role) {
            \Log::error('Property manager role not found');
            return back()->withErrors(['error' => 'System configuration error.'])->withInput();
        }

        // Simple phone number formatting - just concatenate country code and phone
        $formattedPhone = $request->country_code . $request->phone;

        \Log::info('Phone number formatted', ['formatted_phone' => $formattedPhone]);

        try {
            // Create user account as inactive by default
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $formattedPhone,
                'password' => Hash::make($request->password),
                'role_id' => $role->id,
                'is_active' => false, // Account starts as inactive
            ]);

            \Log::info('User created successfully', ['user_id' => $user->id]);

            // Generate verification token
            $verificationToken = $user->generateVerificationToken();

            // Send verification email
            Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationToken));

            \Log::info('Verification email sent successfully');

            // Redirect to verification notice instead of auto-login
            return redirect()->route('verification.notice')
                ->with('success', 'Registration successful! Please check your email to verify your account.');

        } catch (\Exception $e) {
            \Log::error('Error during user creation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'An error occurred during registration. Please try again.'])->withInput();
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