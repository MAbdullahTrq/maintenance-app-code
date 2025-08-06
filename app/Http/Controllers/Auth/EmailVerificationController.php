<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    /**
     * Show the email verification notice.
     */
    public function notice()
    {
        return view('auth.verify-email');
    }

    /**
     * Handle email verification.
     */
    public function verify(Request $request, $token)
    {
        \Log::info('Email verification attempt', [
            'token' => $token,
            'email' => $request->email,
            'request_data' => $request->all()
        ]);

        // Validate email parameter
        if (!$request->has('email') || !filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            \Log::warning('Verification failed: Invalid email parameter', ['email' => $request->email]);
            return redirect()->route('verification.notice')
                ->with('error', 'Invalid verification link. Missing or invalid email parameter.');
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            \Log::warning('Verification failed: User not found', ['email' => $request->email]);
            return redirect()->route('verification.notice')
                ->with('error', 'Invalid verification link. User not found.');
        }

        \Log::info('User found for verification', [
            'user_id' => $user->id,
            'is_active' => $user->is_active,
            'stored_token' => $user->verification_token,
            'token_expires' => $user->verification_token_expires_at,
            'provided_token' => $token
        ]);

        if ($user->is_active) {
            \Log::info('User already active', ['user_id' => $user->id]);
            return redirect()->route('login')
                ->with('success', 'Email already verified. You can now log in.');
        }

        if (!$user->isValidVerificationToken($token)) {
            \Log::warning('Invalid verification token', [
                'user_id' => $user->id,
                'provided_token' => $token,
                'stored_token' => $user->verification_token,
                'token_expires' => $user->verification_token_expires_at,
                'is_future' => $user->verification_token_expires_at ? $user->verification_token_expires_at->isFuture() : null
            ]);
            return redirect()->route('verification.notice')
                ->with('error', 'Invalid or expired verification link. Please request a new verification email.')
                ->with('email', $user->email);
        }

        // Activate the user account and ensure trial is active
        $user->update([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Ensure trial is started if not already
        if (!$user->trial_started_at) {
            $user->update([
                'trial_started_at' => now(),
                'trial_expires_at' => now()->addDays(30),
            ]);
        }

        // Clear the verification token
        $user->clearVerificationToken();

        \Log::info('User verified successfully', ['user_id' => $user->id]);

        // Log the user in immediately after verification
        Auth::login($user);

        return redirect()->route('mobile.manager.dashboard')
            ->with('success', 'Email verified successfully! Welcome to MaintainXtra. Your 30-day free trial has started.');
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        if ($user->is_active) {
            return back()->with('message', 'Email already verified.');
        }

        // Generate new verification token
        $verificationToken = $user->generateVerificationToken();

        // Send verification email
        Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationToken));

        return back()->with('success', 'Verification email sent! Please check your inbox.');
    }

    /**
     * Show the resend verification email form.
     */
    public function showResendForm()
    {
        return view('auth.resend-verification');
    }
} 