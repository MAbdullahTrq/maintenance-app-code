<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('mobile.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
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
                    return back()->withErrors([
                        'email' => 'Your account has been deactivated. Please contact your manager or support.',
                    ]);
                }
            }
            
            // Redirect based on user role
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            } elseif (method_exists($user, 'isPropertyManager') && $user->isPropertyManager()) {
                return redirect()->intended(route('mobile.manager.dashboard'));
            } elseif (method_exists($user, 'isTechnician') && $user->isTechnician()) {
                return redirect()->intended(route('mobile.technician.dashboard'));
            } else {
                // fallback
                return redirect('/');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }
} 