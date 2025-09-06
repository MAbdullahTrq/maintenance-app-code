<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Rules\TurnstileRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Mail\WelcomeMail;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $phoneService = new \App\Services\PhoneValidationService();
        $userCountry = 'US'; // Default to US (+1) instead of detecting location
        $countries = $phoneService->getCountries();
        
        return view('mobile.register', compact('userCountry', 'countries'));
    }

    public function register(Request $request)
    {
        \Log::info('=== MOBILE REGISTRATION CONTROLLER CALLED ===');
        \Log::info('Mobile registration attempt started', [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country_code' => $request->country_code,
            'method' => $request->method(),
            'url' => $request->url(),
            'all_data' => $request->all()
        ]);
        
        try {
            \Log::info('Starting validation...');
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => ['required', new \App\Rules\PhoneValidationRule],
                'password' => 'required|string|min:8|confirmed',
                'cf-turnstile-response' => 'required|string',
                'terms_accepted' => 'required|accepted'
            ]);
            \Log::info('✅ Validation passed');
            
            \Log::info('Starting Turnstile verification...');
            $turnstileResponse = $request->input('cf-turnstile-response');
            $secretKey = config('services.turnstile.secret_key');
            
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secretKey,
                'response' => $turnstileResponse,
                'remoteip' => $request->ip()
            ]);
            
            $result = $response->json();
            \Log::info('Turnstile response:', $result);
            
            if (!$result['success']) {
                \Log::error('❌ Turnstile verification failed');
                return back()->withErrors(['cf-turnstile-response' => 'Please complete the security check.'])->withInput();
            }
            \Log::info('✅ Turnstile verification passed');
            
            \Log::info('Starting user creation...');
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role_id' => 2, // Property Manager role
                'is_active' => false, // Account starts as inactive until email verification
            ]);
            \Log::info('✅ User created successfully', ['user_id' => $user->id]);
            
            // Start the free trial (but don't activate account yet)
            $user->update([
                'trial_started_at' => now(),
                'trial_expires_at' => now()->addDays(30),
            ]);
            
            \Log::info('✅ Trial started for user', ['user_id' => $user->id]);
            
            // Generate verification token
            $verificationToken = $user->generateVerificationToken();
            
            \Log::info('Sending verification email...');
            Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationToken));
            \Log::info('✅ Verification email sent');
            
            \Log::info('=== MOBILE REGISTRATION COMPLETED SUCCESSFULLY ===');
            return redirect()->route('verification.notice')
                ->with('success', 'Registration successful! Please check your email to verify your account.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ Validation failed', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('❌ Registration failed with exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['email' => 'Registration failed. Please try again.'])->withInput();
        }
    }
} 