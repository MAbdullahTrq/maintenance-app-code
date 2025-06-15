<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules;
use App\Models\User;

class ResetPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function showResetForm(Request $request, $token = null)
    {
        // Check if this is a verification token (for new technicians)
        if ($request->has('email') && $token) {
            $user = User::where('email', $request->email)->first();
            if ($user && $user->isValidVerificationToken($token)) {
                return view('auth.passwords.reset')->with([
                    'token' => $token, 
                    'email' => $request->email,
                    'is_verification' => true,
                    'user_name' => $user->name
                ]);
            }
        }

        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email, 'is_verification' => false]
        );
    }

    /**
     * Handle an incoming password reset request.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Check if this is a verification token reset
        if ($request->has('is_verification') && $request->is_verification) {
            return $this->resetWithVerificationToken($request);
        }

        // Standard password reset flow
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }

    /**
     * Handle password reset with verification token (for new technicians).
     */
    protected function resetWithVerificationToken(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        if (!$user->isValidVerificationToken($request->token)) {
            return back()->withErrors(['token' => 'This verification link is invalid or has expired.']);
        }

        // Update password and clear verification token
        $user->update([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ]);

        $user->clearVerificationToken();

        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Your account has been verified and password set successfully! You can now log in.');
    }
} 