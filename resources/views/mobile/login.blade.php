@extends('mobile.layout')

@section('title', 'Login')

@section('content')
<div class="min-h-screen bg-blue-50 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-center" style="padding-top: min(25vh, 120px)">
    <div class="w-full max-w-md space-y-8">
        <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
            <div class="mb-6 text-center">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Sign In</h1>
                <p class="mt-2 text-sm text-gray-600">Welcome back to MaintainXtra</p>
            </div>
            <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input type="email" name="email" id="email" class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Email address" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input type="password" name="password" id="password" class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Password" required>
                </div>
                <div class="mb-2 text-right">
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800">Forgot your password?</a>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 text-sm text-gray-700">Remember me</label>
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold text-sm transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Sign In
                </button>
            </form>
            <div class="mt-6 space-y-3 text-center">
                <a href="/" class="block text-blue-600 hover:text-blue-800 text-sm font-medium transition duration-200">&larr; Back to homepage</a>
                <div class="text-sm text-gray-600">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-medium transition duration-200">Sign up</a>
                </div>
                <div class="text-sm text-gray-600">
                    Need to verify your email?
                    <a href="{{ route('verification.resend.form') }}" class="text-blue-600 hover:text-blue-800 font-medium transition duration-200">Resend verification</a>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection 