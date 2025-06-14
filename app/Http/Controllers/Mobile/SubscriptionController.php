<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function plans()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        $user = Auth::user();
        $activeSubscription = $user ? $user->subscriptions()->where('status', 'active')->where('ends_at', '>', now())->first() : null;
        return view('mobile.subscription.plans', compact('plans', 'activeSubscription'));
    }
} 