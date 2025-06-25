<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                
                // Check if it's a newly registered user who hasn't verified email
                if ($user->verification_token && $user->verification_token_expires_at && $user->verification_token_expires_at->isFuture()) {
                    return redirect()->route('verification.notice')
                        ->with('error', 'Please verify your email address before logging in. Check your inbox for the verification email.')
                        ->with('email', $user->email);
                } else {
                    throw ValidationException::withMessages([
                        'email' => ['Your account has been deactivated. Please contact your manager or support.'],
                    ]);
                }
            }

            // Redirect based on user role
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->isPropertyManager()) {
                // Check if property manager has an active subscription
                if (!$user->hasActiveSubscription()) {
                    return redirect()->route('mobile.subscription.plans');
                }
                
                return redirect()->intended(route('mobile.manager.dashboard'));
            } else {
                return redirect()->intended(route('mobile.technician.dashboard'));
            }
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        $user = Auth::user(); // Get user BEFORE logout
        \Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect all users to the main login page
        return redirect('/login');
    }
} 