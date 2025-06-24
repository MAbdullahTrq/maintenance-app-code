<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationMail;
use App\Models\User;
use Illuminate\Http\Request;
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
    public function verify(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Invalid verification link.');
        }

        if ($user->is_active) {
            return redirect()->route('login')
                ->with('message', 'Email already verified. You can now log in.');
        }

        if (!$user->isValidVerificationToken($request->token)) {
            return redirect()->route('verification.notice')
                ->with('error', 'Invalid or expired verification link. Please request a new verification email.');
        }

        // Activate the user account
        $user->update([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Clear the verification token
        $user->clearVerificationToken();

        return redirect()->route('login')
            ->with('success', 'Email verified successfully! You can now log in to your account.');
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