@extends('layouts.mobile')

@section('title', 'Login')

@section('content')
<div style="padding: 20px; max-width: 500px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('mobile.welcome') }}" style="display: inline-block; color: #2563eb; text-decoration: none; font-size: 16px;">
            <span style="display: inline-flex; align-items: center;">
                ← Back to Home
            </span>
        </a>
    </div>

    <h1 style="text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 20px;">Login to Your Account</h1>
    
    @if (session('status'))
        <div style="background: #e9f7ef; color: #27ae60; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div style="margin-bottom: 15px;">
            <label for="email" style="display: block; font-weight: 500; margin-bottom: 5px;">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">
            @error('email')
                <div style="color: #e74c3c; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>
        
        <div style="margin-bottom: 20px;">
            <label for="password" style="display: block; font-weight: 500; margin-bottom: 5px;">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">
            @error('password')
                <div style="color: #e74c3c; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center;">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} 
                       style="margin-right: 8px;">
                <span>Remember me</span>
            </label>
        </div>
        
        <button type="submit" 
                style="display: block; width: 100%; background: #2563eb; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; text-align: center; cursor: pointer;">
            Login
        </button>
        
        @if (Route::has('password.request'))
            <div style="text-align: center; margin-top: 15px;">
                <a href="{{ route('password.request') }}" style="color: #2563eb; text-decoration: none;">
                    Forgot your password?
                </a>
            </div>
        @endif
        
        <div style="text-align: center; margin-top: 20px;">
            Don't have an account? <a href="{{ route('mobile.register') }}" style="color: #2563eb; text-decoration: none;">Register now</a>
        </div>
    </form>
</div>
@endsection 