@extends('layouts.mobile')

@section('title', 'Register')

@section('content')
<div style="padding: 20px; max-width: 500px; margin: 0 auto;">
    <h1 style="text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 20px;">Create an Account</h1>
    
    <form method="POST" action="{{ route('register') }}">
        @csrf
        
        <div style="margin-bottom: 15px;">
            <label for="name" style="display: block; font-weight: 500; margin-bottom: 5px;">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">
            @error('name')
                <div style="color: #e74c3c; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="email" style="display: block; font-weight: 500; margin-bottom: 5px;">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">
            @error('email')
                <div style="color: #e74c3c; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="password" style="display: block; font-weight: 500; margin-bottom: 5px;">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">
            @error('password')
                <div style="color: #e74c3c; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>
        
        <div style="margin-bottom: 20px;">
            <label for="password-confirm" style="display: block; font-weight: 500; margin-bottom: 5px;">Confirm Password</label>
            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">
        </div>
        
        <button type="submit" 
                style="display: block; width: 100%; background: #22c55e; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; text-align: center; cursor: pointer;">
            Register
        </button>
        
        <div style="text-align: center; margin-top: 20px;">
            Already have an account? <a href="{{ route('mobile.login') }}" style="color: #2563eb; text-decoration: none;">Login</a>
        </div>
    </form>
</div>
@endsection 