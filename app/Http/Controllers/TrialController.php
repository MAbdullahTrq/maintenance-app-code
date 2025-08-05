<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrialController extends Controller
{
    /**
     * Show the trial expired page.
     */
    public function showExpired()
    {
        $user = Auth::user();
        
        if (!$user || $user->canAccessSystem()) {
            return redirect()->route('dashboard');
        }

        return view('trial.expired', compact('user'));
    }

    /**
     * Show the trial status page.
     */
    public function showStatus()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        return view('trial.status', compact('user'));
    }

    /**
     * Extend trial by 7 days (one-time only).
     */
    public function extendTrial(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        try {
            $user->extendTrial();
            return redirect()->back()->with('success', 'Your trial has been extended by 7 days!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reactivate account (for locked accounts).
     */
    public function reactivate(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->isAccountLocked()) {
            return redirect()->route('dashboard');
        }

        // Unlock account and start a new trial
        $user->unlockAccount();
        $user->startTrial();

        return redirect()->route('dashboard')->with('success', 'Your account has been reactivated with a new 30-day trial!');
    }
}
