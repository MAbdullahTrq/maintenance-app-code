@extends('layouts.mobile')

@section('title', 'Welcome')

@section('content')
<style>
    /* Additional styles to ensure icons work properly */
    .fa, .fas, .far, .fab {
        display: inline-block;
        font-style: normal;
        font-variant: normal;
        text-rendering: auto;
        line-height: 1;
    }
    .feature-icon {
        font-size: 24px;
        color: #2563eb;
        margin-bottom: 10px;
    }
</style>

<div style="padding: 15px; margin-bottom: 20px;">
    <div style="background-color: #2563eb; color: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; text-align: center;">
        <h1 style="font-size: 24px; font-weight: bold; margin-bottom: 10px;">
            @auth
                Welcome back, {{ Auth::user()->name }}!
            @else
                Welcome to MaintainXtra
            @endauth
        </h1>
        <p style="font-size: 16px; margin-bottom: 15px;">
            Simplify your maintenance workflow
        </p>
        
        <div style="margin-top: 20px;">
            @auth
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" style="display: block; background-color: white; color: #2563eb; padding: 12px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-bottom: 10px;">
                        📊 Go to Dashboard
                    </a>
                @elseif(Auth::user()->isPropertyManager())
                    @if(Auth::user()->hasActiveSubscription())
                        <a href="{{ route('mobile.manager.dashboard') }}" style="display: block; background-color: white; color: #2563eb; padding: 12px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-bottom: 10px;">
                            📊 Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('subscription.plans') }}" style="display: block; background-color: white; color: #2563eb; padding: 12px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-bottom: 10px;">
                            👑 View Subscription Plans
                        </a>
                    @endif
                @else
                    <a href="{{ route('mobile.technician.dashboard') }}" style="display: block; background-color: white; color: #2563eb; padding: 12px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-bottom: 10px;">
                        📊 Go to Dashboard
                    </a>
                @endif
            @else
                <div>
                    <a href="{{ route('login') }}" style="display: block; background-color: white; color: #2563eb; padding: 12px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-bottom: 10px;">
                        🔐 Login
                    </a>
                    <a href="{{ route('register') }}" style="display: block; background-color: #22c55e; color: white; padding: 12px; border-radius: 6px; text-decoration: none; font-weight: bold;">
                        ✨ Register
                    </a>
                </div>
            @endauth
        </div>
    </div>
    
    <h2 style="font-size: 22px; font-weight: bold; text-align: center; margin: 20px 0;">Key Features</h2>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;">
        <div style="background-color: white; padding: 15px; border-radius: 8px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="feature-icon">📱</div>
            <h3 style="font-weight: bold;">QR Code Access</h3>
        </div>
        
        <div style="background-color: white; padding: 15px; border-radius: 8px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="feature-icon">📋</div>
            <h3 style="font-weight: bold;">Work Orders</h3>
        </div>
        
        <div style="background-color: white; padding: 15px; border-radius: 8px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="feature-icon">🔔</div>
            <h3 style="font-weight: bold;">Notifications</h3>
        </div>
        
        <div style="background-color: white; padding: 15px; border-radius: 8px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="feature-icon">📷</div>
            <h3 style="font-weight: bold;">Photo Docs</h3>
        </div>
    </div>
    
    <div style="background-color: #f9fafb; border-radius: 8px; padding: 15px; margin-top: 20px;">
        <h3 style="font-size: 18px; font-weight: bold; text-align: center; margin-bottom: 15px;">How It Works</h3>
        
        <div style="display: flex; align-items: center; margin-bottom: 15px;">
            <div style="font-size: 24px; margin-right: 10px;">1️⃣</div>
            <div>
                <p style="font-weight: 500;">Submit a maintenance request</p>
            </div>
        </div>
        
        <div style="display: flex; align-items: center; margin-bottom: 15px;">
            <div style="font-size: 24px; margin-right: 10px;">2️⃣</div>
            <div>
                <p style="font-weight: 500;">Manager reviews & assigns</p>
            </div>
        </div>
        
        <div style="display: flex; align-items: center;">
            <div style="font-size: 24px; margin-right: 10px;">3️⃣</div>
            <div>
                <p style="font-weight: 500;">Technician completes work</p>
            </div>
        </div>
    </div>
</div>
@endsection 