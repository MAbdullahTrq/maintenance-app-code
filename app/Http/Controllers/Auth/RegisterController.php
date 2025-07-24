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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', new \App\Rules\PhoneValidationRule()],
            'country_code' => ['required', 'string', 'size:2'],
            'cf-turnstile-response' => ['required', new TurnstileRule],
        ]);

        // Get the property manager role
        $role = Role::where('slug', 'property_manager')->first();

        // Format phone number to E164 format
        $phoneService = new \App\Services\PhoneValidationService();
        $formattedPhone = $phoneService->formatPhoneNumber($request->phone, $request->country_code);

        // Create user account as inactive by default
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $formattedPhone,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
            'is_active' => false, // Account starts as inactive
        ]);

        // Generate verification token
        $verificationToken = $user->generateVerificationToken();

        // Send verification email
        Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationToken));

        // Redirect to verification notice instead of auto-login
        return redirect()->route('verification.notice')
            ->with('success', 'Registration successful! Please check your email to verify your account.');
    }
} 