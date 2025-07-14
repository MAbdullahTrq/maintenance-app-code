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

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('mobile.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'cf-turnstile-response' => ['required', new TurnstileRule],
        ]);

        // Get the property manager role
        $role = Role::where('slug', 'property_manager')->first();

        // Create user account as inactive by default
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
            'is_active' => false, // Account starts as inactive
        ]);

        // Generate verification token
        $verificationToken = $user->generateVerificationToken();

        // Send verification email instead of welcome email
        Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationToken));

        // Redirect to verification notice instead of dashboard
        return redirect()->route('verification.notice')
            ->with('success', 'Registration successful! Please check your email to verify your account.');
    }
} 