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
                
                throw ValidationException::withMessages([
                    'email' => ['Your account has been deactivated. Please contact the administrator.'],
                ]);
            }

            // Redirect based on user role
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->isPropertyManager()) {
                // Check if property manager has an active subscription
                if (!$user->hasActiveSubscription()) {
                    return redirect()->route('subscription.plans');
                }
                
                return redirect()->intended(route('manager.dashboard'));
            } else {
                return redirect()->intended(route('technician.dashboard'));
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

        // Determine if the request is from mobile or web
        $previous = url()->previous();
        $isMobile = str_contains($previous, '/m/') || str_contains($previous, '/mobile') || str_contains($request->path(), '/m/') || str_contains($request->path(), '/mobile');

        if ($isMobile) {
            return redirect('/m/login');
        }
        return redirect('/login'); // Default web login
    }
} 