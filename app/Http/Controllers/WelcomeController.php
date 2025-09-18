<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        // Get active subscription plans (monthly plans only for homepage)
        $plans = SubscriptionPlan::where('is_active', true)
            ->where('duration_in_days', 30)
            ->orderBy('price')
            ->get();

        return view('welcome', compact('plans'));
    }
}
