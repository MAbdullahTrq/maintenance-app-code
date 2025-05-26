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
            if (method_exists($user, 'isPropertyManager') && $user->isPropertyManager()) {
                return redirect()->intended(route('mobile.manager.dashboard'));
            } elseif (method_exists($user, 'isTechnician') && $user->isTechnician()) {
                return redirect()->intended(route('mobile.technician.dashboard'));
            } else {
                // fallback or admin
                return redirect('/');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }
} 