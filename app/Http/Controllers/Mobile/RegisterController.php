<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\WelcomeMail;
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
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign property manager role
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('property_manager');
        } elseif (property_exists($user, 'role_id')) {
            // If using a role_id column
            $user->role_id = /* property manager role id */ 2;
            $user->save();
        }

        Auth::login($user);

        // Send welcome email
        Mail::to($user->email)->send(new WelcomeMail($user));

        return redirect()->route('mobile.manager.dashboard');
    }
} 